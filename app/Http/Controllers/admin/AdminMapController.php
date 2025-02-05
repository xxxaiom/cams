<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\NewReport;
use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminMapController extends Controller
{

    public function index()
    {
        return view('content.admin.reports.admin-map');
    }

    public function newReports()
    {
        $reports = NewReport::select(
            'user_reports.*',
            'user_reports.id as id',
            DB::raw("DATE_FORMAT(user_reports.created_at, '%M %d, %Y') as reported_date"),
            DB::raw("DATE_FORMAT(user_reports.created_at, '%h:%i %p') as reported_on"),
            DB::raw("CONCAT(ci.first_name, ' ', ci.middle_name, ' ', ci.last_name) AS reportedBy"),
            'ct.name as crime_name'
        )
            ->join('crime_types as ct', 'ct.id', '=', 'user_reports.crime_types_id')
            ->join('users as u', 'u.id', '=', 'user_reports.user_id')
            ->join('citizen_info as ci', 'ci.account_id', '=', 'u.id')
            ->where('user_reports.status', 'pending')
            ->get();

        return response()->json($reports);
    }

    public function reportReceived(Request $request)
    {
        try {
            $validated = $request->validate([
                'lat' => 'required|numeric|between:-90,90',
                'long' => 'required|numeric|between:-180,180',
            ]);

            $id = $request->id;
            $user = auth()->user();

            $existingLocation = DB::table('police_location')
                ->where('lat', $validated['lat'])
                ->where('long', $validated['long'])
                ->first();

            if (!$existingLocation) {
                DB::table('police_location')->insert([
                    'police_id' => $user->id,
                    'report_id' => $id,
                    'lat' => $validated['lat'],
                    'long' => $validated['long'],
                    'accuracy' => 20,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'code' => 0,
                    'message' => 'Report received and location updated'
                ]);
            } else {
                return response()->json([
                    'code' => 1,
                    'message' => 'Same coordinates'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'code' => 2,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function fetchBoundaries()
    {
        $boundaries = DB::table('barangay_boundaries')->select('coordinates', 'barangay_name', 'latitude', 'longitude')->get();

        $formattedBoundaries = $boundaries->map(function ($boundary) {

            preg_match_all('/(\d+\.\d+)\s+(\d+\.\d+)/', $boundary->coordinates, $matches);

            $coordsArray = isset($matches[0]) ? $matches[0] : [];

            $latLngArray = array_map(function ($coord) {
                $latLng = explode(' ', $coord);
                return [(float) $latLng[1], (float) $latLng[0]];
            }, $coordsArray);

            return [
                'coordinates' => $latLngArray,
                'barangay_name' => $boundary->barangay_name,
                'latitude' => $boundary->latitude,
                'longitude' => $boundary->longitude
            ];
        });

        return response()->json([
            'boundaries' => $formattedBoundaries,
            'center' => $boundaries
        ]);
    }

    public function checkExistingReport(Request $request)
    {

        $checkRecords = DB::table('police_location')
            ->where('police_location.report_id', $request->id)
            ->where('police_location.police_id', '!=', auth()->user()->id)
            ->first();

        if ($checkRecords) {
            return response()->json([
                'code' => 'reportAlreadyHandled',
                'message' => 'Report is already being handled by another officer.'
            ]);
        }

        $activeReport = DB::table('admin_info')
            ->where('account_id', auth()->user()->id)
            ->where('is_active', 'true')
            ->first();

        if ($activeReport) {
            return response()->json([
                'code' => 'userHasReport',
                'message' => 'You have an ongoing report'
            ]);
        }

        $report = DB::table('user_reports')
            ->where('id', $request->id)
            ->first();

        if (!$report) {
            return response()->json([
                'code' => 'noReport',
                'message' => 'Report not found'
            ]);
        }

        DB::table('user_reports')->where('id', $request->id)->update(['status' => 'responding']);

        DB::table('admin_info')->where('account_id', auth()->user()->id)->update(['is_active' => 'true']);

        return response()->json([
            'code' => 'reportActivated',
            'message' => 'Your report is now active and responding.'
        ]);
    }

    public function finishReport()
    {
        $user = auth()->user();

        DB::table('admin_info')
            ->where('account_id', $user->id)
            ->where(['is_active' => 'false'])
            ->first();

        return response()->json(['message' => 'Report marked as finished.']);
    }

    public function getLocation(Request $request)
    {
        try {
            $id = $request->id;

            $policeCoords = DB::table('police_location')
                ->select(
                    'police_location.id',
                    'police_location.lat as police_lat',
                    'police_location.long as police_long',
                    'police_location.report_id',
                    'police_location.created_at'
                )
                ->where('police_location.report_id', $id)
                ->orderBy('police_location.created_at', 'desc')
                ->get();


            if ($policeCoords->isNotEmpty()) {
                $reportCoords = DB::table('user_reports')
                    ->select(
                        'user_reports.id',
                        'user_reports.latitude as report_lat',
                        'user_reports.longitude as report_long'
                    )
                    ->where('user_reports.id', '=', $policeCoords[0]->report_id)
                    ->first();
            }

            if ($policeCoords && $reportCoords) {
                return response()->json([
                    'code' => 0,
                    'policeCoords' => $policeCoords,
                    'reportCoords' => $reportCoords,
                ]);
            } else {
                return response()->json([
                    'code' => 1,
                    'message' => 'No data found'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'code' => 3,
                'message' => $e->getMessage()
            ]);
        }
    }
}
