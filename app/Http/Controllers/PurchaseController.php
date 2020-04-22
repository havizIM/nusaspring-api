<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Purchase;
use App\PurchaseProduct;
use App\Log;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Purchase::class);
        
        $search = $request->search;
        $limit = $request->limit;
        $supplier = $request->supplier;

        if($limit || $search || $supplier){
            $purchases = Purchase::where('contact_id', '=', $supplier)
                                ->where('purchase_number', 'like', '%'.$search.'%')
                                ->limit($limit)
                                ->get();
        } else {
            $purchases = Purchase::with('contact')->withCount('products')->get();
        }

        $purchases->each->setAppends([
            'grand_total', 
            'total_qty', 
            'total_discount',
            'total_payment', 
            'total_return', 
            'total_qty_return', 
            'total_ppn_return',
            'total_return_discount'
        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch purchases',
            'results' => $purchases
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
        $this->authorize('create', Purchase::class);

        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',
            'purchase_number' => 'required|unique:purchases',
            'date' => 'required|date_format:Y-m-d',
            'total_ppn' => 'required|numeric',
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

                $attachment->storeAs('public/purchases/', $store_as);
            } else {
                $store_as = NULL;
            }

            $purchase = new Purchase;
            $purchase->contact_id = $request->contact_id;
            $purchase->email = $request->email;
            $purchase->address = $request->address;
            $purchase->purchase_number = $request->purchase_number;
            $purchase->reference_number = $request->reference_number;
            $purchase->message = $request->message;
            $purchase->memo = $request->memo;
            $purchase->total_ppn = abs($request->total_ppn);
            $purchase->date = $request->date;
            $purchase->due_date = $request->due_date;
            $purchase->attachment = $store_as;
            $purchase->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add Purchase',
                'error' => $e->getMessage()
            ], 500);
        }

        foreach($request['product_id'] as $key => $val){
            try {
                $product = new PurchaseProduct;
                $product->purchase_id = $purchase->id;
                $product->product_id = $request['product_id'][$key];
                $product->description = $request['description'][$key];
                $product->unit = $request['unit'][$key];
                $product->qty = $request['qty'][$key];
                $product->ppn = $request['ppn'][$key];
                $product->discount_percent = $request['discount_percent'][$key];
                $product->discount_amount = abs($request['discount_amount'][$key]) * -1;
                $product->unit_price = $request['unit_price'][$key];
                $product->total = $request['unit_price'][$key] * $request['qty'][$key];
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
            $log->description = 'Add Purchase';
            $log->reference_id = $purchase->id;
            $log->url = '#/purchase/'.$purchase->id;

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
            'message' => 'Success add purchase',
            'results' => $purchase,
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
        $purchase = Purchase::with([
            'products', 
            'contact', 
            'payments', 
            'returns.products', 
            'created_by_user', 
            'updated_by_user', 
            'deleted_by_user'
        ])->findOrFail($id);
        
        $this->authorize('view', $purchase);

        $purchase->setAppends([
            'grand_total', 
            'total_qty', 
            'total_discount',
            'total_payment', 
            'total_return', 
            'total_qty_return', 
            'total_ppn_return',
            'total_return_discount'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific purchase',
            'results' => $purchase
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
        $purchase = Purchase::findOrFail($id);

        $this->authorize('update', $purchase);
        
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',
            'purchase_number' => 'required|unique:purchases',
            'total_ppn' => 'required|numeric',
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

                    $attachment->storeAs('public/purchases/', $store_as);
                    $purchase->attachment = $store_as;
                } else {
                    $store_as = NULL;
                }

                $purchase->contact_id = $request->contact_id;
                $purchase->email = $request->email;
                $purchase->address = $request->address;
                $purchase->purchase_number = $request->purchase_number;
                $purchase->reference_number = $request->reference_number;
                $purchase->message = $request->message;
                $purchase->memo = $request->memo;
                $purchase->total_ppn = abs($request->total_ppn);
                $purchase->date = $request->date;
                $purchase->due_date = $request->due_date;

                $purchase->update();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed update purchase',
                ], 500);
            }
            
            $purchase->products()->delete();

            foreach($request['product_id'] as $key => $val){
                try {
                    $product = new PurchaseProduct;

                    $product->purchase_id = $purchase->id;
                    $product->product_id = $request['product_id'][$key];
                    $product->description = $request['description'][$key];
                    $product->unit = $request['unit'][$key];
                    $product->qty = $request['qty'][$key];
                    $product->ppn = $request['ppn'][$key];
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
                $log->description = 'Update Purchase';
                $log->reference_id = $purchase->id;
                $log->url = '#/purchase/'.$purchase->id;

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
            'message' => 'Success update purchase',
            'results' => $purchase,
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
        $purchase = Purchase::findOrFail($id);

        $this->authorize('delete', $purchase);

        DB::beginTransaction();
        
            try {
                $purchase->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete category',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Purchase';
                $log->reference_id = $purchase->id;
                $log->url = '#/purchase';

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
            'message' => 'Success archive purchase',
            'results' => $purchase,
        ], 200);
    }

    public function picture($filename)
    {
        $path = base_path().'/storage/app/public/purchases/'.$filename;
        return Response::download($path);  
    }

    

}
