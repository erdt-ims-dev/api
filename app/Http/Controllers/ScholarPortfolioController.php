<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

use App\Models\ScholarPortfolio;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

class ScholarPortfolioController extends APIController
{
    //
    public function create(Request $request){
        try{
        // Helpers
        $portfolioUuid = Str::uuid()->toString();
        // Init
        $data = $request->all();
        $portfolio = new ScholarPortfolio();
        $study_name = $data['study_name'];
        $s3BaseUrl = config('app.s3_base_url');
        // AWS Calls
        if($study_name != null){
            $study = $request->file('study')->storePubliclyAs('users/'.$data['user_id'].'/academic_files/portfolio/'.$study_name, $study_name.$formatted);
        }  
        // Main
        $portfolio->id = $portfolioUuid;
        $portfolio->user_id = $data['scholar_id'];
        $portfolio->study_name = $data['study_name'];
        $portfolio->study = "{$s3BaseUrl}{$study}";
        $portfolio->study_category = $data['study_category']; // "case study", "journal",
        $portfolio->publish_type = $data['publish_type']; // "local", "international"
        $portfolio->save();
        $this->response['data'] =  "Submitted";
        $this->response['details'] =  $portfolio;
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
    public function retrieveByParameter(Request $request){
        $data = $request->all();
        $response = ScholarPortfolio::where($data['col'], '=' ,$data['value'])->get();
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
            $study = $request->file('study')->storePubliclyAs('users/'.$data['user_id'].'/academic_files/portfolio/'.$data['study_name'], $data['study_name'].$formatted);

            $query->study_name = $data['study_name'];
            $query->study = "{$s3BaseUrl}{$study}";
            $query->study_category = $data['study_category']; // case study, journal, etc.
            $query->publish_type = $data['publish_type']; // 1 for local, 2 for international
            $query->save();
        }
    }
}
