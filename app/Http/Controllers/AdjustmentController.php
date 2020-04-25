<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Adjustment;
use App\AdjustmentProduct;
use App\Log;

use Help;

class AdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Adjustment::class);
        
        $adjustments = Adjustment::withCount('products')->get();
        $adjustments->each->setAppends(['grand_total', 'total_qty']);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch adjustments',
            'results' => $adjustments
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
        $this->authorize('create', Adjustment::class);

        $validator = Validator::make($request->all(), [
            'category' => 'required|in:Qty Awal,Transfer In,Transfer Out,Other',
            'date' => 'required|date_format:Y-m-d',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'qty' => 'required|array',
            'qty.*' => 'required|numeric',
            'unit' => 'required|array',
            'unit.*' => 'required|string',
            'unit_price' => 'required|array',
            'unit_price.*' => 'required|numeric',
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

                $attachment->storeAs('public/adjustment/', $store_as);
            } else {
                $store_as = NULL;
            }

            $adjustment = new Adjustment;
            $adjustment->category = $request->category;
            $adjustment->reference_number = Help::dateCode('ADJ', 'adjustments', 'reference_number');
            $adjustment->date = $request->date;
            $adjustment->memo = $request->memo;
            $adjustment->attachment = $store_as;
            $adjustment->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add Adjustment',
                'error' => $e->getMessage()
            ], 500);
        }

        foreach($request['product_id'] as $key => $val){
            try {
                $product = new AdjustmentProduct;
                $product->adjustment_id = $adjustment->id;
                $product->product_id = $request['product_id'][$key];
                $product->description = $request['description'][$key];

                switch($adjustment->category){
                    case 'Qty Awal':
                        $filtered_qty = abs($request['qty'][$key]);
                    break;

                    case 'Transfer In':
                        $filtered_qty = abs($request['qty'][$key]);
                    break;

                    case 'Transfer Out':
                        $filtered_qty = abs($request['qty'][$key]) * -1;
                    break;

                    case 'Other':
                        $filtered_qty = $request['qty'][$key];
                    break;

                    default:
                        $filtered_qty = abs($request['qty'][$key]);
                }

                $product->unit = $request['unit'][$key];
                $product->qty = $filtered_qty;
                $product->unit_price = $request['unit_price'][$key];
                $product->total = $request['unit_price'][$key] * $filtered_qty;
                $product->save();

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add Product',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Adjustment';
            $log->reference_id = $adjustment->id;
            $log->url = '#/adjustment';

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
            'message' => 'Success add adjustment',
            'results' => $adjustment,
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
        $adjustment = Adjustment::with(['products'])->findOrFail($id);
        $adjustment->setAppends(['grand_total', 'total_qty']);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific adjustment',
            'results' => $adjustment
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
        $adjustment = Adjustment::findOrFail($id);

        $this->authorize('update', $adjustment);
        
        $validator = Validator::make($request->all(), [
            'category' => 'required|in:Qty Awal,Transfer In,Transfer Out,Other',
            'date' => 'required|date_format:Y-m-d',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'qty' => 'required|array',
            'qty.*' => 'required|numeric',
            'unit' => 'required|array',
            'unit.*' => 'required|string',
            'unit_price' => 'required|array',
            'unit_price.*' => 'required|numeric',
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

                    $attachment->storeAs('public/adjustment/', $store_as);
                    $adjustment->attachment = $store_as;
                } else {
                    $store_as = NULL;
                }
                    
                $adjustment->category = $request->category;
                $adjustment->reference_number = $request->reference_number;
                $adjustment->date = $request->date;
                $adjustment->memo = $request->memo;
                $adjustment->update();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed update adjustment',
                ], 500);
            }
            
            $adjustment->products()->delete();

            foreach($request['product_id'] as $key => $val){
                try {
                    $product = new AdjustmentProduct;
                    $product->adjustment_id = $adjustment->id;
                    $product->product_id = $request['product_id'][$key];
                    $product->description = $request['description'][$key];

                    switch($adjustment->category){
                        case 'Qty Awal':
                            $filtered_qty = abs($request['qty'][$key]);
                        break;

                        case 'Transfer In':
                            $filtered_qty = abs($request['qty'][$key]);
                        break;

                        case 'Transfer Out':
                            $filtered_qty = abs($request['qty'][$key]) * -1;
                        break;

                        case 'Other':
                            $filtered_qty = $request['qty'][$key];
                        break;

                        default:
                            $filtered_qty = abs($request['qty'][$key]);
                    }

                    $product->qty = $filtered_qty;
                    $product->unit = $request['unit'][$key];
                    $product->unit_price = $request['unit_price'][$key];
                    $product->total = $request['unit_price'][$key] * $filtered_qty;
                    $product->save();
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed add product',
                    ], 500);
                }
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Update Adjustment';
                $log->reference_id = $adjustment->id;
                $log->url = '#/adjustment';

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
            'message' => 'Success update adjustment',
            'results' => $adjustment,
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
        $adjustment = Adjustment::findOrFail($id);

        $this->authorize('delete', $adjustment);

        DB::beginTransaction();

            try {
                $delete = $adjustment->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete adjustment',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Adjustment';
                $log->reference_id = $adjustment->id;
                $log->url = '#/adjustment';

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
            'message' => 'Success archive adjustment',
            'results' => $adjustment,
        ], 200);
    }

    public function picture($filename)
    {
        $path = base_path().'/storage/app/public/adjustment/'.$filename;
        return Response::download($path);  
    }
}
