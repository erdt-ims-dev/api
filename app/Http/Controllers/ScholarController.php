<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Scholar;
use App\Models\User;

class ScholarController extends APIController
{
    //
    public function create(Request $request){
        $data = $request->all();
        $query = new Scholar();
        // $query->id = Str::uuid()->toString();
        $query->user_id = $data['user_id'];
        $query->scholar_request_id = $data['scholar_request_id'];
        $query->scholar_task_id = $data['scholar_task_id'];
        $query->scholar_portfolio_id = $data['scholar_portfolio_id'];
        $query->scholar_leave_app_id = $data['scholar_leave_app_id'];
        $query->save();

    }
    public function delete(Request $request){
        $data = $request->all();
        $query = Scholar::find($data['id']);
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
        $response = Scholar::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response[0];
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveMultipleByParameter(Request $request)    {
    
        $data = $request->all();
        $response = Scholar::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveAll(Request $request){
        //$response = Scholar::all();
        $response = User::where('account_type', '=', 'scholar')->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function filterRetrieve(Request $request)
{
    $data = $request->all();
    $semester = $data['semester'] ?? null;
    $year = $data['year'] ?? null;

    if (!$semester || !$year) {
        $this->response['data'] = [];
        $this->response['status'] = 400;
        $this->response['message'] = "Semester and year are required.";
        return $this->getResponse();
    }

    // Define date ranges based on semester
    switch ($semester) {
        case '1st semester':
            $startDate = "$year-01-01";
            $endDate = "$year-04-30";
            break;
        case '2nd semester':
            $startDate = "$year-08-01";
            $endDate = "$year-11-30";
            break;
        case 'Summer semester':
            $startDate = "$year-05-01";
            $endDate = "$year-07-31";
            break;
        default:
            $this->response['data'] = [];
            $this->response['status'] = 400;
            $this->response['message'] = "Invalid semester.";
            return $this->getResponse();
    }

    // Retrieve users within the date range
    $response = User::where('account_type', 'scholar')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

    $this->response['data'] = $response;
    $this->response['status'] = 200;
    return $this->getResponse();
}

    public function update(Request $request){
        $data = $request->all();
        $query = Scholar::find($data['id']);
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
        $query->user_id = $data['user_id'];
        $query->scholar_request_id = $data['scholar_request_id'];
        $query->scholar_task_id = $data['scholar_task_id'];
        $query->scholar_portfolio_id = $data['scholar_portfolio_id'];
        $query->scholar_leave_app_id = $data['scholar_leave_app_id'];
        $query->save();
        }
    }
}
