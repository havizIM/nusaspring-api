<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\StockOpname;
use App\StockOpnameProduct;
use App\Log;

use Help;

class StockOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', StockOpname::class);
        
        $stock_opname = StockOpname::withCount('products')->get();
        $stock_opname->each->setAppends([
            'total_system_amount',
            'total_actual_amount',
            'total_system_qty',
            'total_actual_qty',
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch stock opname',
            'results' => $stock_opname
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
        $this->authorize('create', StockOpname::class);

        $validator = Validator::make($request->all(), [
            'so_number' => 'required|string',
            'date' => 'required|date_format:Y-m-d',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'system_qty' => 'required|array',
            'system_qty.*' => 'required|numeric',
            'actual_qty' => 'required|array',
            'actual_qty.*' => 'required|numeric',
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

                $attachment->storeAs('public/stock_opnames/', $store_as);
            } else {
                $store_as = NULL;
            }

            $stock_opname = new StockOpname;
            $stock_opname->so_number = Help::dateCode('SO', 'stock_opnames', 'so_number');
            $stock_opname->date = $request->date;
            $stock_opname->status = 'Proccess';
            $stock_opname->message = $request->message;
            $stock_opname->memo = $request->memo;
            $stock_opname->attachment = $store_as;
            $stock_opname->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add stock opname',
                'error' => $e->getMessage()
            ], 500);
        }

        foreach($request['product_id'] as $key => $val){
            try {
                $product = new StockOpnameProduct;
                $product->stock_opname_id = $stock_opname->id;
                $product->product_id = $request['product_id'][$key];
                $product->description = $request['description'][$key];
                $product->unit_price = $request['unit_price'][$key];
                $product->unit = $request['unit'][$key];
                $product->system_qty = $request['system_qty'][$key];
                $product->actual_qty = $request['actual_qty'][$key];
                $product->system_total = $request['system_total'][$key];
                $product->actual_total = $request['actual_total'][$key];
                $product->save();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add Existing Product',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        if($request['add_product_id']){
            foreach($request['add_product_id'] as $key => $val){
                try {
                    $product = new StockOpnameProduct;
                    $product->stock_opname_id = $stock_opname->id;
                    $product->product_id = $request['add_product_id'][$key];
                    $product->description = $request['add_description'][$key];
                    $product->unit_price = $request['add_unit_price'][$key];
                    $product->unit = $request['add_unit'][$key];
                    $product->system_qty = $request['add_system_qty'][$key];
                    $product->actual_qty = $request['add_actual_qty'][$key];
                    $product->system_total = $request['add_system_total'][$key];
                    $product->actual_total = $request['add_actual_total'][$key];
                    $product->save();
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed add Additional Product',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Stock Opname';
            $log->reference_id = $stock_opname->id;
            $log->url = '#/stock_opname/'.$stock_opname->id;

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
            'message' => 'Success add stock opname',
            'results' => $stock_opname,
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
        $stock_opname = StockOpname::with(['products'])->findOrFail($id);
        $stock_opname->setAppends([
            'total_system_amount',
            'total_actual_amount',
            'total_system_qty',
            'total_actual_qty',
        ]);

        $this->authorize('view', $stock_opname);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific stock opname',
            'results' => $stock_opname
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
        $stock_opname = StockOpname::findOrFail($id);

        $this->authorize('update', $stock_opname);
        
        $validator = Validator::make($request->all(), [
            'so_number' => 'required|string',
            'date' => 'required|date_format:Y-m-d',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'description' => 'required|array',
            'description.*' => 'required|string',
            'system_qty' => 'required|array',
            'system_qty.*' => 'required|numeric',
            'actual_qty' => 'required|array',
            'actual_qty.*' => 'required|numeric',
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

                    $attachment->storeAs('public/stock_opnames/', $store_as);
                    $stock_opname->attachment = $store_as;
                } else {
                    $store_as = NULL;
                }
                    
                $stock_opname->so_number = $request->so_number;
                $stock_opname->date = $request->date;
                $stock_opname->status = 'Proccess';
                $stock_opname->message = $request->message;
                $stock_opname->memo = $request->memo;
                $stock_opname->update();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed update stock opname',
                ], 500);
            }
            
            $stock_opname->products()->delete();

            foreach($request['product_id'] as $key => $val){
                try {
                    $product = new StockOpnameProduct;
                    $product->stock_opname_id = $stock_opname->id;
                    $product->product_id = $request['product_id'][$key];
                    $product->description = $request['description'][$key];
                    $product->unit_price = $request['unit_price'][$key];
                    $product->unit = $request['unit'][$key];
                    $product->system_qty = $request['system_qty'][$key];
                    $product->actual_qty = $request['actual_qty'][$key];
                    $product->system_total = $request['system_total'][$key];
                    $product->actual_total = $request['actual_total'][$key];
                    $product->save();
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed add Existing product',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            if($request['add_product_id']){
                foreach($request['add_product_id'] as $key => $val){
                    try {
                        $product = new StockOpnameProduct;
                        $product->stock_opname_id = $stock_opname->id;
                        $product->product_id = $request['add_product_id'][$key];
                        $product->description = $request['add_description'][$key];
                        $product->unit_price = $request['add_unit_price'][$key];
                        $product->unit = $request['add_unit'][$key];
                        $product->system_qty = $request['add_system_qty'][$key];
                        $product->actual_qty = $request['add_actual_qty'][$key];
                        $product->system_total = $request['add_system_total'][$key];
                        $product->actual_total = $request['add_actual_total'][$key];
                        $product->save();
                    } catch (\Exception $e) {
                        DB::rollback();
                        return response()->json([
                            'status' => false,
                            'message' => 'Failed add Additional Product',
                            'error' => $e->getMessage()
                        ], 500);
                    }
                }
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Update Stock Opname';
                $log->reference_id = $stock_opname->id;
                $log->url = '#/stock_opname/'.$stock_opname->id;

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
            'message' => 'Success update stock opname',
            'results' => $stock_opname,
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
        $stock_opname = StockOpname::findOrFail($id);

        $this->authorize('delete', $stock_opname);

        DB::beginTransaction();

            try {
                $delete = $stock_opname->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete stock opname',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Stock Opname';
                $log->reference_id = $stock_opname->id;
                $log->url = '#/stock_opname/'.$stock_opname->id;

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
            'message' => 'Success archive stock opname',
            'results' => $stock_opname,
        ], 200);
    }

    public function validation($id)
    {
        $stock_opname = StockOpname::findOrFail($id);

        $this->authorize('delete', $stock_opname);

        DB::beginTransaction();

            try {
                $stock_opname->status = 'Valid';
                $stock_opname->update();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed validate stock opname',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Validation Stock Opname #'.$stock_opname->so_number;
                $log->reference_id = $stock_opname->id;
                $log->url = '#/stock_opname/'.$stock_opname->id;

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
            'message' => 'Success validate stock opname',
            'results' => $stock_opname,
        ], 200);
    }

    public function picture($filename)
    {
        $path = base_path().'/storage/app/public/stock_opnames/'.$filename;
        return Response::download($path);  
    }
}
