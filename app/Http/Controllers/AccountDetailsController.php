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
    public function create(Request $request){
        // required params should include email, password for manually created new accounts
        try{
            $data = $request->all();
            
            // Get and store files
            $fileFields = ['profile', 'birth', 'tor', 'essay', 'recommendation', 'med', 'nbi', 'notice'];
            $binaryFiles = [];
        
            foreach ($fileFields as $fieldName) {
                if ($request->hasFile($fieldName)) {
                    $file = $request->file($fieldName);
                    $binaryFiles[$fieldName] = file_get_contents($file->getRealPath());
                }
            }

            // get AuthController

            $auth = new AuthController();
            $uuidObject = $auth->newUser($data);  // This takes into account if function is called when manual creating an account through the admin panel
            $uuidString = $uuidObject->toString();
            // Create and save AccountDetails
            $details = new AccountDetails();
            $details->id = Str::orderedUuid();
            $details->user_id = $uuidString;
            $details->first_name = $data['first_name'];
            $details->middle_name = $data['middle_name'];
            $details->last_name = $data['last_name'];
            $details->profile_picture =$binaryFiles['profile'] ?? null;
            $details->birth_certificate = $binaryFiles['birth'] ?? null;
            $details->tor = $binaryFiles['tor'] ?? null;
            $details->narrative_essay = $binaryFiles['essay'] ?? null;
            $details->recommendation_letter = $binaryFiles['recommendation'] ?? null;
            $details->medical_certificate = $binaryFiles['med'] ?? null;
            $details->nbi_clearance = $binaryFiles['nbi'] ?? null;
            $details->admission_notice = $binaryFiles['notice'] ?? null;
            $details->save();
            $this->response['data'] = 'Account Details created';
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
        $query = User::find($data['id']);
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
    // Update function assumes retrieve() has been called  in the frontend, giving you all the data you need
    // Must pass all fields
    public function update(Request $request){
        $data = $request->all();
        $query = User::find($data['id']);
        dd();
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $fileFields = ['profile', 'birth', 'tor', 'essay', 'recommendation', 'med', 'nbi', 'notice'];
            $binaryFiles = [];
            
            foreach ($fileFields as $fieldName) {
                if ($request->hasFile($fieldName)) {
                    $file = $request->file($fieldName);
                    $binaryFiles[$fieldName] = file_get_contents($file->getRealPath());
                }
            }
            $query->first_name = $data['first_name'];
            $query->middle_name = $data['middle_name'];
            $query->last_name = $data['last_name'];
            $query->profile_picture =$binaryFiles['profile'] ?? null;
            $query->birth_certificate = $binaryFiles['birth'] ?? null;
            $query->tor = $binaryFiles['tor'] ?? null;
            $query->narrative_essay = $binaryFiles['essay'] ?? null;
            $query->recommendation_letter = $binaryFiles['recommendation'] ?? null;
            $query->medical_certificate = $binaryFiles['med'] ?? null;
            $query->nbi_clearance = $binaryFiles['nbi'] ?? null;
            $query->admission_notice = $binaryFiles['notice'] ?? null;
            $query->save();
        }
    }
    // alt updated method (patch)
    public function patch(Request $request, $id){
        $data = $request->all();
        $query = User::find($id);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $fields = ['first_name', 'middle_name', 'last_name', 'profile', 'birth', 'tor', 'essay', 'recommendation', 'med', 'nbi', 'notice'];
            $array = [];  
            $i = 0;
            for($i = 0 ; $i <= count($data); $i++){
                dd($data[$fields[$i]]);

                if($data[$fields[$i]] != null || undefined){
                    array_push($array, $data[$fields[$i]]);
                }
            }
            
            foreach ($fileFields as $fieldName) {
                if ($request->hasFile($fieldName)) {
                    $file = $request->file($fieldName);
                    $binaryFiles[$fieldName] = file_get_contents($file->getRealPath());
                }
            }
            // $query->first_name = $data['first_name'];
            // $query->middle_name = $data['middle_name'];
            // $query->last_name = $data['last_name'];
            // $query->profile_picture =$binaryFiles['profile'] ?? null;
            // $query->birth_certificate = $binaryFiles['birth'] ?? null;
            // $query->tor = $binaryFiles['tor'] ?? null;
            // $query->narrative_essay = $binaryFiles['essay'] ?? null;
            // $query->recommendation_letter = $binaryFiles['recommendation'] ?? null;
            // $query->medical_certificate = $binaryFiles['med'] ?? null;
            // $query->nbi_clearance = $binaryFiles['nbi'] ?? null;
            // $query->admission_notice = $binaryFiles['notice'] ?? null;
            $query->save();
        }
    }
    // This should give you an error for now
    // This assumes all data retrieve is text, but current migration has BLOB files in it
    public function retrieveByParameter(Request $request){
        $data = $request->all();
        $response = AccountDetails::where($data['col'], '=' ,$data['value'])->get();
        $this->response['data'] = $response[0];
        $this->response['status'] = 200;
        return $this->getResponse();
    }

    public function retrieveAll(Request $request){
        $response = AccountDetails::all();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }

}
