<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Reminder;
use App\Log;

use Illuminate\Support\Facades\DB;

class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Reminder::class);
        
        $reminders = Reminder::where('user_id', Auth::id())->get();

        return response()->json([
            'status' => true,
            'message' => 'Success fetch reminders',
            'results' => $reminders
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
        $this->authorize('create', Reminder::class);

        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'start_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date' => 'required|date_format:Y-m-d H:i:s',
            'color' => 'required|in:primary,info,success,warning,danger',
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
                $reminder = new Reminder;
                $reminder->user_id = Auth::id();
                $reminder->description = $request->description;
                $reminder->color = $request->color;
                $reminder->start_date = $request->start_date;
                $reminder->end_date = $request->end_date;

                $reminder->save();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed add reminder',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Add Reminder #'.$reminder->id;
                $log->reference_id = $reminder->id;
                $log->url = '#/reminder';

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
            'message' => 'Success add reminder',
            'results' => $reminder,
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
        $reminder = Reminder::findOrFail($id);
        $this->authorize('view', $reminder);

        return response()->json([
            'status' => true,
            'message' => 'Success fetch specific reminder',
            'results' => $reminder
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
        $reminder = Reminder::findOrFail($id);

        $this->authorize('update', $reminder);
        
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'start_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date' => 'required|date_format:Y-m-d H:i:s',
            'color' => 'required|in:primary,info,success,warning,danger',
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
                $reminder->description = $request->description;
                $reminder->color = $request->color;
                $reminder->start_date = $request->start_date;
                $reminder->end_date = $request->end_date;
                
                $reminder->update();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed update reminder',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Update Reminder #'.$reminder->id;
                $log->reference_id = $reminder->id;
                $log->url = '#/reminder';

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
            'message' => 'Success update reminder',
            'results' => $reminder,
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
        $reminder = Reminder::findOrFail($id);

        $this->authorize('delete', $reminder);

        DB::beginTransaction();
        
            try {
                $reminder->delete();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed delete reminder',
                    'error' => $e->getMessage()
                ], 500);
            }

            try {
                $log = new Log;
                $log->user_id = Auth::id();
                $log->description = 'Delete Reminder #'.$reminder->id;
                $log->reference_id = $reminder->id;
                $log->url = '#/reminder';

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
            'message' => 'Success archive reminder',
            'results' => $reminder,
        ], 200);
    }
}
