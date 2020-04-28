<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\SellingReturn;
use App\SellingReturnProduct;
use App\Log;

use Help;

class SellingReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', SellingReturn::class);
        
        $return = SellingReturn::with([
            'selling',
            'contact'
        ])->withCount('products')->get();

        $return->each->setAppends([
            'grand_total', 
            'total_discount',
            'total_qty',
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch return',
            'results' => $return
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
        
        $this->authorize('create', SellingReturn::class);

        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',
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

                $attachment->storeAs('public/selling_returns/', $store_as);
            } else {
                $store_as = NULL;
            }

            $return = new SellingReturn;
            $return->contact_id = $request->contact_id;
            $return->selling_id = $request->selling_id;
            $return->return_number = Help::dateCode('RSL', 'selling_returns', 'return_number');
            $return->reference_number = $request->reference_number;
            $return->message = $request->message;
            $return->memo = $request->memo;
            $return->total_ppn = abs($request->total_ppn);
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
                    $product = new SellingReturnProduct;
                    $product->selling_return_id = $return->id;
                    $product->product_id = $request['product_id'][$key];
                    $product->description = $request['description'][$key];
                    $product->unit = $request['unit'][$key];
                    $product->qty = $request['qty'][$key];
                    $product->ppn = isset($request['ppn'][$key]) ? $request['ppn'][$key] : 'N';
                    $product->discount_percent = $request['discount_percent'][$key];
                    $product->discount_amount = abs($request['discount_amount'][$key]) * -1;
                    $product->unit_price = $request['unit_price'][$key];
                    $product->total = $request['unit_price'][$key] * $request['qty'][$key];
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
            $log->description = 'Add Selling Return #'.$return->return_number;
            $log->reference_id = $return->id;
            $log->url = '#/selling_return/'.$return->id;

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
        $returns = SellingReturn::with([
            'products',
            'contact',
            'selling.products',
            'created_by_user',
            'updated_by_user',
            'deleted_by_user'
        ])->findOrFail($id);
        
        $this->authorize('view', $returns);

        $returns->setAppends([
            'grand_total', 
            'total_discount',
            'total_qty',
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
        $return = SellingReturn::findOrFail($id);

        $this->authorize('update', $return);
        
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',
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

                    $attachment->storeAs('public/selling_returns/', $store_as);
                    $return->attachment = $store_as;
                } else {
                    $store_as = NULL;
                }
                    
                $return->contact_id = $request->contact_id;
                $return->selling_id = $request->selling_id;
                $return->return_number = $request->return_number;
                $return->reference_number = $request->reference_number;
                $return->message = $request->message;
                $return->memo = $request->memo;
                $return->total_ppn = abs($request->total_ppn);
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
                    $product = new SellingReturnProduct;
                    $product->selling_return_id = $return->id;
                    $product->product_id = $request['product_id'][$key];
                    $product->description = $request['description'][$key];
                    $product->unit = $request['unit'][$key];
                    $product->qty = $request['qty'][$key];
                    $product->ppn = isset($request['ppn'][$key]) ? $request['ppn'][$key] : 'N';
                    $product->discount_percent = $request['discount_percent'][$key];
                    $product->discount_amount = abs($request['discount_amount'][$key]) * -1;
                    $product->unit_price = $request['unit_price'][$key];
                    $product->total = $request['unit_price'][$key] * $request['qty'][$key];
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
                $log->description = 'Update Selling Return #'.$return->return_number;
                $log->reference_id = $return->id;
                $log->url = '#/selling_return/'.$return->id;

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
        $return = SellingReturn::findOrFail($id);

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
                $log->description = 'Delete Selling Return #'.$return->return_number;
                $log->reference_id = $return->id;
                $log->url = '#/selling_return/'.$return->id;

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
        $path = base_path().'/storage/app/public/selling_returns/'.$filename;
        return Response::download($path);  
    }
}
