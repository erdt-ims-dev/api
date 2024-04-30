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
        $s3BaseUrl = config('app.s3_base_url');
        try{
            $LeaveUuid = Str::uuid();

            $leave = new LeaveApplication();
            $leave->user_id = $data['user_id'];
            $leave->leave_start = $data['leave_start'];
            $leave->leave_end = $data['leave_end'];

            $s3BaseUrl = config('app.s3_base_url');
            $letter = $request->file('leave_letter')->storePublicly('users/'.$data['user_id'].'/scholar/leave_application');

            $leave->leave_reason = "{$s3BaseUrl}{$letter}";
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
    
    public function retrieveOneByParameter(Request $request)    {
    
        $data = $request->all();
        $response = LeaveApplication::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response[0];
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveMultipleByParameter(Request $request)    {
    
        $data = $request->all();
        $response = LeaveApplication::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }

    public function retrieveAll() {

        $response = LeaveApplication::all();

        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }

    public function update(Request $request) {

        $data = $request->all();
        $query = LeaveApplication::find($data['id']);
        $s3BaseUrl = config('app.s3_base_url');
        if(!$query){
            $this->response['error'] = "Comment Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $query->user_id = $data['user_id'];
            $query->leave_start = $data['leave_start'];
            $query->leave_end = $data['leave_end'];

            //aws call
            $letter = $request->file('leave_letter')->storePublicly('users/'.$data['user_id'].'/scholar/leave_application');

            $query->letter = "{$s3BaseUrl}{$letter}";
            $query->leave_reason = $data['leave_reason'];
            $query->status = $data['status'];
            $query->save();
            $this->response['data'] = true;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }

    public function delete(Request $request) {
        
        $data = $request->all();
        $query = LeaveApplication::find($data['id']);
        if(!$query){
            $this->response['error'] = "Application Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $query->delete();
            $this->response['data'] = true;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }

}
