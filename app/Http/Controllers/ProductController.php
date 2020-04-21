<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Product;
use App\Log;

use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Product::class);

        $product = Product::with([
            'category', 
            'unit'
        ])->get();

        $product->each->setAppends([
            'sum_purchase', 
            'sum_selling', 
            'sum_purchase_return', 
            'sum_selling_return', 
            'sum_adjustment'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch product',
            'results' => $product
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
        $this->authorize('create', Product::class);

        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string'
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
                $picture = $request->file('picture');
        
                if($picture){
                    $name       = $picture->getClientOriginalName();
                    $filename   = pathinfo($name, PATHINFO_FILENAME);
                    $extension  = $picture->getClientOriginalExtension();

                    $store_as   = $filename.'_'.time().'.'.$extension;

                    $picture->storeAs('public/product/', $store_as);
                } else {
                    $store_as = NULL;
                }

                $product = new Product;
                $product->product_name = $request->product_name;
                $product->sku = $request->sku;
                $product->category_id = $request->category_id;
                $product->unit_id = $request->unit_id;
                $product->purchase_price = $request->purchase_price;
                $product->selling_price = $request->selling_price;
                $product->picture = $store_as;
                $product->memo = $request->memo;

                $product->save();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add product',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Add Product';
                $log->reference_id = $product->id;
                $log->url = '#/product';

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
            'message' => 'Success add product',
            'results' => $product,
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
        $product = Product::with([
            'category', 
            'unit', 
            'adjustments', 
            'purchases', 
            'sellings', 
            'purchase_returns', 
            'selling_returns', 
            'created_by_user', 
            'updated_by_user', 
            'deleted_by_user'
        ])->findOrFail($id);

        $this->authorize('view', $product);

        $product->setAppends([
            'sum_qty_awal', 
            'sum_transfer_in', 
            'sum_transfer_out', 
            'sum_other', 
            'sum_purchase', 
            'sum_selling', 
            'sum_purchase_return', 
            'sum_selling_return'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific product',
            'results' => $product
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
        $product = Product::findOrFail($id);

        $this->authorize('update', $product);
        
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string',
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
                $picture = $request->file('picture');
        
                if($picture){
                    $name       = $picture->getClientOriginalName();
                    $filename   = pathinfo($name, PATHINFO_FILENAME);
                    $extension  = $picture->getClientOriginalExtension();

                    $store_as   = $filename.'_'.time().'.'.$extension;

                    $picture->storeAs('public/product/', $store_as);
                    $product->picture = $store_as;
                } else {
                    $store_as = NULL;
                }
                    
                $product->product_name = $request->product_name;
                $product->sku = $request->sku;
                $product->category_id = $request->category_id;
                $product->unit_id = $request->unit_id;
                $product->purchase_price = $request->purchase_price;
                $product->selling_price = $request->selling_price;
                $product->memo = $request->memo;
                
                $product->update();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add category',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Edit Category';
                $log->reference_id = $product->id;
                $log->url = '#/product';

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
            'message' => 'Success update product',
            'results' => $product,
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
        $product = Product::findOrFail($id);

        $this->authorize('delete', $product);

        DB::beginTransaction();

            try {
                $delete = $product->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add category',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Product';
                $log->reference_id = $product->id;
                $log->url = '#/category';

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
            'message' => 'Success archive product',
            'results' => $product,
        ], 200);
    }

    public function picture($filename)
    {
        $path = base_path().'/storage/app/public/product/'.$filename;
        return Response::download($path);  
    }
}
