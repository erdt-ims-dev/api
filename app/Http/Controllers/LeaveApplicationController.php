<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\AccountDetails;

use App\Models\LeaveApplication;
use Illuminate\Support\Facades\Storage;

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
            if($request->file('leave_letter')){
                $letter = $request->file('leave_letter')->storePublicly('users/'.$data['user_id'].'/scholar/leave_application');
            }else{
                
            }

            $leave->comment_id = $data['comment_id'];
            $leave->leave_letter = "{$s3BaseUrl}{$letter}";
            $leave->status = 'Pending'; 
            $leave->save();
            $this->response['data'] = "Created";
            return $this->getResponse();
        }catch(\Throwable $th){
            $this->response['error'] = $th->getMessage();
            $this->response['status'] = $th->getCode();
            return $this->getResponse();
        }
    }
    public function upload(Request $request){
    try {

        // Extract data
        $data = $request->all();

        // Create a new ScholarTask instance
        $leave = new LeaveApplication();
        $leave->user_id = $data['user_id'];
        $leave->year = $data['year'];
        $leave->semester = $data['semester'];

        // Initialize S3 storage
        $s3BaseUrl = config('app.s3_base_url');
        $leaveFiles = $request->file('file');
        $leaveUrls = [];

        // Handle multiple file uploads
        if ($leaveFiles && is_array($leaveFiles)) {
            foreach ($leaveFiles as $file) {
                // Store each file in AWS S3 using the specified path
                $filePath = $file->storePublicly("users/{$data['user_id']}/scholar/portfolio", 's3');
                
                // Generate a public URL for each stored file
                $leaveUrls[] = Storage::disk('s3')->url($filePath);
            }
        }
        

        // Store the file URLs as a JSON array in the database
        $leave->file = json_encode($leaveUrls);
        $leave->comment_id = $data['comment_id'];
        $leave->status = 'pending'; 

        $leave->save();

        // Prepare the response
        $this->response['data'] = [
            'id' => $leave->id,
            'leave' => $leave->user_id,
            'year' => $leave->year,
            'semester' => $leave->semester,
            'file' => $leaveUrls,  // Return the list of file URLs
            // 'approval_status' => $scholar->approval_status,
        ];

        $this->response['status'] = 200;
        return $this->getResponse();

    } catch (\Throwable $th) {
        $this->response['error'] = mb_convert_encoding($th->getMessage(), 'UTF-8', 'auto');
        $this->response['status'] = $th->getCode();
        return $this->getResponse();
    }
    }

    public function uploadWithEmail(Request $request){
        try {
    
            // Extract data
            $data = $request->all();
            $query = User::where('email', '=', $data['email'])->first();
            if (!$query) {
                // If no user found, set an error response
                $this->response['error'] = 'User not found with provided email.';
                $this->response['status'] = 404;
                return $this->getResponse();
            }
            // Create a new ScholarTask instance
            $leave = new LeaveApplication();
            $leave->user_id = $query->id;
            $leave->year = $data['year'];
            $leave->semester = $data['semester'];
    
            // Initialize S3 storage
            $s3BaseUrl = config('app.s3_base_url');
            $leaveFiles = $request->file('file');
            $leaveUrls = [];
    
            // Handle multiple file uploads
            if ($leaveFiles && is_array($leaveFiles)) {
                foreach ($leaveFiles as $file) {
                    // Store each file in AWS S3 using the specified path
                    $filePath = $file->storePublicly("users/{$query->id}/scholar/portfolio", 's3');
                    
                    // Generate a public URL for each stored file
                    $leaveUrls[] = Storage::disk('s3')->url($filePath);
                }
            }
            
    
            // Store the file URLs as a JSON array in the database
            $leave->file = json_encode($leaveUrls);
            $leave->comment_id = "None";
            $leave->status = 'pending'; 
    
            $leave->save();
    
            // Prepare the response
            $this->response['data'] = [
                'id' => $leave->id,
                'user_id' => $leave->user_id,
                'year' => $leave->year,
                'semester' => $leave->semester,
                'file' => $leaveUrls,  // Return the list of file URLs
                // 'approval_status' => $scholar->approval_status,
            ];
    
            $this->response['status'] = 200;
            return $this->getResponse();
    
        } catch (\Throwable $th) {
            $this->response['error'] = mb_convert_encoding($th->getMessage(), 'UTF-8', 'auto');
            $this->response['status'] = $th->getCode();
            return $this->getResponse();
        }
        }

    public function uploadEdit(Request $request){
        $data = $request->all();
        $query = LeaveApplication::find($data['id']);

        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }

        if($query){
            $fileUrls = [];

            // If new files are uploaded, process and store each one
            if ($request->hasFile('file')) {
                foreach ($request->file('file') as $file) {
                    $path = $file->storePublicly('users/' . $query->user_id . '/scholar/portfolio');
                    $fileUrls[] = "https://erdt.s3.us-east-1.amazonaws.com/{$path}";
                }
            } else {
                // If no new files, retain current value of `study` as a JSON array
                $fileUrls = json_decode($query->file) ?? [];
            }

            // Update only if new values are provided, otherwise keep current values
            $query->file = json_encode($fileUrls);
            $query->year = $data['year'];
            $query->semester = $data['semester'];

            // Save the updated record
            $query->save();
             
            $this->response['data'] = $query;
            $this->response['status'] = 200;
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

    public function retrieveAll(Request $request) {

        $response = LeaveApplication::all();

        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveLeaves()
    {
        $response = LeaveApplication::with(['user', 'user.accountDetails'])->get();

        $data = $response->map(function ($leave) {
            return [
                'leave' => $leave,
                'email' => $leave->user->email ?? null,
                'name' => $leave->user->accountDetails
                    ? $leave->user->accountDetails->first_name . ' ' . $leave->user->accountDetails->last_name
                    : null,
            ];
        });

        $this->response['data'] = $data;
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
            $query->status = $data['status'];
            $query->save();
            $this->response['data'] = true;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }
    public function updateOne(Request $request){
        $data = $request->all();
        $query = LeaveApplication::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
             // AWS Calls
            if ($request->file('leave_letter')) {
                $letter = $request->file('leave_letter')->storePublicly('users/'.$data['user_id'].'/scholar/portfolio');
                $query->leave_letter = "https://erdt.s3.us-east-1.amazonaws.com/{$letter}";
            } else {
                $query->leave_letter = $data['leave_letter'];
            }
             
             $query->leave_start = $data['leave_start'];
             $query->leave_end = $data['leave_end'];
             $query->status = $data['status'];
             
             $query->save();
            $this->response['data'] = $query;
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
    public function approveLeave(Request $request) {
        $data = $request->validate([
            // takes account details id from table
            'id' => 'required|integer',
        ]);
    
        $query = LeaveApplication::where('id', '=', $data['id'])->first();
        if(!$query){
            $this->response['error'] = "ID Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        $user = User::where('id', '=', $query->user_id)->first();
        
        $user->status = "On Leave";
        $query->status = 'Approved';

        $user->save();
        $query->save();
        $this->response['data'] =  $query;
        $this->response['status'] = 200;
        return $this->getResponse();
        
    }
    public function rejectLeave(Request $request) {
        $data = $request->validate([
            // takes account details id from table
            'id' => 'required|integer',
        ]);
    
        $query = LeaveApplication::where('id', '=', $data['id'])->first();
        if(!$query){
            $this->response['error'] = "ID Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        $query->status = 'Denied';
        $query->save();
        $this->response['data'] =  $query;
        $this->response['status'] = 200;
        return $this->getResponse();
        
    }
    public function editLeave(Request $request)
{
    // Validate input data
    $data = $request->validate([
        'id' => 'required|integer',
        'year' => 'nullable|string',
        'semester' => 'nullable|string',
        'status' => 'nullable|string',
        'comment' => 'nullable|string',
        'file' => 'nullable|array',
        'file.*' => 'nullable|file|mimes:zip,rar,pdf|max:20480', // Restrict file types and size
    ]);

    // Find the leave request by ID
    $leaveRequest = LeaveApplication::find($data['id']);

    if (!$leaveRequest) {
        return response()->json([
            'error' => 'Leave request not found',
            'status' => 404,
        ], 404);
    }

    // Update fields if new values are provided; retain existing otherwise
    $leaveRequest->year = $data['year'] ?? $leaveRequest->year;
    $leaveRequest->semester = $data['semester'] ?? $leaveRequest->semester;
    $leaveRequest->status = $data['status'] ?? $leaveRequest->status;
    $leaveRequest->comment_id = $data['comment'] ?? $leaveRequest->comment_id; // Adjust this if the column expects an ID

    // Initialize S3 storage
    $s3BaseUrl = config('app.s3_base_url');
    $leaveFiles = $request->file('file');
    $leaveUrls = [];

    // Handle multiple file uploads
    if ($request->hasFile('file')) {
    
        if ($leaveFiles && is_array($leaveFiles)) {
            foreach ($leaveFiles as $file) {
                // Store each file in AWS S3 using the specified path
                $filePath = $file->storePublicly("users/{$leaveRequest->user_id}/scholar/portfolio", 's3');
                
                // Generate a public URL for each stored file
                $leaveUrls[] = Storage::disk('s3')->url($filePath);
            }
        }
    }
    // Update file URLs in the database
    if (!empty($leaveUrls)) {
        $leaveRequest->file = json_encode($leaveUrls);
    }
    // Save the updated leave request
    $leaveRequest->save();

    // Respond with the updated leave request data
    return response()->json([
        'data' => $leaveRequest,
        'status' => 200,
    ]);
}


}
