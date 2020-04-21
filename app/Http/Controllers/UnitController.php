<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Unit;
use App\Log;

use Illuminate\Support\Facades\DB;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Unit::class);
        
        $units = Unit::withCount('products')->get();

        return response()->json([
            'status' => true,
            'message' => 'Success fetch units',
            'results' => $units
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Unit::class);

        $validator = Validator::make($request->all(), [
            'unit_name' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Fields Required',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

            try {
                $unit = new Unit;
                $unit->unit_name = $request->unit_name;

                $unit->save();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add unit',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Add Unit';
                $log->reference_id = $unit->id;
                $log->url = '#/unit';

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
            'message' => 'Success add unit',
            'results' => $unit,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $this->authorize('update', $unit);
        
        $validator = Validator::make($request->all(), [
            'unit_name' => 'required|string',
        ]);
            
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Fields Required',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

            try {
                $unit->unit_name = $request->unit_name;
                $unit->update();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed Edit unit',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Edit Unit';
                $log->reference_id = $unit->id;
                $log->url = '#/unit';

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
            'message' => 'Success update unit',
            'results' => $unit,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);

        $this->authorize('delete', $unit);

        try {
            $unit->delete();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add unit',
                'error' => $e->getMessage()
            ], 500);
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Delete Unit';
            $log->reference_id = $unit->id;
            $log->url = '#/unit';

            $log->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add log',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Success archive unit',
            'results' => $unit,
        ], 200);
    }
}
