<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\NewReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminReportsController extends Controller
{
    public function index()
    {
        $newReports = NewReport::select(
            'user_reports.crime_location as crime_location',
            'user_reports.id as reports_id',
            'ct.name as crime_name',
            'user_reports.crime_description as crime_description',
            'user_reports.status as status',
            DB::raw("CONCAT(ci.first_name, ' ', ci.middle_name, ' ', ci.last_name) AS reportedBy")
        )
            ->join('crime_types as ct', 'ct.id', '=', 'user_reports.crime_types_id')
            ->join('users as u', 'u.id', '=', 'user_reports.user_id')
            ->join('citizen_info as ci', 'ci.account_id', '=', 'u.id')
            ->where('status', '=', 'responding')
            ->orderBy('user_reports.created_at', 'DESC')
            ->get();

        $viewFullReports = DB::table('incident_report')->select(
            'incident_report.incident_name',
            'incident_report.location_description',
            'incident_report.id as incident_id',
            DB::raw("DATE_FORMAT(user_reports.created_at, '%M %d, %Y %h:%i %p') as reportedOn"),
            DB::raw("CONCAT(citizen_info.first_name, ' ', citizen_info.middle_name, ' ', citizen_info.last_name) AS reportedBy")
        )
            ->join('user_reports', 'user_reports.id', '=', 'incident_report.report_id')
            ->join('users', 'users.id', '=', 'user_reports.user_id')
            ->join('citizen_info', 'citizen_info.account_id', '=', 'user_reports.user_id')
            ->where('user_reports.created_at', '>=', Carbon::now()->startOfDay())
            ->where('user_reports.created_at', '<=', Carbon::now()->endOfDay())
            ->orderBy('user_reports.created_at', 'DESC')
            ->get();

        foreach ($newReports as $report) {
            $report->reports_id = Crypt::encrypt($report->reports_id);
        }


        return view('content.admin.reports.admin-reports', compact('newReports', 'viewFullReports'));
    }

    public function fetchModalData(Request $request)
    {

        $decryptedID = Crypt::decrypt($request->id);

        $reports = DB::table('user_reports')
            ->select('user_reports.id as id', 'user_reports.latitude as latitude', 'user_reports.longitude as longitude', 'user_reports.accuracy as accuracy', 'user_reports.crime_description as crime_description', 'user_reports.crime_location as crime_location', 'user_reports.status as status', DB::raw("CONCAT(ci.first_name, ' ', ci.middle_name, ' ', ci.last_name) AS reportedBy"))
            ->join('crime_types as ct', 'ct.id', '=', 'user_reports.crime_types_id')
            ->join('users as u', 'u.id', '=', 'user_reports.user_id')
            ->join('citizen_info as ci', 'ci.account_id', '=', 'u.id')
            ->where('user_reports.id', '=', $decryptedID)
            ->first();

        $date_reported = NewReport::select('created_at')
            ->where('id', '=', $decryptedID)
            ->first();
        $formattedDate = Carbon::parse($date_reported->created_at)->format('F d, Y h:i A');

        return response()->json([
            'reports' => $reports,
            'date_reported' => $formattedDate,
        ]);
    }

    public function incidentReport(Request $request)
    {
        try {

            $barangays = DB::table('barangay_boundaries')->pluck('barangay_name')->toArray();

            $request->validate([
                'incident_name' => ['required'],
                'incident_description' => ['required'],
                'location_description' => ['required', Rule::in($barangays)],
                'dateTime_committed' => ['required', 'before:today'],
                'lat' => ['required'],
                'long' => ['required'],
            ], [
                'location_description.in' => 'The barangay is not part of Sogod'
            ]);


            $location = DB::table('barangay_boundaries')->select('id')->where('barangay_name', '=', $request->location_description)->first();

            $location_id = $location->id;

            $reportID = Crypt::decrypt($request->report_id);

            $data = [
                'report_id' => $reportID,
                'location_id' => $location_id,
                'incident_name' => $request->incident_name,
                'incident_description' => $request->incident_description,
                'location_description' => $request->location_description,
                'date_time_committed' => $request->dateTime_committed,
                'latitude' => $request->lat,
                'longitude' => $request->long
            ];

            if ($data) {
                DB::table('user_reports')
                    ->where('user_reports.id', '=', $reportID)
                    ->update(['status' => 'responded']);
                DB::table('incident_report')->insert($data);

                return response()->json([
                    'code' => 0,
                ]);
            } else {
                return response()->json([
                    'code' => 1,
                    'message' => 'Unable to insert data'
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'code' => 2,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function searchReports(Request $request)
    {
        $searchReport = DB::table('user_reports')
            ->select(
                DB::raw("CONCAT(ci.first_name, ' ', ci.middle_name, ' ', ci.last_name) AS reportedBy"),
                'incident_report.incident_name AS incident_name',
                'incident_report.location_description AS incident_location',
                DB::raw("DATE_FORMAT(incident_report.date_time_committed, '%M %d, %Y %h:%i %p') as reported_on")
            )
            ->join('users as u', 'u.id', '=', 'user_reports.user_id')
            ->join('citizen_info as ci', 'ci.account_id', '=', 'u.id')
            ->join('incident_report', 'incident_report.report_id', '=', 'user_reports.id')
            ->where(DB::raw("CONCAT(ci.first_name, ' ', ci.middle_name, ' ', ci.last_name)"), 'LIKE', '%' . $request->input . '%')
            ->get();

        return response()->json([
            'reports' => $searchReport,
        ]);
    }

    public function fetchTimeFrame()
    {
        $today = DB::table('incident_report')->select(
            'incident_report.incident_name',
            'incident_report.location_description',
            DB::raw("DATE_FORMAT(user_reports.created_at, '%M %d, %Y %h:%i %p') as reported_on"),
            DB::raw("CONCAT(citizen_info.first_name, ' ', citizen_info.middle_name, ' ', citizen_info.last_name) AS reportedBy")
        )
            ->join('user_reports', 'user_reports.id', '=', 'incident_report.report_id')
            ->join('users', 'users.id', '=', 'user_reports.user_id')
            ->join('citizen_info', 'citizen_info.account_id', '=', 'users.id')
            ->where('user_reports.created_at', '>=', Carbon::now()->startOfDay())
            ->where('user_reports.created_at', '<=', Carbon::now()->endOfDay())
            ->get();

        $week = DB::table('incident_report')->select(
            'incident_report.incident_name',
            'incident_report.location_description',
            DB::raw("DATE_FORMAT(user_reports.created_at, '%M %d, %Y %h:%i %p') as reported_on"),
            DB::raw("CONCAT(citizen_info.first_name, ' ', citizen_info.middle_name, ' ', citizen_info.last_name) AS reportedBy")
        )
            ->join('user_reports', 'user_reports.id', '=', 'incident_report.report_id')
            ->join('users', 'users.id', '=', 'user_reports.user_id')
            ->join('citizen_info', 'citizen_info.account_id', '=', 'users.id')
            ->where('user_reports.created_at', '>=', Carbon::now()->subDays(7)->startOfDay())
            ->where('user_reports.created_at', '<=', Carbon::now()->endOfDay())
            ->get();

        $month = DB::table('incident_report')->select(
            'incident_report.incident_name',
            'incident_report.location_description',
            DB::raw("DATE_FORMAT(user_reports.created_at, '%M %d, %Y %h:%i %p') as reported_on"),
            DB::raw("CONCAT(citizen_info.first_name, ' ', citizen_info.middle_name, ' ', citizen_info.last_name) AS reportedBy")
        )
            ->join('user_reports', 'user_reports.id', '=', 'incident_report.report_id')
            ->join('users', 'users.id', '=', 'user_reports.user_id')
            ->join('citizen_info', 'citizen_info.account_id', '=', 'users.id')
            ->where('user_reports.created_at', '>=', Carbon::now()->subDays(30)->startOfDay())
            ->where('user_reports.created_at', '<=', Carbon::now()->endOfDay())
            ->get();

        return response()->json([
            'reportsToday' => $today,
            'reportsWeek' => $week,
            'reportsMonth' => $month,
        ]);
    }

    public function fetchNewReports()
    {
        $newReports = NewReport::select(
            'user_reports.id as reports_id',
            'ct.name as crime_name',
            'user_reports.crime_description as crime_description',
            'user_reports.status as status',
            DB::raw("CONCAT(ci.first_name, ' ', ci.middle_name, ' ', ci.last_name) AS reportedBy")
        )
            ->join('crime_types as ct', 'ct.id', '=', 'user_reports.crime_types_id')
            ->join('users as u', 'u.id', '=', 'user_reports.user_id')
            ->join('citizen_info as ci', 'ci.account_id', '=', 'u.id')
            ->where('status', '=', 'responding')
            ->get();

        return response()->json([
            'newReports' => $newReports
        ]);
    }

    public function fetchFullReports()
    {
        $fullReports = DB::table('incident_report')->select(
            'incident_report.*',
            'incident_report.id as id',
            DB::raw("DATE_FORMAT(user_reports.created_at, '%M %d, %Y %h:%i %p') as reported_on"),
            DB::raw("CONCAT(citizen_info.first_name, ' ', citizen_info.middle_name, ' ', citizen_info.last_name) AS reportedBy")
        )
            ->join('user_reports', 'user_reports.id', '=', 'incident_report.report_id')
            ->join('users', 'users.id', '=', 'user_reports.user_id')
            ->join('citizen_info', 'citizen_info.account_id', '=', 'user_reports.user_id')
            ->where('user_reports.created_at', '>=', Carbon::now()->startOfDay())
            ->where('user_reports.created_at', '<=', Carbon::now()->endOfDay())
            ->get();

        return response()->json([
            'fullReports' => $fullReports
        ]);
    }

    public function fetchMapBoundaries()
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
