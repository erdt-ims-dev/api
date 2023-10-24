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
use App\Models\LoginAttempts;



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

    public function login(Request $request)
    {
        $data = $request->all();

        $credentials = null;
        //get data using email;
        $account = User::whereRaw("BINARY email='" . $data['email'] . "'")->first();
        $credentials = ['email' => $data['email'], 'password' => $data['password']];

        if ($credentials && $account) {
            try {
                $valid = Auth::attempt($credentials);
                $attemp = LoginAttempts::where('account_id', '=', $account['id'])->first();
                if ($valid) {
                    $user = Auth::user();
                    $account = [
                        'email' => $user['email'],
                        'token' => $user->createToken($data['email'])->plainTextToken
                    ];
                    $this->response['data'] = $account;
                    $this->response['status'] = 200;
                    return $this->getResponse();
                } else {
                    if ($attemp) {
                        LoginAttempts::where('account_id', '=', $account['id'])->update(['attempts' => intVal($attemp['attempts']) + 1]);
                    } else {
                        LoginAttempts::create([
                            'account_id' => $account['id'],
                            'attempts' => 1
                        ]);
                    }
                    $this->response['error'] = 'Invalid Crendentials';
                    $this->response['status'] = 401;
                    return $this->getError();
                }
            } catch (\Throwable $th) {
                $this->response['error'] = $th->getMessage();
                $this->response['status'] = $th->getCode();
                return $this->getError();
            }

        } else {
            $this->response['error'] = 'Invalid Crendentials';
            $this->response['status'] = 401;
            return $this->getError();
        }
    }
}
