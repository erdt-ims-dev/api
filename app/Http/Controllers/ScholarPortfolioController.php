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
        $portfolioUuid = Str::orderedUuid();
        // Init
        $data = $request->all();
        $portfolio = new ScholarPortfolio();
        $study_name = $data['study_name'];
        // AWS Calls
        if($study_name != null){
            $study = $request->file('study')->storePubliclyAs('users/'.$data['user_id'].'/academic_files/portfolio/'.$study_name, $study_name.$formatted);
        }  
        // Main
        $portfolio->id = $portfolioUuid;
        $portfolio->user_id = $data['user_id'];
        $portfolio->study_name = $data['study_name'];
        $portfolio->study = "https://erdt.s3.us-east-1.amazonaws.com/{$study}";
        $portfolio->study_category = $data['study_category']; // case study, journal, etc.
        $portfolio->publish_type = $data['publish_type']; // 1 for local, 2 for international
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
        $this->response['data'] = $response[0];
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
        if(!$query){
            $this->response['error'] = "Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            // AWS Calls
            $study = $request->file('study')->storePubliclyAs('users/'.$data['user_id'].'/academic_files/portfolio/'.$data['study_name'], $data['study_name'].$formatted);

            $query->study_name = $data['study_name'];
            $query->study = "https://erdt.s3.us-east-1.amazonaws.com/{$study}";
            $query->study_category = $data['study_category']; // case study, journal, etc.
            $query->publish_type = $data['publish_type']; // 1 for local, 2 for international
        }
    }
}
