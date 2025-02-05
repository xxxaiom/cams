<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardForecast extends Controller
{
    public function index()
    {
        return view('content.admin.dashboard.admin-forecast');
    }

    public function runRScript(Request $request)
    {
        $incidentType = $request->type;

        if ($incidentType === null) {
            return response()->json([
                'message' => 'Please select a type'
            ]);
        } else if ($incidentType != 'crime' && $incidentType != 'accidents') {
            return response()->json([
                'message' => 'Panlabot pa!'
            ]);
        } else {
            $rscriptPath = 'C:\\Program Files\\R\\R-4.4.2\\bin\\Rscript.exe';
            $scriptPath = base_path('r_scripts/try.R');

            $command = '"' . $rscriptPath . '" "' . $scriptPath . '" "' . $incidentType . '" 2>nul';

            $output = shell_exec($command);

            $cleanedOutput = preg_replace('/^\s*(null device|1)\s*$/m', '', $output);

            $decodedOutput = json_decode($cleanedOutput, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'error' => 'Invalid JSON format',
                    'message' => json_last_error_msg()
                ]);
            }

            return response()->json([
                'type' => $incidentType,
                'output' => $decodedOutput['forecast'],
                'ts_plot' => $decodedOutput['ts_plot'],
                'auto_arima_plot' => $decodedOutput['auto_arima_plot']
            ]);
        }
    }
}
