<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Providers\RequestValidatorServiceProvider;
use Laravel\Sanctum\Sanctum;

use App\Models\User;
use App\Models\AccountDetails;



class AuthController extends APIController
{

    public function register(Request $requests){
        $data = $requests->all();
        //validate
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
            $user = new User();
            $user->id = Str::orderedUuid();
            
            $user->email = $data['email'];
            $user->account_type = 'staff';
            $user->password = Hash::make($data['password']);
            $user->status = 'verified';
            $user->save();

            $accountDetails = new AccountDetails();
            $accountDetails->id = Str::orderedUuid();
            $accountDetails->user_id = $user->id;
            $accountDetails->first_name = $data['first_name'];
            $accountDetails->middle_name = '';
            $accountDetails->profile_picture = '';
            $accountDetails->last_name = $data['last_name'];
            $accountDetails->save();

            $this->response['data'] = 'User regitered';
            return $this->getResponse();
        } catch (\Throwable $th) {
            $this->response['error'] = $th->getMessage();
            $this->response['status'] = $th->getCode();
            return $this->getResponse();
        }
    }
}
