<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Artisan;
use App\Log;
use Auth;
// use Illuminate\Support\Facades\Artisan;

class ExportController extends Controller
{
    public function database()
    {
        $auth = Auth::user();

        DB::beginTransaction();
            try {
                \Artisan::call('backup:run --only-db');
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed backup database',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = $auth->id;
                $log->description = 'Backup Database';
                $log->reference_id = $auth->id;
                $log->url = '#/setting';
                $log->save();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add log',
                    'error' => $e->getMessage()
                ], 500);
            }
        
        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Success backup database',
        ], 200);
    }
}
