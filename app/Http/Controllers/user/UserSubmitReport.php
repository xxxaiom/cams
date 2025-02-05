<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\CrimeType;
use App\Models\NewReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserSubmitReport extends Controller
{
    public function index()
    {
        $types = CrimeType::select('*')->get();
        $crimes = CrimeType::select('crime_table.*', 'crime_types.*')
            ->join('crime_types', 'crime_types.crime_id', '=', 'crime_table.id')->orderby('crime_types.name')->get();

        $reports = NewReport::where('user_id', Auth::user()->id)->get();

        $reports = $reports->map(function ($item) {
            $item->encrypted_id = Crypt::encrypt($item->id);
            return $item;
        });

        return view('content.user.user-submit-report', compact('crimes', 'reports'));
    }

    public function submitReport(Request $request)
    {
        try {

            $barangays = DB::table('barangay_boundaries')
                ->pluck('barangay_name')
                ->toArray();

            $request->validate([
                'crime_location' => ['required', Rule::in($barangays)],
                'crime' => ['required'],
                'crime_description' => ['required'],
                // 'share_location' => ['required'],
            ]);

            $id = CrimeType::join('crime_types', 'crime_types.crime_id', '=', 'crime_table.id')
                ->where('crime_types.name', '=', $request->crime)->value('crime_types.id');

            $user_id = Auth::user()->id;
            $status = 'pending';

            $report = [
                'crime_types_id' => $id,
                'user_id' => $user_id,
                'crime_location' => $request->crime_location,
                'crime_description' => $request->crime_description,
                'latitude' => $request->reportLat,
                'longitude' => $request->reportLong,
                'accuracy' => 20,
                'status' => $status,
            ];

            if ($report) {
                $id = NewReport::insertGetID($report);
                $newReport = NewReport::find($id);

                return response()->json([
                    'code' => 0,
                    'newReport' => $newReport,
                    'created_at' => Carbon::parse($newReport->created_at)->format('F d, Y h:i A'),
                ]);
            } else {
                return response()->json([
                    'code' => 1,
                    'message' => 'Invalid Input'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'code' => 2,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getlatestReport(Request $request)
    {
        $checkNewStatus = DB::table('user_reports')
            ->where('user_id', auth()->user()->id)
            ->get();

        return response()->json([
            'data' => $checkNewStatus,
        ]);
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
            'boundaries' => $formattedBoundaries
        ]);
    }

    public function getBarangay(Request $request)
    {
        $lat = $request->lat;
        $long = $request->lng;

        $point = "POINT($long $lat)";

        $barangay = DB::table('barangay_boundaries')
            ->select('barangay_name')
            ->whereRaw('ST_Contains(new_coordinates, ST_GeomFromText(?))', [$point])
            ->first();

        return response()->json([
            'barangay' => $barangay ? $barangay->barangay_name : null,
        ]);
    }
}
