<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Cost;
use App\CostDetail;
use App\Log;

use Help;

class CostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Cost::class);
        
        $costs = Cost::withCount('details')->get();

        $costs->each->setAppends([
            'grand_total',
            'total_discount',
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch costs',
            'results' => $costs
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
        $this->authorize('create', Cost::class);

        $validator = Validator::make($request->all(), [
            'cost_number' => 'required|string',
            'type' => 'required|in:Cash,Cek/Giro,Transfer,Kartu Kredit',
            'date' => 'required|date_format:Y-m-d',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'amount' => 'required|array',
            'amount.*' => 'required|numeric',
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
            $attachment = $request->file('attachment');
        
            if($attachment){
                $name       = $attachment->getClientOriginalName();
                $filename   = pathinfo($name, PATHINFO_FILENAME);
                $extension  = $attachment->getClientOriginalExtension();

                $store_as   = $filename.'_'.time().'.'.$extension;

                $attachment->storeAs('public/costs/', $store_as);
            } else {
                $store_as = NULL;
            }

            $cost = new Cost;
            $cost->cost_number = Help::dateCode('CST', 'costs', 'cost_number');
            $cost->to = $request->to;
            $cost->type = $request->type;
            $cost->date = $request->date;
            $cost->total_ppn = $request->total_ppn;
            $cost->message = $request->message;
            $cost->memo = $request->memo;
            $cost->attachment = $store_as;
            $cost->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add cost',
                'error' => $e->getMessage()
            ], 500);
        }

        foreach($request['description'] as $key => $val){
            try {
                $detail = new CostDetail;
                $detail->cost_id = $cost->id;
                $detail->description = $request['description'][$key];
                $detail->ppn = isset($request['ppn'][$key]) ? $request['ppn'][$key] : 'N';
                $detail->amount = $request['amount'][$key];
                $detail->discount_percent = $request['discount_percent'][$key];
                $detail->discount_amount = abs($request['discount_amount'][$key]) * -1;
                $detail->save();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add detail',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Cost #'.$cost->cost_number;
            $log->reference_id = $cost->id;
            $log->url = '#/cost/'.$cost->id;

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
            'message' => 'Success add cost',
            'results' => $cost,
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
        $cost = Cost::with(['details'])->findOrFail($id);
        $cost->setAppends([
            'grand_total',
            'total_discount',
        ]);

        $this->authorize('view', $cost);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific cost',
            'results' => $cost
        ], 200);
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
        $cost = Cost::findOrFail($id);

        $this->authorize('update', $cost);
        
        $validator = Validator::make($request->all(), [
            'cost_number' => 'required|string',
            'type' => 'required|in:Cash,Cek/Giro,Transfer,Kartu Kredit',
            'date' => 'required|date_format:Y-m-d',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'amount' => 'required|array',
            'amount.*' => 'required|numeric',
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
                $attachment = $request->file('attachment');
        
                if($attachment){
                    $name       = $attachment->getClientOriginalName();
                    $filename   = pathinfo($name, PATHINFO_FILENAME);
                    $extension  = $attachment->getClientOriginalExtension();

                    $store_as   = $filename.'_'.time().'.'.$extension;

                    $attachment->storeAs('public/costs/', $store_as);
                    $cost->attachment = $store_as;
                } else {
                    $store_as = NULL;
                }
                    
                $cost->cost_number = $request->cost_number;
                $cost->to = $request->to;
                $cost->type = $request->type;
                $cost->date = $request->date;
                $cost->total_ppn = $request->total_ppn;
                $cost->message = $request->message;
                $cost->memo = $request->memo;

                $cost->update();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed update cost',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            $cost->details()->delete();

            foreach($request['description'] as $key => $val){
                try {
                    $detail = new CostDetail;
                    $detail->cost_id = $cost->id;
                    $detail->description = $request['description'][$key];
                    $detail->ppn = isset($request['ppn'][$key]) ? $request['ppn'][$key] : 'N';
                    $detail->amount = $request['amount'][$key];
                    $detail->discount_percent = $request['discount_percent'][$key];
                    $detail->discount_amount = abs($request['discount_amount'][$key]) * -1;
                    $detail->save();
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed add detail',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Update Cost #'.$cost->cost_number;
                $log->reference_id = $cost->id;
                $log->url = '#/cost/'.$cost->id;

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
            'message' => 'Success update cost',
            'results' => $cost,
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
        $cost = Cost::findOrFail($id);

        $this->authorize('delete', $cost);

        DB::beginTransaction();

            try {
                $delete = $cost->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete cost',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Cost #'.$cost->cost_number;
                $log->reference_id = $cost->id;
                $log->url = '#/cost';

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
            'message' => 'Success archive cost',
            'results' => $cost,
        ], 200);
    }

    public function picture($filename)
    {
        $path = base_path().'/storage/app/public/costs/'.$filename;
        return Response::download($path);  
    }
}
