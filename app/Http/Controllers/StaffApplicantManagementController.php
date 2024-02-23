<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\Comments;
use App\Models\StaffApplicantManagement;

class StaffApplicantManagementController extends Controller
{
    //
    
    // not yet implemented 2-20-24

    public function create(Request $request)
    {
        //$testData = "very good job!";
        $data = $request->all();
        $staffapplicant = new StaffApplicantManagement();
        $staffapplicant->id = Str::uuid();
        $staffapplicant->message = $data['scholar_request_id'];
        $staffapplicant->message = $data['endorsed_by'];
        $staffapplicant->save();

        $this->response['data'] = 'Comment added successfully';
        return $this->getResponse();
    }

    public function retrievebyParameter(Request $request)    {
    
        $data = $request->all();
        $response = StaffApplicantManagement::where($data['col'], '=', $data['value'])->get();
        if ($response->isEmpty()) {
            // If no results are found, return an appropriate response
            $this->response['error'] = 'No matching records found.';
            $this->response['status'] = 404;
        } else {
            // If results are found, return the first record
            $this->response['data'] = $response[0];
            $this->response['status'] = 200;
        }
    
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
