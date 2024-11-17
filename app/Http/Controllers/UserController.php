<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\AccountDetails;
use App\Models\ScholarRequestApplication;
class UserController extends APIController
{
    public function retrieveAll(){
        $response = User::withTrashed()->get();;
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function paginate(Request $request){
        $limit = $request->input('limit', 10); // Default limit is 10
        $offset = $request->input('offset', 0); // Default offset is 0
        $search = $request->input('search', ''); // Get search term
        
        $query = User::where('account_type', '!=', 'admin'); // Exclude admin accounts
        
        // $query->orWhere('status', 'deactivated');
        // Filter by search term if provided
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('email', 'LIKE', '%' . $search . '%')
                ->orWhere('status', 'LIKE', '%' . $search . '%')
                ->orWhere('account_type', 'LIKE', '%' . $search . '%');
            });
        }
        // Clone the query for total count before pagination
        $total = $query->count();

        // Apply offset and limit for paginated data
        // $accounts = $query->skip($offset)->take($limit)->get();

            // Apply offset and limit for paginated data and eager load account details
        $accounts = $query->with('accountDetails:id,user_id,first_name,last_name') // Eager load account details
                        ->skip($offset)
                        ->take($limit)
                        ->get();

        // Transform the accounts to include first_name and last_name
        $accounts->transform(function($account) {
            return [
                'id' => $account->id,
                'email' => $account->email,
                'status' => $account->status,
                'account_type' => $account->account_type,
                'first_name' => $account->accountDetails->first_name ?? null, // Use null if no account details
                'last_name' => $account->accountDetails->last_name ?? null, // Use null if no account details
            ];
        });
        
        $this->response['data'] = [
            'accounts' => $accounts,
            'total' => $total,
        ];
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveOneByParameter(Request $request)    {
    
        $data = $request->all();
        $response = User::where($data['col'], '=', $data['value'])->get();
        if ($response->isNotEmpty()) {
            $this->response['data'] = $response[0];
            $this->response['status'] = 200;
        } else {
            // Handle the case where no results are found
            $this->response['error'] = "User not found";
            $this->response['status'] = 404;
        }
        return $this->getResponse();
    }
    public function retrieveOneWithData(Request $request)
    {
        $data = $request->all();
    }
    public function retrieveMultipleByParameter(Request $request)    {
    
        $data = $request->all();
        $response = User::where($data['col'], '=', $data['value'])->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveMultipleByFilter(Request $request)    {
    
        $data = $request->all();
        $response = User::where($data['col'], '!=', $data['value'])->get();
        $this->response['data'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveWithAccountDetailsWithEmail(Request $request)    {
        // receives email, searches User table for id, uses id to search AccountDetails table
        $data = $request->all();
        $response = User::where("email", '=', $data['email'])->get()->first();
        $details = AccountDetails::where("user_id", '=', $response->id)->get()->first();
        $this->response['data'] = $details;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    
    public function update(Request $request) {
        $data = $request->all();
        $query = User::find($data['id']);
        if (!$query) {
            $this->response['error'] = "Account Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if ($query) {
            $query->{$data['col']} = $data['value'];
            $query->save();
            $this->response['data'] = $query;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }
    public function updateProfile(Request $request) {
        $formData = $request->all();
    
        // Validate the request to ensure it contains the necessary fields
        $validator = Validator::make($formData, [
            'id' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Adjust mime types and max size as needed
        ]);
    
        if ($validator->fails()) {
            // Validation failed, return error response
            $this->response['error'] = $validator->errors();
            $this->response['status'] = 400;
            return $this->getError();
        }
    
        // Retrieve the user and account details based on the ID from formData
        $user = User::where('id', '=', $formData['user_id'])->first();
        $details = AccountDetails::where('user_id', '=', $user->id)->first();
    
        if (!$user ||!$details) {
            // User or account details not found
            $this->response['error'] = "User or account details not found.";
            $this->response['status'] = 404;
            return $this->getError();
        }
    
        // Check if an image file was uploaded
        $file = $request->file('image');
        if ($file) {
            // Store the image file publicly in the specified path
            $filePath = $file->storePublicly('users/'. $user->uuid. '/account_files/profile_picture');
    
            // Update the profile_picture URL in the account details
            $details->profile_picture = "https://erdt.s3.us-east-1.amazonaws.com/". $filePath;
            $details->save();
        }
    
        // Prepare the response
        $this->response['data'] = $details;
        $this->response['status'] = 200;
    
        return $this->getResponse();
    }

    public function updatePassword(Request $request) {
        $formData = $request->all();
    
        $validator = Validator::make($formData, [
            'email' => 'required|email',  // Validate email
            'new_password' => 'required|string|min:8|max:255',
        ]);
    
        if ($validator->fails()) {
            // Validation failed, return error response
            return response()->json([
                'success' => false,
                'message' => 'Invalid fields',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
    
        $user = User::where('email', '=', $formData['email'])->first();
        if (!$user) {
            // User not found
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'status' => 404
            ], 404);
        }
    
        $user->password = Hash::make($formData['new_password']);
        $user->status = 'verified'; // Update the user status to 'verified'
        $user->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Password successfully changed!',
            'status' => 200
        ], 200);
    }
    
    
    public function updateEmail(Request $request) {
        $formData = $request->all();

        // Step 1: Validate formData to ensure it contains valid email addresses
        $validator = Validator::make($formData, [
            'user_id' => 'required|integer',
            'current_email' => 'required|email',
            'new_email' => 'required|email',
        ]);

        if ($validator->fails()) {
            // Validation failed, return error response
            // $this->response['error'] = $validator->errors();
            $this->response['status'] = 400;
            return $this->getError();
        }

        // Step 2: Check if the current_email matches $user->email before updating
        $user = User::where('id', '=', $formData['user_id'])->first();
        if ($user && $user->email === $formData['current_email']) {
            $user->email = $formData['new_email'];
            $user->save();

            $query = AccountDetails::where('user_id', '=', $user->id)->first();
            $account = [
                'email' => $user['email'],
                'status' => $user['status'],
                'account_type' => $user['account_type'],
            ];
            // Prepare the response
            // dd($account);
            $this->response['details'] = $query;
            $this->response['user'] = $account;
            $this->response['status'] = 200;
        } else {
            // Current email does not match or user not found, handle accordingly
            $this->response['error'] = "Current email does not match or user not found.";
            $this->response['status'] = 404;
        }

        return $this->getResponse();
    }
    public function delete(Request $request) {
        
        $data = $request->all();
        $query = User::find($data['id']);
        if(!$query){
            $this->response['error'] = "User Not Found";
            $this->response['status'] = 401;
            return $this->getError();
        }
        if($query){
            $query->status = 'deactivated';
            $query->save();
            $query->delete();
            $this->response['data'] = true;
            $this->response['status'] = 200;
            return $this->getResponse();
        }
    }
    // statistics

    public function retrieveStatistics(Request $request)    {
    
        $data = $request->all();
        $applicant_count = User::where('account_type', '=', 'applicant')->get()->count();
        $scholar_count = User::where('account_type', '=', 'scholar')->get()->count();
        $pending_count = ScholarRequestApplication::where('status', '=', 'pending')->get()->count();
        $endorsed_count = ScholarRequestApplication::where('status', '=', 'endorsed')->get()->count();
        $total_applications = ScholarRequestApplication::count();
        $total_approved = ScholarRequestApplication::where('status', '=', 'approved')->get()->count();
        
        $statistics = [
            'applicant_count' => $applicant_count,
            'pending_count' => $pending_count,
            'endorsed_count' => $endorsed_count,
            'scholar_count' => $scholar_count,
            'total_applications' => $total_applications,
            'total_approved' => $total_approved,
        ];
        $this->response['data'] = $statistics;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function retrieveEmailAccountDetails(Request $request)    {
        // receives email, searches User table for id, uses id to search AccountDetails table
        $data = $request->all();
        $response = User::where('email', '=', $data['email'])->first();
        $details = AccountDetails::where("user_id", '=', $response->id)->get()->first();
        $this->response['details'] = $details;
        $this->response['user'] = $response;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    public function changePassword(Request $request)    {
        // receives email, searches User table for id, uses id to search AccountDetails table
        $data = $request->all();
        $response = User::where('email', '=', $data['email'])->first();
        // change password logic here
        $this->response['data'] = $details;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
}
