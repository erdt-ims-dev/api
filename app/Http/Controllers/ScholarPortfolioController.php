<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

use App\Models\ScholarPortfolio;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

class ScholarPortfolioController extends APIController
{
    //
    // public function create(Request $request){
    //     try{
    //     // Helpers
    //     $portfolioUuid = Str::uuid()->toString();
    //     // Init
    //     $data = $request->all();
    //     $portfolio = new ScholarPortfolio();
    //     $study_name = $data['study_name'];
    //     $s3BaseUrl = config('app.s3_base_url');
    //     // AWS Calls
    //     $study = $request->file('study')->storePublicly('users/'.$data['scholar_id'].'/scholar/portfolio');
    //     // Main
    //     // $portfolio->id = $portfolioUuid;
    //     $portfolio->study = "{$s3BaseUrl}{$study}";
    //     $portfolio->scholar_id = $data['scholar_id'];
    //     $portfolio->study_name = $data['study_name'];
    //     //$portfolio->study = "{$s3BaseUrl}{$study}"; 
    //     $portfolio->study = "{$s3BaseUrl}{$study}"; //<- remove this once on files
    //     $portfolio->study_category = $data['study_category']; // "case study", "journal",
    //     $portfolio->publish_type = $data['publish_type']; // "local", "international"
    //     $portfolio->save();
    //     $this->response['data'] =  "Submitted";
    //     $this->response['details'] =  $portfolio;
    //     $this->response['status'] = 200;
    //     return $this->getResponse();
    //     }catch(\Throwable $th){
    //         $message = $th->getMessage();
    //         $this->response['error'] = mb_convert_encoding($message, 'UTF-8', 'auto');
    //         $this->response['status'] = $th->getCode();
    //         return $this->getResponse();
    //     }
        
    // }
    public function create(Request $request)
{
    try {
        // Helpers
        $portfolioUuid = Str::uuid()->toString();
        
        // Init
        $data = $request->all();
        $portfolio = new ScholarPortfolio();
        $study_name = $data['study_name'];
        $s3BaseUrl = config('app.s3_base_url');
        
        // Handle multiple file uploads
        $studyFiles = $request->file('study');
        $studyUrls = [];
        
        // Loop through the files and store them on S3
        if ($studyFiles && is_array($studyFiles)) {
            foreach ($studyFiles as $file) {
                // Store the file on S3
                $filePath = $file->storePublicly('users/'.$data['scholar_id'].'/scholar/portfolio');
                
                // Store the full URL of the file on S3
                $studyUrls[] = "{$s3BaseUrl}{$filePath}";
            }
        }
        
        // Main
        $portfolio->study = json_encode($studyUrls); // Store array of URLs as JSON
        $portfolio->scholar_id = $data['scholar_id'];
        $portfolio->study_name = $study_name;
        $portfolio->study_category = $data['study_category']; // "case study", "journal"
        $portfolio->publish_type = $data['publish_type']; // "local", "international"
        $portfolio->save();
        
        // Response
        $this->response['data'] = "Submitted";
        $this->response['details'] = $portfolio;
        $this->response['status'] = 200;
        
        return $this->getResponse();
    } catch (\Throwable $th) {
        $message = $th->getMessage();
        $this->response['error'] = mb_convert_encoding($message, 'UTF-8', 'auto');
        $this->response['status'] = $th->getCode();
        
        return $this->getResponse();
    }
}

    public function upload(Request $request)
    {
        try {
            // Validate the input for files, file types, size, and other required fields
            $request->validate([
                'study' => 'required',
                'study.*' => 'file|mimes:zip,rar|max:10240', // 10MB max per file
                'scholar_id' => 'required|integer',
                'study_name' => 'required|string',
                'study_category' => 'required|string',
                'publish_type' => 'required|string'
            ], [
                'study.*.max' => 'Each file must be less than 10MB.',
                'study.*.mimes' => 'Only ZIP and RAR files are allowed.'
            ]);

            // Initialize required data
            $data = $request->all();
            $portfolio = new ScholarPortfolio();
            $studyUrls = [];

            // Handle multiple file uploads
            $studyFiles = $request->file('study');
            if ($studyFiles && is_array($studyFiles)) {
                foreach ($studyFiles as $file) {
                    // Store each file in AWS S3 using the specified path
                    $filePath = $file->storePublicly("users/{$data['scholar_id']}/scholar/portfolio", 's3');
                    
                    // Generate a public URL for each stored file
                    $studyUrls[] = Storage::disk('s3')->url($filePath);
                }
            }

            // Save portfolio details in the database
            $portfolio->study = json_encode($studyUrls); // Store URLs as JSON array
            $portfolio->scholar_id = $data['scholar_id'];
            $portfolio->study_name = $data['study_name'];
            $portfolio->study_category = $data['study_category'];
            $portfolio->publish_type = $data['publish_type'];
            $portfolio->save();

            // Build response structure
            $this->response['data'] = "Submitted";
            $this->response['details'] = $portfolio;
            $this->response['status'] = 200;

            return $this->getResponse();

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation error messages
            $this->response['error'] = $e->errors();
            $this->response['status'] = 422;

            return $this->getResponse();
        } catch (\Throwable $th) {
            // General error handling
            $this->response['error'] = $th->getMessage();
            $this->response['status'] = $th->getCode() ?: 500;

            return $this->getResponse();
        }
    }
    

    public function delete(Request $request){
        $data = $request->all();
        $query = ScholarPortfolio::find($data['id']);
        if(!$query){
            $this->response['error'] = "Not Found";
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
    public function retrieveByEmail(Request $request)    {
    
        $data = $request->all();
        $response = ScholarPortfolio::where('email', '=', $data['email'])->get();
        $this->response['data'] = $response[0];
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveOneByParameter(Request $request)    {
    
        $data = $request->all();
        $response = ScholarPortfolio::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response[0];
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveMultipleByParameter(Request $request)    {
    
        $data = $request->all();
        $response = ScholarPortfolio::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveAll(Request $request){
        $response = ScholarPortfolio::all();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function update(Request $request){
        $data = $request->all();
        $query = ScholarTasks::find($data['id']);
        $s3BaseUrl = config('app.s3_base_url');
        if(!$query){
            $this->response['error'] = "Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            // AWS Calls
            $study = $request->file('study')->storePublicly('users/'.$data['user_id'].'/scholar/portfolio');
            
            $query->study = "{$s3BaseUrl}{$study}";
            $query->study_name = $data['study_name'];
            $query->study_category = $data['study_category']; // case study, journal, etc.
            $query->publish_type = $data['publish_type']; // 1 for local, 2 for international
            $query->save();
        }
    }
    public function updateOne(Request $request){
        $data = $request->all();
        $query = ScholarPortfolio::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
             // AWS Calls
            if ($request->file('study') !== null){
                $study = $request->file('study')->storePublicly('users/'.$data['scholar_id'].'/scholar/portfolio');
                $query->study = "https://erdt.s3.us-east-1.amazonaws.com/{$study}";
            } else {
                $query->study = $data['study'];
            }

            $query->study_name = $data['study_name'];
            $query->study_category = $data['study_category']; // case study, journal, etc.
            $query->publish_type = $data['publish_type'];
            
            $query->save();
            $this->response['data'] = $query;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }
    public function uploadEdit(Request $request)
    {
        $data = $request->all();
        $query = ScholarPortfolio::find($data['id']);

        if (!$query) {
            $this->response['error'] = "Portfolio Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }

        if ($query) {
            // Initialize file URLs array
            $fileUrls = [];

            // If new files are uploaded, process and store each one
            if ($request->hasFile('study')) {
                foreach ($request->file('study') as $file) {
                    $path = $file->storePublicly('users/' . $data['scholar_id'] . '/scholar/portfolio');
                    $fileUrls[] = "https://erdt.s3.us-east-1.amazonaws.com/{$path}";
                }
            } else {
                // If no new files, retain current value of `study` as a JSON array
                $fileUrls = json_decode($query->study) ?? [];
            }

            // Update only if new values are provided, otherwise keep current values
            $query->study = json_encode($fileUrls);
            $query->study_name = $data['study_name'] ?? $query->study_name;
            $query->study_category = $data['study_category'] ?? $query->study_category;
            $query->publish_type = $data['publish_type'] ?? $query->publish_type;

            // Save the updated record
            $query->save();

            // Prepare response
            $this->response['data'] = $query;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }


}
