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
}
