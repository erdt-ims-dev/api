<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

use App\Models\ScholarTasks;
use App\Models\User;

use Illuminate\Http\Request;

class ScholarTasksController extends APIController
{
    //
    public function create(Request $request){
        try{
        $scholarUuid = Str::orderedUuid();
        // $userUuid = Str::orderedUuid();

        $data = $request->all();
        $scholar = new ScholarTasks();
        $scholar->id = $scholarUuid;
        $scholar->user_id = $data['user_id']; // user_id comes from Frontend
        // AWS Calls
        $midterm = $request->file('midterm_assessment')->storePublicly('public/files');
        $finals = $request->file('final_assessment')->storePublicly('public/files');
        // Shove the generated links to DB
        $scholar->midterm_assessment = "https://erdt.s3.us-east-1.amazonaws.com/$midterm";
        $scholar->final_assessment = "https://erdt.s3.us-east-1.amazonaws.com/$finals";
        $scholar->approval_status = false;
        $scholar->save();
        $this->response['data'] =  "Submitted";
        $this->response['detils'] =  $scholar;
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
    public function update(Request $request){
        $data = $request->all();
        $query = ScholarTasks::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            // idk how tf its gonna update or patch in AWS side. 
            // Needs research
            // maybe dont delete and create another folder for past records?    
            
            $query->midterm_assessment =$binaryFiles['midterm_assessment'] ?? null;
            $query->final_assessment = $binaryFiles['final_assessment'] ?? null;
            $query->save();
        }
    }
    public function retrieveByParameter(Request $request){
        $data = $request->all();
        $response = ScholarTasks::where($data['col'], '=' ,$data['value'])->get();
        $this->response['data'] = $response[0];
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
