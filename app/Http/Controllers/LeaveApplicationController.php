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
        $LeaveUuid = Str::orderedUuid();

        $leave = new LeaveApplication();
        $leave->id = $LeaveUuid;
        $leave->user_id = $data['user_id'];
        $leave->leave_start = $data['leave_start'];
        $leave->leave_end = $data['leave_end'];
        $table->leave_reason = $data['leave_reason'];
        $table->status = 'pending'; 
    }
}
