<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
class StaffController extends APIController
{
    // assumes created manually
    function create(Request $request){
        $valid = RequestValidatorServiceProvider::registerValidator($data);
        if ($valid) {
            return $this->insertNew($data);
        }else{
            $this->response['error'] = $valid['error'];
            $this->response['status'] = 401;
            return $this->getResponse();
        }
    }
    public function insertNew($data)
    {
        try {
            $userUuid = Str::orderedUuid();
            $accountDetailUuid = Str::orderedUuid();

            $user = new User();
            $user->id = $userUuid;
            $user->email = $data['email'];
            $user->account_type = 'staff';
            $user->password = Hash::make($data['password']);
            $user->status = 'verified';
            $user->save();

            $accountDetails = new AccountDetails();
            $accountDetails->id = $accountDetailUuid;
            $accountDetails->user_id = $userUuid;
            $accountDetails->first_name = $data['first_name'];
            $accountDetails->middle_name = '';
            $accountDetails->profile_picture = '';
            $accountDetails->last_name = $data['last_name'];
            $accountDetails->save();

            $this->response['data'] = 'User registered';
            return $this->getResponse();
        } catch (\Throwable $th) {
            $this->response['error'] = $th->getMessage();
            $this->response['status'] = $th->getCode();
            return $this->getResponse();
        }
    }
}
