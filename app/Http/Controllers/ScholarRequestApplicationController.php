<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use Illuminate\Support\Str;

use App\Models\ScholarRequestApplication;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

class ScholarRequestApplicationController extends APIController
{
    //
    public function create(Request $request){
        try{
        // Helpers
        $applicationUuid = Str::uuid()->toString();
        // Init
        $data = $request->all();
        $application = new ScholarRequestApplication();
        $application->id = $applicationUuid;

        // main
        $application->account_details_id = $data['account_details_id'];
        $application->user_id = $data['user_id'];
        $application->status = 0;
        $application->comment_id = null;
        $application->save();
        $this->response['data'] =  "created";
        $this->response['details'] =  $application;
        $this->response['status'] = 200;
        return $this->getResponse();
        }catch(\Throwable $th){
            $message = $th->getMessage();
            $this->response['error'] = mb_convert_encoding($message, 'UTF-8', 'auto');
            $this->response['status'] = $th->getCode();
            return $this->getResponse();
        }
    }
    public function delete(Request $request){
        $data = $request->all();
        $query = ScholarRequestApplication::find($data['id']);
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
    public function retrieveByParameter(Request $request){
        $data = $request->all();
        $response = ScholarRequestApplication::where($data['col'], '=' ,$data['value'])->get();
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
    public function retrieveAll(Request $request){
        $response = ScholarRequestApplication::all();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }

    public function update(Request $request){
        $data = $request->all();
        $query = ScholarTasks::find($data['id']);
        if(!$query){
            $this->response['error'] = "Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
        $query->user_id = $data['user_id'];
        $query->status = $data['status'];
        $query->comment_id = $data['comment_id'];
        $query->save();
        $this->response['data'] =  "created";
        $this->response['details'] =  $query;
        $this->response['status'] = 200;
        }
    }
    
}
