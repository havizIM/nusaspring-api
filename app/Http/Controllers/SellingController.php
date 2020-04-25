<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Selling;
use App\SellingProduct;
use App\Log;

use Help;

class SellingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Selling::class);
        

        $search = $request->search;
        $limit = $request->limit;
        $customer = $request->customer;

        if($limit || $search || $customer){
            $sellings = Selling::where('contact_id', '=', $customer)
                                ->where('selling_number', 'like', '%'.$search.'%')
                                ->limit($limit)
                                ->get();
        } else {
            $sellings = Selling::with([
                'contact'
            ])->withCount('products')->get();
        }

        $sellings->each->setAppends([
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
            'message' => 'Success fetch sellings',
            'results' => $sellings
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
        $this->authorize('create', Selling::class);

        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',
            'selling_number' => 'required',
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

                $attachment->storeAs('public/sellings/', $store_as);
            } else {
                $store_as = NULL;
            }

            $selling = new Selling;
            $selling->contact_id = $request->contact_id;
            $selling->email = $request->email;
            $selling->address = $request->address;
            $selling->selling_number = Help::dateCode('SL', 'sellings', 'selling_number');
            $selling->reference_number = $request->reference_number;
            $selling->message = $request->message;
            $selling->memo = $request->memo;
            $selling->total_ppn = abs($request->total_ppn) * -1;
            $selling->date = $request->date;
            $selling->due_date = $request->due_date;
            $selling->attachment = $store_as;
            $selling->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed add Selling',
                'error' => $e->getMessage()
            ], 500);
        }

        foreach($request['product_id'] as $key => $val){
            try {
                $product = new SellingProduct;
                $product->selling_id = $selling->id;
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
            $log->description = 'Add Selling';
            $log->reference_id = $selling->id;
            $log->url = '#/selling/'.$selling->id;

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
            'message' => 'Success add selling',
            'results' => $selling,
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
        $selling = Selling::with([
            'products', 
            'contact', 
            'payments', 
            'returns.products', 
            'created_by_user', 
            'updated_by_user', 
            'deleted_by_user'
        ])->findOrFail($id);
        
        $this->authorize('view', $selling);

        $selling->setAppends([
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
            'message' => 'Success fetch specific selling',
            'results' => $selling
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
        $selling = Selling::findOrFail($id);

        $this->authorize('update', $selling);
        
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',
            'selling_number' => 'required',
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

                    $attachment->storeAs('public/sellings/', $store_as);
                    $selling->attachment = $store_as;
                } else {
                    $store_as = NULL;
                }
                    
                $selling->contact_id = $request->contact_id;
                $selling->email = $request->email;
                $selling->address = $request->address;
                $selling->selling_number = $request->selling_number;
                $selling->reference_number = $request->reference_number;
                $selling->message = $request->message;
                $selling->memo = $request->memo;
                $selling->total_ppn = abs($request->total_ppn) * -1;
                $selling->date = $request->date;
                $selling->due_date = $request->due_date;
                $selling->update();
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' => 'Failed update selling',
                ], 500);
            }
            
            $selling->products()->delete();

            foreach($request['product_id'] as $key => $val){
                try {
                    $product = new SellingProduct;
                    $product->selling_id = $selling->id;
                    $product->product_id = $request['product_id'][$key];
                    $product->description = $request['description'][$key];
                    $product->unit = $request['unit'][$key];
                    $product->qty = abs($request['qty'][$key]) * -1;
                    $product->ppn = $request['ppn'][$key];
                    $product->discount_percent = $request['discount_percent'][$key];
                    $product->discount_amount = abs( $request['discount_amount'][$key]);
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
                $log->description = 'Update Selling';
                $log->reference_id = $selling->id;
                $log->url = '#/selling/'.$selling->id;

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
            'message' => 'Success update selling',
            'results' => $selling,
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
        $selling = Selling::findOrFail($id);

        $this->authorize('delete', $selling);

        DB::beginTransaction();
        
            try {
                $selling->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete selling',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Selling';
                $log->reference_id = $selling->id;
                $log->url = '#/selling';

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
            'message' => 'Success archive selling',
            'results' => $selling,
        ], 200);
    }

    public function picture($filename)
    {
        $path = base_path().'/storage/app/public/sellings/'.$filename;
        return Response::download($path);  
    }

    
}
