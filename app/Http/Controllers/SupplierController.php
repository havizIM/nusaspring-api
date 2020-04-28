<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Contact;
use App\Log;

use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Contact::class);

        $search = $request->search;
        $limit = $request->limit;
        $type = $request->type;

        if($limit || $search || $type){
            $suppliers = Contact::where('type', '=', $type)
                                ->where(function($query) use($search){
                                    $query->where('pic', 'like', '%'.$search.'%')
                                          ->orWhere('contact_name', 'like', '%'.$search.'%');
                                })
                                ->limit($limit)
                                ->get();
        } else {
            $suppliers = Contact::where('type', '=', 'Supplier')->get();
        }
        
        
        $suppliers->each->setAppends([
            'sum_purchase', 
            'sum_purchase_ppn', 
            'sum_purchase_payment', 
            'sum_purchase_return', 
            'sum_purchase_return_ppn',
            'sum_purchase_discount',
            'sum_purchase_return_discount',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch suppliers',
            'results' => $suppliers
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
        $this->authorize('create', Contact::class);

        $validator = Validator::make($request->all(), [
            'contact_name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string'
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
                $supplier = new Contact;
                $supplier->contact_name = $request->contact_name;
                $supplier->type = 'Supplier';
                $supplier->pic = $request->pic;
                $supplier->phone = $request->phone;
                $supplier->fax = $request->fax;
                $supplier->handphone = $request->handphone;
                $supplier->email = $request->email;
                $supplier->address = $request->address;
                $supplier->npwp = $request->npwp;
                $supplier->memo = $request->memo;

                $supplier->save();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add supplier',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Add Supplier #'.$supplier->id;
                $log->reference_id = $supplier->id;
                $log->url = '#/supplier/'.$supplier->id;

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
            'message' => 'Success add supplier',
            'results' => $supplier,
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
        $supplier = Contact::with([
            'purchases', 
            'purchase_returns.purchase', 
            'purchase_payments.purchase', 
            'created_by_user', 
            'updated_by_user', 
            'deleted_by_user'
        ])->findOrFail($id);

        $this->authorize('view', $supplier);

        $supplier->setAppends([
            'sum_purchase', 
            'sum_purchase_ppn', 
            'sum_purchase_payment', 
            'sum_purchase_return', 
            'sum_purchase_return_ppn',
            'sum_purchase_discount',
            'sum_purchase_return_discount',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific supplier',
            'results' => $supplier
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
        $supplier = Contact::findOrFail($id);

        $this->authorize('update', $supplier);
        
        $validator = Validator::make($request->all(), [
            'contact_name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string'
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
                $supplier->contact_name = $request->contact_name;
                $supplier->pic = $request->pic;
                $supplier->phone = $request->phone;
                $supplier->fax = $request->fax;
                $supplier->handphone = $request->handphone;
                $supplier->email = $request->email;
                $supplier->address = $request->address;
                $supplier->npwp = $request->npwp;
                $supplier->memo = $request->memo;
                
                $supplier->update();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed update supplier',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Update Supplier #'.$supplier->id;
                $log->reference_id = $supplier->id;
                $log->url = '#/supplier/'.$supplier->id;

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
            'message' => 'Success update supplier',
            'results' => $supplier,
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
        $supplier = Contact::findOrFail($id);

        $this->authorize('delete', $supplier);

        DB::beginTransaction();

            try {
                $supplier->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete supplier',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Supplier #'.$supplier->id;
                $log->reference_id = $supplier->id;
                $log->url = '#/supplier';

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
            'message' => 'Success archive supplier',
            'results' => $supplier,
        ], 200);
    }
}
