<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\PurchaseReturn;
use App\PurchaseReturnProduct;
use App\Log;

class PurchaseReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', PurchaseReturn::class);
        
        $returns = PurchaseReturn::with('purchase.contact')->withCount('products')->get();
        $returns->each->setAppends([
            'grand_total', 
            'total_discount',
            'total_qty',
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch returns',
            'results' => $returns
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
        $this->authorize('create', PurchaseReturn::class);

        $validator = Validator::make($request->all(), [
            'purchase_id' => 'required|exists:purchases,id',
            'return_number' => 'required',
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

                $attachment->storeAs('public/purchase_returns/', $store_as);
            } else {
                $store_as = NULL;
            }

            $return = new PurchaseReturn;
            $return->purchase_id = $request->purchase_id;
            $return->return_number = $request->return_number;
            $return->reference_number = $request->reference_number;
            $return->message = $request->message;
            $return->memo = $request->memo;
            $return->total_ppn = abs($request->total_ppn) * -1;
            $return->date = $request->date;
            $return->attachment = $store_as;
            $return->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add return',
                'error' => $e->getMessage()
            ], 500);
        }

        foreach($request['product_id'] as $key => $val){
            try {
                if(abs($request['qty'][$key]) != 0){
                    $product = new PurchaseReturnProduct;
                    $product->purchase_return_id = $return->id;
                    $product->product_id = $request['product_id'][$key];
                    $product->description = $request['description'][$key];
                    $product->unit = $request['unit'][$key];
                    $product->qty = abs($request['qty'][$key]) * -1;
                    $product->ppn = $request['ppn'][$key];
                    $product->discount_percent = $request['discount_percent'][$key];
                    $product->discount_amount = abs($request['discount_amount'][$key]);
                    $product->unit_price = $request['unit_price'][$key];
                    $product->total = $request['unit_price'][$key] * abs($request['qty'][$key]) * -1;
                    $product->save();
                }
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add product',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        try {
            $log = new Log;
            $log->user_id = Auth::id();
            $log->description = 'Add Purchase Return';
            $log->reference_id = $return->id;
            $log->url = '#/purchase_return/'.$return->id;

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
            'message' => 'Success add return',
            'results' => $return,
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
        $returns = PurchaseReturn::with([
            'products', 
            'purchase.products', 
            'purchase.contact', 
            'created_by_user', 
            'updated_by_user', 
            'deleted_by_user'
        ])->findOrFail($id);
        
        $this->authorize('view', $returns);

        $returns->setAppends([
            'grand_total',
            'total_discount',
            'total_qty'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific return',
            'results' => $returns
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
        $return = PurchaseReturn::findOrFail($id);

        $this->authorize('update', $return);
        
        $validator = Validator::make($request->all(), [
            'purchase_id' => 'required|exists:purchases,id',
            'return_number' => 'required',
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

                    $attachment->storeAs('public/purchase_returns/', $store_as);
                    $return->attachment = $store_as;
                } else {
                    $store_as = NULL;
                }
                    
                $return->purchase_id = $request->purchase_id;
                $return->return_number = $request->return_number;
                $return->reference_number = $request->reference_number;
                $return->message = $request->message;
                $return->memo = $request->memo;
                $return->discount_percent = $request->discount_percent;
                $return->discount_amount = abs($request->discount_amount);
                $return->total_ppn = abs($request->total_ppn) * -1;
                $return->date = $request->date;

                $return->update();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed update return',
                ], 500);
            }
            
            $return->products()->delete();

            foreach($request['product_id'] as $key => $val){
                try {
                    $product = new PurchaseReturnProduct;

                    $product->purchase_return_id = $return->id;
                    $product->product_id = $request['product_id'][$key];
                    $product->description = $request['description'][$key];
                    $product->unit = $request['unit'][$key];
                    $product->qty = abs($request['qty'][$key]) * -1;
                    $product->ppn = $request['ppn'][$key];
                    $product->unit_price = $request['unit_price'][$key];
                    $product->total = $request['unit_price'][$key] * abs($request['qty'][$key]) * -1;

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
                $log->description = 'Update Purchase Return';
                $log->reference_id = $return->id;
                $log->url = '#/purchase_return';

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
            'message' => 'Success update return',
            'results' => $return,
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
        $return = PurchaseReturn::findOrFail($id);

        $this->authorize('delete', $return);

        DB::beginTransaction();
        
            try {
                $return->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete return',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Purchase Return';
                $log->reference_id = $return->id;
                $log->url = '#/purchase_return';

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
            'message' => 'Success archive return',
            'results' => $return,
        ], 200);
    }

    public function picture($filename)
    {
        $path = base_path().'/storage/app/public/purchase_returns/'.$filename;
        return Response::download($path);  
    }
}
