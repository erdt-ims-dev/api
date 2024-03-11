<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\Comments;
use App\Models\StaffApplicantManagement;
use App\Models\User;

class StaffApplicantManagementController extends Controller
{
    //
    
    // not yet implemented 2-20-24

    public function create(Request $request)
    {
        // verify authorization 
        $user = User::where('id', $data['user_id'])->whereIn('account_type', ['admin', 'staff'])->first();
        if (!$user) {
            $this->response['error'] = 'Insufficient previleges';
            $this->response['status'] = 404; // or another appropriate status code
            return $this->getResponse();
        }
        try
        {
            $staff = new StaffApplicantManagement();
            $staff->scholar_request_id = ScholarRequestApplication::find($data['scholar_id']);
            $staff->endorsed_by = $user->id;
            $staff->save();

            $this->response['data'] = 'Endorsement added successfully';
            $this->response['status'] = 200;
            return $this->getResponse();
        }
        catch (\Throwable $th)
        {
            $this->response['error'] = $th->getMessage();
            $this->response['status'] = $th->getCode();
            return $this->getResponse();
        }
    }

    public function retrieveOneByParameter(Request $request)    {
    
        $data = $request->all();
        $response = StaffApplicantManagement::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response[0];
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveMultipleByParameter(Request $request)    {
    
        $data = $request->all();
        $response = StaffApplicantManagement::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }

    public function retrieveAll() {

        $response = StaffApplicantManagement::all();

        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }

    public function update(Request $request) {

        $data = $request->all();
        $query = StaffApplicantManagement::find($data['id']);
        if(!$query){
            $this->response['error'] = "Comment Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $query->message = $data['scholarrequest_id'];
            $query->message = $data['staff_id'];
            $query->save();
            $this->response['data'] = true;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }

    public function delete(Request $request) {
        
        $data = $request->all();
        $query = StaffApplicantManagement::find($data['id']);
        if(!$query){
            $this->response['error'] = "Comment Not Found";
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
