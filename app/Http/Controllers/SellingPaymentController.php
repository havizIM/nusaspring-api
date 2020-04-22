<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\SellingPayment;
use App\Log;

class SellingPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', SellingPayment::class);
        
        $payment = SellingPayment::with([
            'selling',
            'contact'
        ])->get();
        
        return response()->json([
            'status' => true,
            'message' => 'Success fetch payment',
            'results' => $payment
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
        $this->authorize('create', SellingPayment::class);

        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',
            'payment_number' => 'required|string',
            'type' => 'required|in:Cash,Cek/Giro,Transfer,Kartu Kredit',
            'date' => 'required|date_format:Y-m-d',
            'amount' => 'required|numeric',
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
                $picture = $request->file('attachment');
        
                if($picture){
                    $name       = $picture->getClientOriginalName();
                    $filename   = pathinfo($name, PATHINFO_FILENAME);
                    $extension  = $picture->getClientOriginalExtension();

                    $store_as   = $filename.'_'.time().'.'.$extension;

                    $picture->storeAs('public/selling_payments/', $store_as);
                } else {
                    $store_as = NULL;
                }

                $payment = new SellingPayment;
                $payment->contact_id = $request->contact_id;
                $payment->selling_id = $request->selling_id;
                $payment->payment_number = $request->payment_number;
                $payment->type = $request->type;
                $payment->date = $request->date;
                $payment->description = $request->description;
                $payment->memo = $request->memo;
                $payment->amount = $request->amount;
                $payment->attachment = $store_as;

                $payment->save();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add payment',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Add Selling Payment';
                $log->reference_id = $payment->id;
                $log->url = '#/selling_payment';

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
            'message' => 'Success add payment',
            'results' => $payment,
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
        $selling_payment = SellingPayment::with([
            'selling',
            'contact', 
            'created_by_user', 
            'updated_by_user', 
            'deleted_by_user'
        ])->findOrFail($id);
        
        $this->authorize('view', $selling_payment);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific payment',
            'results' => $selling_payment
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
        $payment = SellingPayment::findOrFail($id);

        $this->authorize('update', $payment);
        
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|exists:contacts,id',
            'payment_number' => 'required|string',
            'type' => 'required|in:Cash,Cek/Giro,Transfer,Kartu Kredit',
            'date' => 'required|date_format:Y-m-d',
            'amount' => 'required|numeric',
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
                $picture = $request->file('attachment');
        
                if($picture){
                    $name       = $picture->getClientOriginalName();
                    $filename   = pathinfo($name, PATHINFO_FILENAME);
                    $extension  = $picture->getClientOriginalExtension();

                    $store_as   = $filename.'_'.time().'.'.$extension;

                    $picture->storeAs('public/selling_payments/', $store_as);
                    $payment->picture = $store_as;
                } else {
                    $store_as = NULL;
                }
                    
                $payment->contact_id = $request->contact_id;
                $payment->selling_id = $request->selling_id;
                $payment->payment_number = $request->payment_number;
                $payment->type = $request->type;
                $payment->description = $request->description;
                $payment->memo = $request->memo;
                $payment->date = $request->date;
                $payment->amount = $request->amount;
                
                $payment->update();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add payment',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Edit Selling Payment';
                $log->reference_id = $payment->id;
                $log->url = '#/selling_payment';

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
            'message' => 'Success update payment',
            'results' => $payment,
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
        $payment = SellingPayment::findOrFail($id);

        $this->authorize('delete', $payment);

        DB::beginTransaction();
        
            try {
                $payment->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete payment',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Selling Payment';
                $log->reference_id = $payment->id;
                $log->url = '#/selling_payment';

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
            'message' => 'Success archive payment',
            'results' => $selling,
        ], 200);
    }

    public function picture($filename)
    {
        $path = base_path().'/storage/app/public/selling_payments/'.$filename;
        return Response::download($path);  
    }
}
