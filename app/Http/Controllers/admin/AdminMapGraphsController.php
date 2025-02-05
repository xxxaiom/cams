<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMapGraphsController extends Controller
{
    public function index()
    {
        $sqlQuery = DB::table('user_reports')->select('*')->whereDate('created_at', Carbon::today())->get();
        $count = $sqlQuery->count();

        $allData = DB::table('datasets')
            ->selectRaw(
                'barangay_boundaries.barangay_name AS barangay_name,
            count(datasets.id) as totalReports,
            sum(CASE WHEN datasets.offense LIKE "%RECKLESS%" THEN 1 ELSE 0 END) as accidentReports,
            sum(CASE WHEN datasets.offense NOT LIKE "%RECKLESS%" THEN 1 ELSE 0 END) as crimeReports',
            )
            ->rightJoin('barangay_boundaries', 'barangay_boundaries.id', '=', 'datasets.barangay_id')
            ->groupBy('barangay_name')
            ->orderBy('totalReports', 'DESC')
            ->limit(10)
            ->get();

        return view('content.admin.dashboard.admin-maps', compact('count', 'allData'));
    }

    public function changeReportValue(Request $request)
    {
        $value = $request->value;

        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::now();

        $count = 0;
        $sort = '';
        $recklessPercentage = 0;
        $nonRecklessPercentage = 0;

        switch ($value) {
            case 'today':
                $count = DB::table('user_reports')->whereDate('created_at', $today)->count();
                $sort = 'today';
                break;
            case 'month':
                $count = DB::table('user_reports')
                    ->whereBetween('created_at', [$startOfMonth, $today])
                    ->count();
                $sort = 'this month';
                break;
            case 'all':
                $count = DB::table('user_reports')->count();
                $sort = 'all time';

                $total_count = DB::table('datasets')->count();

                $reckless = DB::table('datasets')->where('offense', 'LIKE', '%RECKLESS%')->count();

                $nonReckless = $total_count - $reckless;

                $recklessPercentage = $total_count > 0 ? ($reckless / $total_count) * 100 : 0;
                $nonRecklessPercentage = $total_count > 0 ? ($nonReckless / $total_count) * 100 : 0;
                break;
        }

        return response()->json([
            'value' => $count,
            'sort' => $sort,
            'recklessPercentage' => round($recklessPercentage),
            'nonRecklessPercentage' => round($nonRecklessPercentage),
        ]);
    }

    public function getAllData(Request $request)
    {
        $chartValue = $request->chartData;

        switch ($chartValue) {
            case '':
                $allData = DB::table('datasets')->select(DB::raw('YEAR(dateTimeReported) as year'), DB::raw('COUNT(id) as numOfReports'))->groupBy(DB::raw('year'))->orderBy(DB::raw('year'))->get();
                break;
            case 'All':
                $allData = DB::table('datasets')->select(DB::raw('YEAR(dateTimeReported) as year'), DB::raw('COUNT(id) as numOfReports'))->groupBy(DB::raw('year'))->orderBy(DB::raw('year'))->get();
                break;
            case 'Crime':
                $allData = DB::table('datasets')->select(DB::raw('YEAR(dateTimeReported) as year'), DB::raw('COUNT(id) as numOfReports'))->where('offense', 'NOT LIKE', '%RECKLESS%')->groupBy(DB::raw('year'))->orderBy(DB::raw('year'))->get();
                break;
            case 'Accident':
                $allData = DB::table('datasets')->select(DB::raw('YEAR(dateTimeReported) as year'), DB::raw('COUNT(id) as numOfReports'))->where('offense', 'LIKE', '%RECKLESS%')->groupBy(DB::raw('year'))->orderBy(DB::raw('year'))->get();
                break;
        }

        return response()->json([
            'allData' => $allData,
        ]);
    }

    public function getDonutData(Request $request)
    {
        $select = $request->data;
        $message = 'Invalid selection';

        // Apply the CAST function in selectRaw() to ensure the result is an integer
        if ($select === 'donutAll') {
            $data = DB::table('datasets')
                ->selectRaw(
                    '
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 1 AND HOUR(dateTimeReported) < 4 THEN 1 ELSE 0 END) AS SIGNED) AS "1AM_to_4AM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 4 AND HOUR(dateTimeReported) < 8 THEN 1 ELSE 0 END) AS SIGNED) AS "4AM_to_8AM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 8 AND HOUR(dateTimeReported) < 12 THEN 1 ELSE 0 END) AS SIGNED) AS "8AM_to_12PM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 12 AND HOUR(dateTimeReported) < 16 THEN 1 ELSE 0 END) AS SIGNED) AS "12PM_to_4PM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 16 AND HOUR(dateTimeReported) < 20 THEN 1 ELSE 0 END) AS SIGNED) AS "4PM_to_8PM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 20 OR HOUR(dateTimeReported) < 1 THEN 1 ELSE 0 END) AS SIGNED) AS "8PM_to_1AM_count"
                '
                )
                ->get();
            $message = 'All time data';
        } elseif ($select === 'donutCrime') {
            $data = DB::table('datasets')
                ->selectRaw(
                    '
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 1 AND HOUR(dateTimeReported) < 4 THEN 1 ELSE 0 END) AS SIGNED) AS "1AM_to_4AM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 4 AND HOUR(dateTimeReported) < 8 THEN 1 ELSE 0 END) AS SIGNED) AS "4AM_to_8AM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 8 AND HOUR(dateTimeReported) < 12 THEN 1 ELSE 0 END) AS SIGNED) AS "8AM_to_12PM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 12 AND HOUR(dateTimeReported) < 16 THEN 1 ELSE 0 END) AS SIGNED) AS "12PM_to_4PM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 16 AND HOUR(dateTimeReported) < 20 THEN 1 ELSE 0 END) AS SIGNED) AS "4PM_to_8PM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 20 OR HOUR(dateTimeReported) < 1 THEN 1 ELSE 0 END) AS SIGNED) AS "8PM_to_1AM_count"
                '
                )
                ->whereRaw('datasets.offense NOT LIKE "%RECKLESS%"')
                ->get();
            $message = 'Crime data';
        } elseif ($select === 'donutAccident') {
            $data = DB::table('datasets')
                ->selectRaw(
                    '
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 1 AND HOUR(dateTimeReported) < 4 THEN 1 ELSE 0 END) AS SIGNED) AS "1AM_to_4AM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 4 AND HOUR(dateTimeReported) < 8 THEN 1 ELSE 0 END) AS SIGNED) AS "4AM_to_8AM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 8 AND HOUR(dateTimeReported) < 12 THEN 1 ELSE 0 END) AS SIGNED) AS "8AM_to_12PM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 12 AND HOUR(dateTimeReported) < 16 THEN 1 ELSE 0 END) AS SIGNED) AS "12PM_to_4PM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 16 AND HOUR(dateTimeReported) < 20 THEN 1 ELSE 0 END) AS SIGNED) AS "4PM_to_8PM_count",
                CAST(SUM(CASE WHEN HOUR(dateTimeReported) >= 20 OR HOUR(dateTimeReported) < 1 THEN 1 ELSE 0 END) AS SIGNED) AS "8PM_to_1AM_count"
                '
                )
                ->whereRaw('datasets.offense LIKE "%RECKLESS%"')
                ->get();
            $message = 'Accident data';
        } else {
            $data = [];
            $message = 'Invalid selection';
        }

        return response()->json([
            'data' => $data,
            'message' => $message,
            'select' => $select
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
                'longitude' => $boundary->longitude,
            ];
        });

        return response()->json([
            'boundaries' => $formattedBoundaries,
            'center' => $boundaries,
        ]);
    }

    public function fetchHeatMap(Request $request)
    {
        $year = $request->year;

        if ($year === null) {
            $brgyHeatMap = DB::table('datasets')->select('barangay_boundaries.barangay_name', 'datasets.lat', 'datasets.lng')->join('barangay_boundaries', 'barangay_boundaries.id', '=', 'datasets.barangay_id')->get();
        } else {
            $brgyHeatMap = DB::table('datasets')
                ->select('barangay_boundaries.barangay_name', 'datasets.lat', 'datasets.lng')
                ->join('barangay_boundaries', 'barangay_boundaries.id', '=', 'datasets.barangay_id')
                ->whereRaw('YEAR(datasets.dateTimeReported) = ?', [$year])
                ->get();
        }

        return response()->json([
            'brgyHeatMap' => $brgyHeatMap,
            'year' => $year,
        ]);
    }

    public function getBarData(Request $request)
    {
        $select = $request->data;
        $message = 'Invalid Selection!';

        if ($select === 'allReports') {
            $sqlData = DB::table('datasets')
                ->selectRaw(
                    'barangay_boundaries.barangay_name AS barangay_name,
            count(datasets.id) as totalReports'
                )
                ->rightJoin('barangay_boundaries', 'barangay_boundaries.id', '=', 'datasets.barangay_id')
                ->groupBy('barangay_name')
                ->orderBy('totalReports', 'DESC')
                ->limit(10)
                ->get();

            $message = 'All Reports';
        } else if ($select === 'crime') {
            $sqlData = DB::table('datasets')
                ->selectRaw(
                    'barangay_boundaries.barangay_name AS barangay_name,
                    sum(CASE WHEN datasets.offense NOT LIKE "%RECKLESS%" THEN 1 ELSE 0 END) as totalReports',
                )
                ->rightJoin('barangay_boundaries', 'barangay_boundaries.id', '=', 'datasets.barangay_id')
                ->groupBy('barangay_name')
                ->orderBy('totalReports', 'DESC')
                ->limit(10)
                ->get();
            $message = 'All Reports';
        } else if ($select === 'accident') {
            $sqlData = DB::table('datasets')
                ->selectRaw(
                    'barangay_boundaries.barangay_name AS barangay_name,
                    sum(CASE WHEN datasets.offense LIKE "%RECKLESS%" THEN 1 ELSE 0 END) as totalReports'
                )
                ->rightJoin('barangay_boundaries', 'barangay_boundaries.id', '=', 'datasets.barangay_id')
                ->groupBy('barangay_name')
                ->orderBy('totalReports', 'DESC')
                ->limit(10)
                ->get();
            $message = 'All Reports';
        } else {
            $sqlData = [];
        }

        return response()->json([
            'data' => $sqlData,
            'message' => $message
        ]);
    }

    public function fetchMostReports(Request $request)
    {
        $year = $request->year;

        if ($year === null) {
            $allData = DB::table('datasets')
                ->selectRaw(
                    'barangay_boundaries.barangay_name AS barangay_name,
            count(datasets.id) as totalReports,
            sum(CASE WHEN datasets.offense LIKE "%RECKLESS%" THEN 1 ELSE 0 END) as accidentReports,
            sum(CASE WHEN datasets.offense NOT LIKE "%RECKLESS%" THEN 1 ELSE 0 END) as crimeReports',
                )
                ->rightJoin('barangay_boundaries', 'barangay_boundaries.id', '=', 'datasets.barangay_id')
                ->groupBy('barangay_name')
                ->orderBy('totalReports', 'DESC')
                ->limit(10)
                ->get();
        } else {
            $allData = DB::table('datasets')
                ->selectRaw(
                    'barangay_boundaries.barangay_name AS barangay_name,
            count(datasets.id) as totalReports,
            sum(CASE WHEN datasets.offense LIKE "%RECKLESS%" THEN 1 ELSE 0 END) as accidentReports,
            sum(CASE WHEN datasets.offense NOT LIKE "%RECKLESS%" THEN 1 ELSE 0 END) as crimeReports',
                )
                ->whereYear('dateTimeReported', $year)
                ->rightJoin('barangay_boundaries', 'barangay_boundaries.id', '=', 'datasets.barangay_id')
                ->groupBy('barangay_name')
                ->orderBy('totalReports', 'DESC')
                ->limit(10)
                ->get();
        }

        return response()->json([
            'year' => $year,
            'allData' => $allData,
        ]);
    }
}
