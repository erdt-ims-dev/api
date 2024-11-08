<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use Illuminate\Support\Str;

use App\Models\ScholarRequestApplication;
use App\Models\AccountDetails;
use App\Models\User;
use App\Models\Scholar;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        // $application->id = $applicationUuid;

        // main
        $application->account_details_id = $data['account_details_id'];
        $application->scholar_id = null;
        $application->status = 'pending';
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
        $query = ScholarRequestApplication::where('account_details_id', '=', $data['id'])->first();;
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
    public function reject(Request $request){
        $data = $request->all();
        $query = ScholarRequestApplication::where('account_details_id', '=', $data['id'])->first();;
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $query->status = 'rejected';
            $query->save();
            $this->response['data'] = true;
            $this->response['status'] = 200;
            return $this->getResponse();
        }

    }
    public function retrieveOneByParameter(Request $request)    {
    
        $data = $request->all();
        $response = ScholarRequestApplication::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response[0];
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveMultipleByParameter(Request $request)    {
    
        $data = $request->all();
        $response = ScholarRequestApplication::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
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
        $query = ScholarRequestApplication::find($data['id']);
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
            return $this->getResponse();
        }
    }
    // Custom methods
    public function updateToEndorsed(Request $request) {
        $data = $request->validate([
            'id' => 'required|integer',
        ]);
        
        $query = ScholarRequestApplication::where('account_details_id', '=', $data['id'])->first();
        if(!$query){
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        $query->status = 'endorsed';
        $query->save();
        $this->response['data'] =  $query;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function approveApplicant(Request $request) {
        $data = $request->validate([
            // takes account details id from table
            'id' => 'required|integer',
        ]);
    
        $query = ScholarRequestApplication::where('account_details_id', '=', $data['id'])->first();
        if(!$query){
            $this->response['error'] = "ID Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query->status == 'endorsed'){
            $query->status = 'approved';
            $query->save();
        }else{
            $this->response['error'] = "Applicant is not endorsed";
            $this->response['status'] = 401;
            return $this->getError();
        }
        
        // find user using account details table
        $details = AccountDetails::where('id', '=', $query->account_details_id)->first();
        // create new scholar entry
        if ($details) {
            $res = new Scholar();
            $res->user_id = $details->user_id;
            $res->save();
            $query->scholar_id = $res->id;
            $query->save();

            $user = User::where('id', '=', $data['id'])->first();
            $user->account_type = 'scholar';
            $user->save();
            $this->response['data'] =  $query;
            $this->response['status'] = 200;
            return $this->getResponse();
        } else {
            $this->response['error'] = "Account Details Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        
    }
    public function retrievePendingTableAndDetail(Request $request)    {
        // where status = pending
        $data = $request->all();
        $response = ScholarRequestApplication::where('status', '=', 'pending')->get();

        $query = [];

        foreach ($response as $item) {
            $accountDetail = AccountDetails::find($item->account_details_id);

            if ($accountDetail) {
                $query[] = [
                    'list' => $item,
                    'details' => $accountDetail
                ];
            }
        }

        $this->response['data'] = $query;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function paginate(Request $request) {
        $limit = $request->input('limit', 10); // Default limit is 10
        $offset = $request->input('offset', 0); // Default offset is 0
    
        // Initial query with status filter
        $query = ScholarRequestApplication::where('status', '=', 'pending');
    
        // Paginate results
        $total = $query->count(); // Get total count before pagination
        $response = $query->skip($offset)->take($limit)->get();
    
        // Prepare data with account details
        $paginatedData = [];
        foreach ($response as $item) {
            $accountDetail = AccountDetails::find($item->account_details_id);
            if ($accountDetail) {
                $paginatedData[] = [
                    'list' => $item,
                    'details' => $accountDetail,
                ];
            }
        }
    
        // Response with paginated data
        $this->response['data'] = [
            'items' => $paginatedData,
            'total' => $total,
        ];
        $this->response['status'] = 200;
    
        return $this->getResponse();
    }
    public function paginateEndorsed(Request $request) {
        $limit = $request->input('limit', 10); // Default limit is 10
        $offset = $request->input('offset', 0); // Default offset is 0
    
        // Initial query with status filter
        $query = ScholarRequestApplication::where('status', '=', 'endorsed');
    
        // Paginate results
        $total = $query->count(); // Get total count before pagination
        $response = $query->skip($offset)->take($limit)->get();
    
        // Prepare data with account details
        $paginatedData = [];
        foreach ($response as $item) {
            $accountDetail = AccountDetails::find($item->account_details_id);
            if ($accountDetail) {
                $paginatedData[] = [
                    'list' => $item,
                    'details' => $accountDetail,
                ];
            }
        }
    
        // Response with paginated data
        $this->response['data'] = [
            'items' => $paginatedData,
            'total' => $total,
        ];
        $this->response['status'] = 200;
    
        return $this->getResponse();
    }
    
    public function retrieveEndorsedTableAndDetail(Request $request)    {
        // where status = endorsed
        $data = $request->all();
        $response = ScholarRequestApplication::where('status', '=', 'endorsed')->get();

        $query = [];

        foreach ($response as $item) {
            $accountDetail = AccountDetails::find($item->account_details_id);

            if ($accountDetail) {
                $query[] = [
                    'list' => $item,
                    'details' => $accountDetail
                ];
            }
        }

        $this->response['data'] = $query;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveUserApplications(Request $request) {
        // where status = endorsed
        $data = $request->all();
        $response = ScholarRequestApplication::where('account_details_id', '=', $data['id'])
            ->select(['*']) // Select all columns
            ->get();
    
        // Use Carbon to format the created_at date
        foreach ($response as $item) {
            $item->created_at = \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
        }
    
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }

}
