<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

use App\Models\ScholarTasks;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

class ScholarTasksController extends APIController
{
    //
    public function create(Request $request)
{
    try {
        // Validate incoming request
        // $request->validate([
        //     'file' => 'required|array|min:1|max:5', // Ensure at least 1 file, max 5
        //     'file.*' => 'file|mimes:zip,rar|max:10240', // Each file must be less than 10MB
        //     'semester' => 'required|integer',
        //     'type' => 'required|string',
        //     'year' => 'required|integer',
        // ], [
        //     'file.*.max' => 'Each file must be less than 10MB.',
        //     'file.*.mimes' => 'Only ZIP and RAR files are allowed.',
        //     'file.array' => 'You must upload an array of files.',
        //     'file.min' => 'At least one file is required.',
        //     'file.max' => 'You can upload up to 5 files only.',
        // ]);

        // Extract data
        $data = $request->all();

        // Create a new ScholarTask instance
        $scholar = new ScholarTasks();
        $scholar->scholar_id = $data['scholar_id'];
        $scholar->year = $data['year'];
        $scholar->semester = $data['semester'];
        $scholar->type = $data['type'];
        // $scholar->approval_status = 'pending';

        // Initialize S3 storage
        $s3BaseUrl = config('app.s3_base_url');
        $taskFiles = $request->file('file');
        $taskUrls = [];

        // Loop through the files and store them on S3
        foreach ($taskFiles as $file) {
            $filePath = $file->storePublicly('users/'.$data['scholar_id'].'/scholar/tasks', 's3');
            $taskUrls[] = Storage::disk('s3')->url($filePath); // Store the URL
        }

        // Store the file URLs as a JSON array in the database
        $scholar->file = json_encode($taskUrls);
        $scholar->save();

        // Prepare the response
        $this->response['data'] = [
            'id' => $scholar->id,
            'scholar_id' => $scholar->scholar_id,
            'year' => $scholar->year,
            'semester' => $scholar->semester,
            'type' => $scholar->type,
            'file' => $taskUrls,  // Return the list of file URLs
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

    public function delete(Request $request){
        $data = $request->all();
        $query = ScholarTasks::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
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

    public function update(Request $request) {
        // Validate the incoming request data
        // $request->validate([
        //     'file' => 'required',
        //     'file.*' => 'file|mimes:zip,rar|max:10240', // Ensure the files are ZIP or RAR and less than 10MB
        //     'scholar_id' => 'required|exists:scholar_tasks,scholar_id', // Make sure scholar_id exists
        //     'year' => 'required|integer',
        //     'semester' => 'required|string', // Assuming 'semester' is a string
        //     'type' => 'required|string',
        // ]);
    
        // Initialize the data
        $data = $request->all();
        $task = ScholarTasks::find($data['scholar_id']);
    
        // If task is not found, return an error response
        if (!$task) {
            $this->response['error'] = "Task not found";
            $this->response['status'] = 404;
            return $this->getError();
        }
    
        // Process the files if they exist
        if ($request->hasFile('file')) {
            // Store the file(s) in S3 and create URLs
            $files = $request->file('file');
            $fileUrls = [];
            
            foreach ($files as $file) {
                // Store each file in S3 (ensure the path is correct)
                $filePath = $file->storePublicly('users/' . $data['scholar_id'] . '/scholar/tasks', 's3');
                
                // Add the URL to the array
                $fileUrls[] = Storage::disk('s3')->url($filePath);
            }
    
            // Store the file URLs as a JSON array in the DB
            $task->file = json_encode($fileUrls);
        }
    
        // Update the other task fields
        $task->year = $data['year'];
        $task->semester = $data['semester'];
        $task->type = $data['type'];
        // $task->approval_status = $data['status'] ?? 'pending'; // Default approval status to 'pending' if not provided
    
        // Save the updated task
        $task->save();
    
        // Return a success response
        $this->response['data'] = "Task updated successfully";
        $this->response['details'] = $task;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    
    public function updateOne (Request $request)    {
        $data = $request->all();
        $query = ScholarTasks::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
             // AWS Calls
             if ($request->file('midterm_assessment') !== null){
                $midterm = $request->file('midterm_assessment')->storePublicly('users/'.$data['scholar_id'].'/scholar/tasks');
                $query->midterm_assessment = "https://erdt.s3.us-east-1.amazonaws.com/{$midterm}";
            } else {
                $query->midterm_assessment = $data['midterm_assessment'];
            }
            if ($request->file('final_assessment') !== null){
                $final = $request->file('final_assessment')->storePublicly('users/'.$data['scholar_id'].'/scholar/tasks');
                $query->final_assessment = "https://erdt.s3.us-east-1.amazonaws.com/{$final}";
            } else {
                $query->final_assessment = $data['final_assessment'];
            }
             
             // Shove the generated links to DB
             $query->approval_status = $data['approval_status'] ?? false;
             $query->save();
            $this->response['data'] = $query;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }
    public function retrieveOneByParameter(Request $request)    {
    
        $data = $request->all();
        $response = ScholarTasks::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response[0];
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveMultipleByParameter(Request $request)    {
    
        $data = $request->all();
        $response = ScholarTasks::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveAll(Request $request){
        $response = ScholarTasks::all();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
}
