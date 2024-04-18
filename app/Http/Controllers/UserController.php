<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AccountDetails;
use App\Models\ScholarRequestApplication;
class UserController extends APIController
{
    public function retrieveAll(){
        $response = User::withTrashed()->get();;
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveOneByParameter(Request $request)    {
    
        $data = $request->all();
        $response = User::where($data['col'], '=', $data['value'])->get();
        if ($response->isNotEmpty()) {
            $this->response['data'] = $response[0];
            $this->response['status'] = 200;
        } else {
            // Handle the case where no results are found
            $this->response['error'] = "User not found";
            $this->response['status'] = 404;
        }
        return $this->getResponse();
    }
    public function retrieveMultipleByParameter(Request $request)    {
    
        $data = $request->all();
        $response = User::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveMultipleByFilter(Request $request)    {
    
        $data = $request->all();
        $response = User::where($data['col'], '!=', $data['value'])->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveWithAccountDetailsWithEmail(Request $request)    {
        // receives email, searches User table for id, uses id to search AccountDetails table
        $data = $request->all();
        $response = User::where("email", '=', $data['email'])->get()->first();
        $details = AccountDetails::where("user_id", '=', $response->id)->get()->first();
        $this->response['data'] = $details;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function update(Request $request) {
        $data = $request->all();
        $query = User::find($data['id']);
        if (!$query) {
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if ($query) {
            $query->{$data['col']} = $data['value'];
            $query->save();
            $this->response['data'] = $query;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }
    public function delete(Request $request) {
        
        $data = $request->all();
        $query = User::find($data['id']);
        if(!$query){
            $this->response['error'] = "User Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $query->status = 'deactivated';
            $query->save();
            $query->delete();
            $this->response['data'] = true;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }
    // statistics

    public function retrieveStatistics(Request $request)    {
    
        $data = $request->all();
        $applicant_count = User::where('account_type', '=', 'applicant')->get()->count();
        $scholar_count = User::where('account_type', '=', 'scholar')->get()->count();
        $pending_count = ScholarRequestApplication::where('status', '=', 'pending')->get()->count();
        $endorsed_count = ScholarRequestApplication::where('status', '=', 'endorsed')->get()->count();
        $total_applications = ScholarRequestApplication::count();
        $total_approved = ScholarRequestApplication::where('status', '=', 'approved')->get()->count();
        
        $statistics = [
            'applicant_count' => $applicant_count,
            'pending_count' => $pending_count,
            'endorsed_count' => $endorsed_count,
            'scholar_count' => $scholar_count,
            'total_applications' => $total_applications,
            'total_approved' => $total_approved,
        ];
        $this->response['data'] = $statistics;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
}
