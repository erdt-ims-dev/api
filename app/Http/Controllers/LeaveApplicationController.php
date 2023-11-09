<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\LeaveApplication;

class LeaveApplicationController extends APIController
{
    public function create(Request $request){
        $data = $request->all();
        try{
            $LeaveUuid = Str::orderedUuid();

            $leave = new LeaveApplication();
            $leave->id = $LeaveUuid;
            $leave->user_id = $data['user_id'];
            $leave->leave_start = $data['leave_start'];
            $leave->leave_end = $data['leave_end'];
            $leave->leave_reason = $data['leave_reason'];
            $leave->status = 'pending'; 
            $leave->save();
            $this->response['data'] = "Created";
            return $this->getResponse();
        }catch(\Throwable $th){
            $this->response['error'] = $th->getMessage();
            $this->response['status'] = $th->getCode();
            return $this->getResponse();
        }
    }
    
    public function retrievebyParameter($id) {
        
        $leaveapplication = Item::findOrFail($id);
        return response()->json($leaveapplication);
    }

    public function update($id) {
        $leaveapplication = Item::findOrFail($id);

        $validatedData = $request->validate([
            'user_id' => 'required',
            'leave_start' => 'required',
            'leave_end' => 'required',
            'leave_reason' => 'required',
            'status' => 'required',
        ]);

        $leaveapplication->update($validatedData);
        return response()->json($leaveapplication, 200);
    }

    public function delete($id) {
        $leaveapplication = Item::findOrFail($id);
        
        $leaveapplication->delete($id);
        return response()->json($leaveapplication, 200);
    }
}
