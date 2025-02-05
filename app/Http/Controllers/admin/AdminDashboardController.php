<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Accidents;
use App\Models\NewReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $reports = NewReport::select('*')->get();
        $accidents = DB::table('datasets')->select('*')->where('datasets.offense', 'LIKE', '%RECKLESS%')->get();
        $incidents = DB::table('datasets')->select('*')->get();
        return view('content.admin.dashboard.admin-dashboard', compact('reports', 'accidents', 'incidents'));
    }

    public function fetchData()
    {
        // Fetch total reports for each day of the week
        $accidents = DB::table('datasets')->selectRaw('DAYOFWEEK(dateTimeReported) as day_of_week, COUNT(*) as count')
            ->whereNotNull('dateTimeReported')
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();

        $data = [
            'days' => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            'counts' => array_fill(0, 7, 0),
        ];

        foreach ($accidents as $accident) {
            $data['counts'][$accident->day_of_week - 1] = $accident->count;
        }
        // Fetch total reports for each month    
        $monthlyReports = DB::table('datasets')->selectRaw('MONTH(dateTimeReported) as month, COUNT(*) as count')
            ->whereNotNull('dateTimeReported')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyData = array_fill(0, 12, 0);
        foreach ($monthlyReports as $report) {
            $monthlyData[$report->month - 1] = $report->count;
        }

        // Fetch total reports for each year
        $yearlyReports = DB::table('datasets')->selectRaw('YEAR(dateTimeReported) as year, COUNT(*) as count')
            ->whereNotNull('dateTimeReported')
            ->groupBy('year')->orderBy('year')
            ->get();

        $yearlyData = [];
        foreach ($yearlyReports as $report) {
            $yearlyData[$report->year] = $report->count;
        }

        $offenses = Accidents::selectRaw('offense, COUNT(*) as count')
            ->groupBy('offense')
            ->orderBy('count', 'desc')
            ->get();

        // For Pie Chart
        $seconddata = [];
        foreach ($offenses as $offense) {
            $seconddata[] = [
                'value' => $offense->count,
                'name' => $offense->offense,
            ];
        }

        return response()->json([
            'accidentData' => $data,
            'monthlyReports' => $monthlyData,
            'yearlyReports' => $yearlyData,
            'offenseData' => $seconddata,
        ]);
    }

    public function fetchMapBoundaries()
    {
        $boundaries = DB::table('barangay_boundaries')
            ->select('coordinates', 'barangay_name', 'latitude', 'longitude')
            ->get();

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

    public function fetchHeatMap(Request $request)
    {

        $year = $request->year;

        if ($year === null) {
            $brgyHeatMap = DB::table('datasets')
                ->select('barangay_boundaries.barangay_name', 'datasets.lat', 'datasets.lng')
                ->join('barangay_boundaries', 'barangay_boundaries.id', '=', 'datasets.barangay_id')
                ->get();
        } else {
            $brgyHeatMap = DB::table('datasets')
                ->select('barangay_boundaries.barangay_name', 'datasets.lat', 'datasets.lng')
                ->join('barangay_boundaries', 'barangay_boundaries.id', '=', 'datasets.barangay_id')
                ->whereRaw('YEAR(datasets.dateTimeReported) = ?', [$year])
                ->get();
        }


        return response()->json([
            'brgyHeatMap' => $brgyHeatMap,
            'year' => $year
        ]);
    }

    public function addColor(Request $request)
    {

        $selectedYear = $request->selectedYear;

        if ($selectedYear === null) {
            $query = DB::table('barangay_boundaries')
                ->select(
                    'barangay_boundaries.barangay_name',
                    DB::raw('count(datasets.id) as accident_count')
                )
                ->join('datasets', 'datasets.barangay_id', '=', 'barangay_boundaries.id')
                ->groupBy('barangay_boundaries.barangay_name')
                ->get();
        } else {
            $query = DB::table('barangay_boundaries')
                ->select(
                    'barangay_boundaries.barangay_name',
                    DB::raw('count(datasets.id) as accident_count')
                )
                ->join('datasets', 'datasets.barangay_id', '=', 'barangay_boundaries.id')
                ->whereRaw('YEAR(datasets.dateTimeReported) = ?', [$selectedYear])
                ->groupBy('barangay_boundaries.barangay_name')
                ->get();
        }

        $boundaries = DB::table('barangay_boundaries')
            ->select('coordinates', 'barangay_name', 'latitude', 'longitude')
            ->get();

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
            'selectedYear' => $selectedYear,
            'mapColors' => $query,
            'boundaries' => $formattedBoundaries,
            'center' => $boundaries
        ]);
    }
}
