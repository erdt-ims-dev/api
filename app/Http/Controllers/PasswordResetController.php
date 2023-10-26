<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Providers\RequestValidatorServiceProvider;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;

use App\Models\PasswordReset;
use App\Models\User;

use App\Notifications\PasswordResetNotification;

class PasswordResetController extends Controller
{
    //
    public function sendPasswordToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email|exists:user"
        ]);

        if ($validator->fails()) {
            $this->response['error'] =  $validator->errors()->first();
            $this->response['status'] = 401;
            return $this->getError();
        }

        $data = $validator->validated();
        $user = User::where("email", $data["email"])->first();

        if (!$user) {
            $this->response['error'] = 'Account Not Found';
            $this->response['status'] = 401;
            return $this->getError();
        }

        $resetLinkSent = $this->sendPasswordResetLink($user['id']);
        if ($resetLinkSent) {
            $this->response['data'] = 'Link Sent';
            $this->response['status'] = 200;
            return $this->getResponse();

        } else {
            $this->response['data'] = 'Failed to send token';
            $this->response['status'] = 401;
            return $this->getError();
        }

    }

    public function sendPasswordResetLink($id){
        try {
            $token = $this->genResetCode();
            $signature = hash('md5', $token);
            $user = User::findOrFail($id);
            $user->notify(new PasswordResetNotification($token));
            PasswordReset::create([
                "id" => Str::orderedUuid(),
                "user_id" => $id,
                "token" => $signature,
                "token_type" => 1,
                "expires_at" => Carbon::now()->addMinutes(30)
            ]);
            return true;
        } catch (\Throwable $th) {
            $this->response['data'] = $th->getMessage();
            $this->response['status'] = 401;
            return $this->getError();
        }
    }

    public function genResetCode(){
        // Generate a random password reset code/token
        return Str::random(10);
    }
}
