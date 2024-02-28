<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

use App\Models\ScholarTasks;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

class ScholarTasksController extends APIController
{
    //
    public function create(Request $request){
        try{
        
        // Helpers
        $scholarUuid = Str::uuid()->toString();
        // $userUuid = Str::orderedUuid();

        $time = Carbon::now();
        $formatted = $time->format('Y-m-d');

        // Init
        $data = $request->all();
        $scholar = new ScholarTasks();
        // $scholar->id = $scholarUuid;
        $scholar->user_id = $data['user_id']; // user_id comes from Frontend
        $s3BaseUrl = config('app.s3_base_url');

        // AWS Calls
        $midterm = $request->file('midterm_assessment')->storePubliclyAs('users/'.$data['user_id'].'/academic_files/asessments', "midterm_asessment_".$formatted);
        $finals = $request->file('final_assessment')->storePubliclyAs('users/'.$data['user_id'].'/academic_files/asessments', "final_assessment_".$formatted);
        
        // Shove the generated links to DB
        $scholar->midterm_assessment = "{$s3BaseUrl}{$midterm}";
        $scholar->final_assessment = "{$s3BaseUrl}{$finals}";
        $scholar->approval_status = false;
        $scholar->save();
        $this->response['data'] =  "Submitted";
        $this->response['details'] =  $scholar;
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
            $this->response['error'] = "Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            // idk how tf its gonna update or patch in AWS side. 
            // Needs research
            // maybe dont delete and create another folder for past records?    1/22/24

            // Will go for versioning, by default i made it so that there's already a time attached to the file name
            // Update will create a new one and return the latest generated files. 1/25/24

            // AWS Calls
            $midterm = $request->file('midterm_assessment')->storePubliclyAs('users/'.$data['user_id'].'/academic_files/asessments', "midterm_asessment_".$formatted);
            $finals = $request->file('final_assessment')->storePubliclyAs('users/'.$data['user_id'].'/academic_files/asessments', "final_assessment_".$formatted);
            
            // Shove the generated links to DB
            $query->midterm_assessment = "https://erdt.s3.us-east-1.amazonaws.com/{$midterm}";
            $query->final_assessment = "https://erdt.s3.us-east-1.amazonaws.com/{$finals}";
            $query->approval_status = $data['status'] ?? false;
            $scholar->save();
            


            // $query->midterm_assessment =$binaryFiles['midterm_assessment'] ?? null;
            // $query->final_assessment = $binaryFiles['final_assessment'] ?? null;
            $query->save();
        }
    }
    public function retrieveByParameter(Request $request){
        $data = $request->all();
        $response = ScholarTasks::where($data['col'], '=' ,$data['value'])->get();
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
        $response = ScholarTasks::all();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
}
