<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\Comments;
use App\Models\StaffApplicantManagement;

class StaffApplicantManagementController extends Controller
{
    //
    public function index()
    {
        return 'this is the staff applicant management controller';
    }

    public function create(Request $request)
    {
        //$testData = "very good job!";
        $data = $request->all();
        $staffapplicant = new StaffApplicantManagement();
        $staffapplicant->id = Str::orderedUuid();
        $staffapplicant->message = $data['scholarrequest_id'];
        $staffapplicant->message = $data['staff_id'];
        $staffapplicant->save();

        $this->response['data'] = 'Comment added successfully';
        return $this->getResponse();
        
        // Comments::create(['message' => $testData]);

        // return response()->json(['message' => 'Test data added successfully'], 201);
        //return $testData;
    }

    public function retrievebyParameter(Request $request)    {
        
        // $comment = Item::findOrFail($id);
        // return response()->json($comment);
    
        $data = $request->all();
        $response = StaffApplicantManagement::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response[0];
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
