<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

use App\Models\User;
use App\Models\AccountDetails;
use App\Models\ScholarRequestApplication;

use Carbon\Carbon;

use App\Http\Controllers\AuthController;


class AccountDetailsController extends APIController
{
    // 2-20-24 update statements are not yet updated in routes
    public function create(Request $request){
        // required params should include email, password for manually created new accounts
        try{
            $data = $request->all();
            $s3BaseUrl = config('app.s3_base_url');
            // get AuthController

            $auth = new AuthController();
            $uuidObject = $auth->newUser($data);  // This takes into account if function is called when manual creating an account through the admin panel
            $uuidString = $uuidObject->toString();
            // Create and save AccountDetails
            $details = new AccountDetails();
            // $details->id = Str::uuid()->toString();
            // $details->user_id = $uuidString;
            
            // AWS Calls - Return paths to file on S3 bucket
            $pfp = $request->file('profile')->storePublicly('users/'.$uuidString.'/account_files/profile/');
            $birth = $request->file('birth')->storePublicly('users/'.$uuidString.'/account_files/birth/');
            $tor = $request->file('tor')->storePublicly('users/'.$uuidString.'/account_files/tor/');
            $essay = $request->file('essay')->storePublicly('users/'.$uuidString.'/account_files/essay/');
            $recommendation = $request->file('recommendation')->storePublicly('users/'.$uuidString.'/account_files/recommendation/');
            $medical = $request->file('medical')->storePublicly('users/'.$uuidString.'/account_files/medical/');
            $nbi = $request->file('nbi')->storePublicly('users/'.$uuidString.'/account_files/nbi/');
            $notice = $request->file('notice')->storePublicly('users/'.$uuidString.'/account_files/notice/');

            $details->first_name = $data['first_name'];
            $details->middle_name = $data['middle_name'];
            $details->last_name = $data['last_name'];
            // Account Type - 0: Admin 1: Project Coord 2: Staff 3: Scholar
            $details->account_type = $data['account_type'];
            
            $details->profile_picture ="{$s3BaseUrl}{$pfp}" ?? null; // Still not sure if I should outright store the pfp image here or just use img.src that points to the AWS file in Frontend. Will do the rnedentring on FE for now
            $details->birth_certificate = "{$s3BaseUrl}{$birth}"  ?? null;
            $details->tor = "{$s3BaseUrl}{$tor}"  ?? null;
            $details->narrative_essay ="{$s3BaseUrl}{$essay}"  ?? null;
            $details->recommendation_letter = "{$s3BaseUrl}{$recommendation}"  ?? null;
            $details->medical_certificate = "{$s3BaseUrl}{$medical}"  ?? null;
            $details->nbi_clearance = "{$s3BaseUrl}{$nbi}"  ?? null;
            $details->admission_notice = "{$s3BaseUrl}{$notice}"  ?? null;
            $details->save();
            $this->response['data'] = 'Account Details created';
            $this->response['details'] = $details;
            return $this->getResponse();
        }catch (\Throwable $th){
            $message = $th->getMessage();
            $this->response['error'] = mb_convert_encoding($message, 'UTF-8', 'auto');
            $this->response['status'] = $th->getCode();
            return $this->getResponse();
        }
        
    }
    public function delete(Request $request){
        $data = $request->all();
        $query = AccountDetails::find($data['id']);
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
    public function updateDetails(Request $request){
        $data = $request->all();
        $query = AccountDetails::find($data['id']);
        if(!$query){
            $this->response['error'] = "Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $query->first_name = $data['first_name'];
            $query->middle_name = $data['middle_name'];
            $query->last_name = $data['last_name'];
            $query->program = $data['program'];
            $query->save();
            $this->response['data'] =  "created";
            $this->response['details'] =  $query;
            $this->response['status'] = 200;
        }
    }
    
    // Upload in bulk
    public function updateByParameter(Request $request) {
        $data = $request->validate([
            'user_id' => 'required|integer',
        ]);
        // Update UserTable's account_type
        $user = User::where('id', '=', $data['user_id'])->first();
        $user->account_type = "applicant";

        $query = AccountDetails::find($data['user_id']);
        if (!$query) {
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
    
        // Iterate over each file in the request
        foreach ($request->allFiles() as $field => $file) {
            if ($file) {
                $filePath = $file->storePublicly('users/' . $user->uuid . '/account_files/' . $field);
                $query->{$field} = "https://erdt.s3.us-east-1.amazonaws.com/$filePath";
            }
        }
        
        // Create new entry on ScholarRequestApplication
        $application = new ScholarRequestApplication();
        $application->account_details_id = AccountDetails::find($data['user_id'])->id;
        $application->scholar_id = null;
        $application->status = 'pending';
        
        $user->save();
        $application->save();
        $query->save();
        return response()->json(['data' => "Submitted", 'details' => $query, 'status' => 200]);
    }

    public function uploadNewFiles(Request $request) {
        $data = $request->validate([
            'user_id' => 'required|integer',
        ]);
        // Update UserTable's account_type
        $user = User::where('id', '=', $data['user_id'])->first();

        $query = AccountDetails::find($data['user_id']);
        if (!$query) {
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
    
        // Iterate over each file in the request
        foreach ($request->allFiles() as $field => $file) {
            if ($file) {
                $filePath = $file->storePublicly('users/' . $user->uuid . '/account_files/' . $field);
                $query->{$field} = "https://erdt.s3.us-east-1.amazonaws.com/$filePath";
            }
        }
        $query->save();
        return response()->json(['data' => "Submitted", 'details' => $query, 'status' => 200]);
    }


    

    public function retrieveAll(Request $request){
        $response = AccountDetails::all();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveOneByParameter(Request $request)    {
    
        $data = $request->all();
        $response = AccountDetails::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response[0];
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveMultipleByParameter(Request $request)    {

        $data = $request->all();
        $response = AccountDetails::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
}
