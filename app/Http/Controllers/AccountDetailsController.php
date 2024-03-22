<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

use App\Models\User;
use App\Models\AccountDetails;

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
    
    // use user id? or use id itself when finding what to update?
    // gonna add both methods
    public function updateByParameter(Request $request) {
        $data = $request->validate([
            'user_id' => 'required|integer',
            'file' => 'required|file',
            'field' => 'required|string',
        ]);
    
        $query = AccountDetails::find($data['user_id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
    
        if ($request->hasFile($data['file'])) {
            $file = $request->file($data['file'])->storePublicly('users/' . $data['user_id'] . '/account_files/' . $data['field'] . '/');
            $query->{$data['field']} = "https://erdt.s3.us-east-1.amazonaws.com/$file";
            $query->save();
            return response()->json(['data' => "Submitted", 'details' => $query, 'status' => 200]);
        } else {
            $this->response['error'] = "Upload Failed";
            $this->response['status'] = 401;
            return $this->getError();
        }
    }
    
    public function updateProgram(Request $request){
        $data = $request->all();
        $query = AccountDetails::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            // AWS
            if($request->hasFile('program')){
                $file = $request->file('program')->storePublicly('users/'.$data['user_id'].'/account_files/birth/');
                $query->profile_picture ="https://erdt.s3.us-east-1.amazonaws.com/$file"; 
                $this->response['data'] =  "Submitted";
                $this->response['details'] =  $query;
                $this->response['status'] = 200;
            }else{
                $this->response['data'] =  "failed";
                $this->response['details'] =  $query;
                $this->response['status'] = 402;
            }
        }
    }
    public function updateBirth(Request $request){
        $data = $request->all();
        $query = AccountDetails::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            // AWS
            if($request->hasFile('birth')){
                $file = $request->file('birth')->storePublicly('users/'.$data['user_id'].'/account_files/birth/');
                $query->profile_picture ="https://erdt.s3.us-east-1.amazonaws.com/$file"; 
                $this->response['data'] =  "Submitted";
                $this->response['details'] =  $query;
                $this->response['status'] = 200;
            }else{
                $this->response['data'] =  "failed";
                $this->response['details'] =  $query;
                $this->response['status'] = 402;
            }
        }
    }
    public function updateTor(Request $request){
        $data = $request->all();
        $query = AccountDetails::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            // AWS
            if($request->hasFile('tor')){
                $file = $request->file('tor')->storePublicly('users/'.$data['user_id'].'/account_files/tor/');
                $query->tor ="https://erdt.s3.us-east-1.amazonaws.com/$file"; 
                $query->save();
                $this->response['data'] =  "Submitted";
                $this->response['details'] =  $query;
                $this->response['status'] = 200;
            }else{
                $this->response['data'] =  "failed";
                $this->response['details'] =  $query;
                $this->response['status'] = 402;
            }
        }
    }
    public function updateEssay(Request $request){
        $data = $request->all();
        $query = AccountDetails::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            // AWS
            if($request->hasFile('essay')){
                $file = $request->file('essay')->storePublicly('users/'.$data['user_id'].'/account_files/essay/');
                $query->narrative_essay ="https://erdt.s3.us-east-1.amazonaws.com/$file"; 
                $query->save();
                $this->response['data'] =  "Submitted";
                $this->response['details'] =  $query;
                $this->response['status'] = 200;
            }else{
                $this->response['data'] =  "failed";
                $this->response['details'] =  $query;
                $this->response['status'] = 402;
            }
        }
    }
    public function updateRecommendation(Request $request){
        $data = $request->all();
        $query = AccountDetails::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            // AWS
            if($request->hasFile('recommendation')){
                $file = $request->file('essay')->storePublicly('users/'.$data['user_id'].'/account_files/recommendation/');
                $query->recommendation_letter ="https://erdt.s3.us-east-1.amazonaws.com/$file"; 
                $query->save();
                $this->response['data'] =  "Submitted";
                $this->response['details'] =  $query;
                $this->response['status'] = 200;
            }else{
                $this->response['data'] =  "failed";
                $this->response['details'] =  $query;
                $this->response['status'] = 402;
            }
        }
    }
    public function updateMedical(Request $request){
        $data = $request->all();
        $query = AccountDetails::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            // AWS
            if($request->hasFile('medical')){
                $file = $request->file('medical')->storePublicly('users/'.$data['user_id'].'/account_files/medical/');
                $query->medical_certificate ="https://erdt.s3.us-east-1.amazonaws.com/$file"; 
                $query->save();
                $this->response['data'] =  "Submitted";
                $this->response['details'] =  $query;
                $this->response['status'] = 200;
            }else{
                $this->response['data'] =  "failed";
                $this->response['details'] =  $query;
                $this->response['status'] = 402;
            }
        }
    }
    public function updateNBI(Request $request){
        $data = $request->all();
        $query = AccountDetails::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            // AWS
            if($request->hasFile('nbi')){
                $file = $request->file('medical')->storePublicly('users/'.$data['user_id'].'/account_files/nbi/');
                $query->nbi_clearance ="https://erdt.s3.us-east-1.amazonaws.com/$file"; 
                $query->save();
                $this->response['data'] =  "Submitted";
                $this->response['details'] =  $query;
                $this->response['status'] = 200;
            }else{
                $this->response['data'] =  "failed";
                $this->response['details'] =  $query;
                $this->response['status'] = 402;
            }
        }
    }
    public function updateNotice(Request $request){
        $data = $request->all();
        $query = AccountDetails::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            // AWS
            if($request->hasFile('notice')){
                $file = $request->file('medical')->storePublicly('users/'.$data['user_id'].'/account_files/notice/');
                $query->admission_notice ="https://erdt.s3.us-east-1.amazonaws.com/$file"; 
                $query->save();
                $this->response['data'] =  "Submitted";
                $this->response['details'] =  $query;
                $this->response['status'] = 200;
            }else{
                $this->response['data'] =  "failed";
                $this->response['details'] =  $query;
                $this->response['status'] = 402;
            }
        }
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
