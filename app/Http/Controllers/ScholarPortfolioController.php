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
        $study = $request->file('study')->storePublicly('users/'.$data['scholar_id'].'/scholar/portfolio');
        // Main
        // $portfolio->id = $portfolioUuid;
        $portfolio->study = "{$s3BaseUrl}{$study}";
        $portfolio->scholar_id = $data['scholar_id'];
        $portfolio->study_name = $data['study_name'];
        //$portfolio->study = "{$s3BaseUrl}{$study}"; 
        $portfolio->study = "{$s3BaseUrl}{$study}"; //<- remove this once on files
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
            
             $study = $request->file('study')->storePublicly('users/'.$data['scholar_id'].'/scholar/portfolio');
 
             $query->study = "https://erdt.s3.us-east-1.amazonaws.com/{$study}";
             $query->study_name = $data['study_name'];
             $query->study_category = $data['study_category']; // case study, journal, etc.
             $query->publish_type = $data['publish_type'];
             
             $query->save();
            $this->response['data'] = $query;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }
}
