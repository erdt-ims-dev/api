<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\LeaveApplicationStatus;
use App\Models\AccountDetails;

// class LeaveApplicationStatusController extends Controller
// {
//     //
//     public function index()
//     {
//         return 'this is the leave application status controller';
//     }

//     public function create(Request $request)
//     {
//         //$testData = "very good job!";
//         $data = $request->all();
//         $leaverecord = new LeaveApplicationStatus();
//         $leaverecord->id = Str::orderedUuid();
//         $leaverecord->message = $data['scholar_leave_app_id'];
//         $leaverecord->message = $data['user_id'];
//         $leaverecord->message = $data['comment_id'];
//         $leaverecord->message = $data['application_status'];
//         $leaverecord->message = $data['application_letter'];
//         $comments->save();

//         $this->response['data'] = 'leave application added successfully';
//         return $this->getResponse();
        
//         // Comments::create(['message' => $testData]);

//         // return response()->json(['message' => 'Test data added successfully'], 201);
//         //return $testData;
//     }

//     public function retrievebyParameter(Request $request)    {
        
//         // $comment = Item::findOrFail($id);
//         // return response()->json($comment);
    
//         $data = $request->all();
//         $response = LeaveApplicationStatus::where($data['col'], '=', $data['value'])->get();
//         $this->response['data'] = $response[0];
//         $this->response['status'] = 200;
//         return $this->getResponse();
//     }

//     public function retrieveAll() {

//         $response = LeaveApplicationStatus::all();

//         $this->response['data'] = $response;
//         $this->response['status'] = 200;
//         return $this->getResponse();
//     }

//     public function update(Request $request) {

//         $data = $request->all();
//         $query = LeaveApplicationStatus::find($data['id']);
//         if(!$query){
//             $this->response['error'] = "Comment Not Found";
//             $this->response['status'] = 401;
//             return $this->getError();
//         }
//         if($query){
//             $query->message = $data['scholar_leave_app_id'];
//             $query->message = $data['user_id'];
//             $query->message = $data['comment_id'];
//             $query->message = $data['application_status'];
//             $query->message = $data['application_letter'];
//             $query->save();
//             $this->response['data'] = true;
//             $this->response['status'] = 200;
//             return $this->getResponse();
//         }
//     }

//     public function delete(Request $request) {
        
//         $data = $request->all();
//         $query = LeaveApplicationStatus::find($data['id']);
//         if(!$query){
//             $this->response['error'] = "Comment Not Found";
//             $this->response['status'] = 401;
//             return $this->getError();
//         }
//         if($query){
//             $query->delete();
//             $this->response['data'] = true;
//             $this->response['status'] = 200;
//             return $this->getResponse();
//         }
//     }

// }
