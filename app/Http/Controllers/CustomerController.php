<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Contact;
use App\Log;

use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
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
            $customers = Contact::where('type', '=', $type)
                                ->where(function($query) use($search){
                                    $query->where('pic', 'like', '%'.$search.'%')
                                          ->orWhere('contact_name', 'like', '%'.$search.'%');
                                })
                                ->limit($limit)
                                ->get();
        } else {
            $customers = Contact::where('type', '=', 'Customer')->get();
        }
        
        $customers->each->setAppends([
            'sum_selling',
            'sum_selling_discount',
            'sum_selling_ppn', 
            'sum_selling_payment', 
            'sum_selling_return', 
            'sum_selling_return_ppn',
            'sum_selling_return_discount',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch customers',
            'results' => $customers
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
                $customer = new Contact;
                $customer->contact_name = $request->contact_name;
                $customer->type = 'Customer';
                $customer->pic = $request->pic;
                $customer->phone = $request->phone;
                $customer->fax = $request->fax;
                $customer->handphone = $request->handphone;
                $customer->email = $request->email;
                $customer->address = $request->address;
                $customer->npwp = $request->npwp;
                $customer->memo = $request->memo;

                $customer->save();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add customer',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Add Customer #'.$customer->id;
                $log->reference_id = $customer->id;
                $log->url = '#/customer/'.$customer->id;

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
            'message' => 'Success add customer',
            'results' => $customer,
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
        $customer = Contact::with([
            'sellings',
            'selling_returns.selling',
            'selling_payments.selling',
            'created_by_user',
            'updated_by_user',
            'deleted_by_user'
        ])->findOrFail($id);

        $customer->setAppends([
            'sum_selling',
            'sum_selling_discount',
            'sum_selling_ppn', 
            'sum_selling_payment', 
            'sum_selling_return', 
            'sum_selling_return_ppn',
            'sum_selling_return_discount',
        ]);

        $this->authorize('view', $customer);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific customer',
            'results' => $customer
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
        $customer = Contact::findOrFail($id);

        $this->authorize('update', $customer);
        
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
                $customer->contact_name = $request->contact_name;
                $customer->pic = $request->pic;
                $customer->phone = $request->phone;
                $customer->fax = $request->fax;
                $customer->handphone = $request->handphone;
                $customer->email = $request->email;
                $customer->address = $request->address;
                $customer->npwp = $request->npwp;
                $customer->memo = $request->memo;
                
                $customer->update();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed update customer',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Add Customer #'.$customer->id;
                $log->reference_id = $customer->id;
                $log->url = '#/customer/'.$customer->id;

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
            'message' => 'Success update customer',
            'results' => $customer,
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
        $customer = Contact::findOrFail($id);

        $this->authorize('delete', $customer);

        DB::beginTransaction();

            try {
                $customer->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete customer',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Customer #'.$customer->id;
                $log->reference_id = $customer->id;
                $log->url = '#/customer';

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
            'message' => 'Success archive customer',
            'results' => $customer,
        ], 200);
    }
}
