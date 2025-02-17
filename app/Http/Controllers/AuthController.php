<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

use App\Mail\SendPasswordMail;
use App\Mail\ResetPasswordMail;

use App\Providers\RequestValidatorServiceProvider;

use Laravel\Sanctum\Sanctum;

use App\Models\User;
use App\Models\PasswordResets;
use App\Models\AccountDetails;
use App\Models\LoginAttempts;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuthExceptions\JWTException;
use Carbon\Carbon;

class AuthController extends APIController
{
    

    public function register(Request $request)
{
    $data = $request->all();
    
    // Validate input values
    $valid = RequestValidatorServiceProvider::registerValidator($data);
    
    if (!$valid) {
        $this->response['error'] = $valid['error'];
        $this->response['status'] = 401;
        return $this->getResponse();
    }

    // Check if the email already exists in the User table
    if (User::where('email', $data['email'])->exists()) {
        $this->response['error'] = "Email already exists";
        $this->response['status'] = 401;
        return $this->getResponse();
    }

    // Insert new user since the email does not exist
    return $this->insertNew($data);
}

    // Used when using the registration form


    public function insertNew($data)
    {
        try {
            $userUuid = Str::uuid()->toString();
            $generatedPassword = Str::random(8);
            $user = new User();
            $user->uuid = $userUuid;
            $user->email = $data['email'];
            $user->account_type = 'new';
            $user->session_token = null;
            $user->password = Hash::make($generatedPassword);
            $user->status = 'unverified';
            $user->save();
    
            $accountDetails = new AccountDetails();
            $accountDetails->user_id = $user->id;
            $accountDetails->first_name = $data['first_name'];
            $accountDetails->middle_name = '';
            $accountDetails->profile_picture = '';
            $accountDetails->last_name = $data['last_name'];
            $accountDetails->save();
            
            // Send the password email to the user
            $password = $generatedPassword;  // This is the plain password (not hashed)
            Mail::send('emails.sendPassword', ['password' => $password], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Your Account Password');
            });
    
            event(new Registered($user));
            $this->response['data'] = $user;
            return $this->getResponse();
        } catch (\Throwable $th) {
            $this->response['error'] = $th->getMessage();
            $this->response['status'] = $th->getCode();
            return $this->getResponse();
        }
    }
    

    //used when manually creating an account - admin panel
    public function newUser($data){
        $userUuid = Str::uuid()->toString();
        $valid = RequestValidatorServiceProvider::registerValidator($data);
        if($valid){
            $user = new User();
            $user->id = $userUuid;
            $user->email = $data['email'];
            $user->session_token = null;
            $user->account_type = 'new';
            $user->password = Hash::make($data['password']);
            $user->status = 'active';
            $user->save();
            return $userUuid;
        }
        if(!$valid){
            return null;
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
                        'status' => $user['status'],
                        'account_type' => $user['account_type'],
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

    public function logout(Request $request){
        try {
            // $data = $request->all();
            $account = Auth::user();
            $account->tokens()->where('id', '=', $account->currentAccessToken()->id)->update(array('deleted_at', '=', now()));
            $this->data = true;
            $this->status = 200;
            return $this->getResponse();
        } catch (\Throwable $th) {
            $this->error = $th->getMessage();
            $this->status = $th->getCode();
            return $this->getError();
        }
    }
    // Password Resets - WIP

    // Sends reset link to email
    public function forgot_password(Request $request)
    {
        // Validate the request (ensure the email is provided)
        $data = $request->all();
        $valid = RequestValidatorServiceProvider::forgotPasswordValidator($data);
        if (!$valid) {
            $this->response['error'] = 'Invalid Data Provided';
            $this->response['status'] = 500;
            return $this->getResponse();
        }
    
        // Find the user by email
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            $this->response['error'] = 'User not found';
            $this->response['status'] = 404; // or 500 based on your logic
            return $this->getResponse();
        }
    
        // Generate the reset token and store it
        $token = Str::random(60); // Use your logic for generating the token
        PasswordResets::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addMinutes(60), // Set expiration time
        ]);
    
        // Send the email using the Mailable class
        Mail::to($user->email)->send(new ResetPasswordMail($token));
    
        // Return response indicating that the reset link has been sent
        $this->response['data'] = true;
        $this->response['status'] = 200;
        return $this->getResponse();
    }
    // Once link is clicked, begin reset password process
    public function reset_password(Request $request)
    {
        // Validate request
        $data = $request->all();
        $valid = RequestValidatorServiceProvider::resetPasswordValidator($data);
        if (!$valid) {
            $this->response['error'] = 'Invalid Data Provided';
            $this->response['status'] = 500;
            return $this->getResponse();
        }

        $response = Password::reset(
            $data,
            function ($user, $password) {
                // Reset the user's password
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            $this->response['data'] = true;
            $this->response['status'] = 200;
            return $this->getResponse();
        } else {
            $this->response['error'] = 'Password reset failed';
            $this->response['status'] = 500;
            return $this->getError();
        }
    }
    // JWT Related

    public function __construct(){
        $this->middleware('auth:api', ['except' => ['authenticate', 'login', 'register', 'forgot_password', 'reset_password']]);
    }
    
    // public function authenticate(Request $request){
    //     // validate if account exists
    //     $data = $request->all();
    //     $response = User::where("email", '=' ,$data['email'])->get();
            
    //     if(!$response->isEmpty()){
    //             $credentials = array("email" => $data['email'], 'password' => $data['password']);
    //             $token = null;
                
    //             try {
    //                 // Verify the credentials and create a JWT token for the user
    //                 if (! $token = JWTAuth::attempt($credentials)) {
    //                     // If verification fails, return an error response
    //                     return response()->json(['error' => 'invalid_credentials'], 401);
    //                 }
    //             } catch (JWTException $e) {
    //                 // If an exception occurs during JWT token creation, return an error response
    //                 return response()->json(['error' => 'could_not_create_token'], 500);
    //             }
    //             // Calculate how much time has passed since last update
    //             $lastLogin = Carbon::createFromFormat('Y-m-d H:i:s', $response[0]['updated_at']);
    //             $currentDate = Carbon::now();
    //             $diff = $currentDate->diffInMinutes($lastLogin);
                
    //             $token = compact('token');
    //             if ($diff > 0) {
    //                 $accountToken = json_decode($response[0]['session_token'], true);
                    
    //                 if ($accountToken === null) {
    //                     // If the token exists, update it along with the timestamp
    //                     $accountToken['token'] = $token['token'];
    //                 } else {
    //                     // If the token doesn't exist, create a new entry
    //                     $accountToken = [
    //                         'token' => $token['token'],
    //                         'updated_at' => Carbon::now(),
    //                     ];
    //                 }
                
    //                 $updatedData = [
    //                     'session_token' => json_encode($accountToken),
    //                     'updated_at' => Carbon::now(),
    //                 ];
                
    //                 try {
    //                     User::where('id', '=', $response[0]['id'])->update($updatedData);
    //                     $this->response['data'] = "Updated";
    //                     $this->response['status'] = 401;
    //                     return $this->getResponse();
    //                 } catch (\Exception $e) {
    //                     // Handle the exception
    //                     $this->response['error'] = $e->getMessage();
    //                     $this->response['status'] = 500;
    //                     return $this->getResponse();
    //                 }
    //             }

    //         }else{
    //             $this->response['error'] = "Account Doesn't Exist";
    //             $this->response['status'] = 401;
    //             return $this->getResponse();   
    //         }
            
    // }

    // formatted by gpt
    public function authenticate(Request $request)
    {
        // Validate if the account exists
        $data = $request->all();
        $user = User::where("email", $data['email'])->first();

        if (!$user) {
            return $this->getResponseWithError("Account doesn't exist", 401);
        }

        $credentials = ["email" => $data['email'], 'password' => $data['password']];
        $token = null;

        try {
            // Verify the credentials and create a JWT token for the user
            $token = JWTAuth::attempt($credentials);

            if (!$token) {
                return $this->getResponseWithError('Invalid credentials', 401);
            }
        } catch (JWTException $e) {
            // If an exception occurs during JWT token creation, return an error response
            return $this->getResponseWithError('Could not create token', 500);
        }

        // Calculate how much time has passed since last update
        $lastLogin = Carbon::parse($user->updated_at);
        $currentDate = Carbon::now();
        $diff = $currentDate->diffInMinutes($lastLogin);
        // set to 0 for testing
        if ($diff > 0) {
            $accountToken = json_decode($user->session_token, true) ?: [];
            $accountToken['token'] = $token;
            $accountToken['updated_at'] = Carbon::now();

            try {
                // Use a transaction to ensure data consistency
                \DB::beginTransaction();

                $user->update([
                    'session_token' => json_encode($accountToken),
                    'updated_at' => Carbon::now(),
                ]);

                \DB::commit();

                return $this->getResponseWithData($accountToken, 401);
            } catch (\Exception $e) {
                // Rollback the transaction on exception
                \DB::rollBack();

                return $this->getResponseWithError($e->getMessage(), 500);
            }
        }

        return $this->getResponseWithData("No update needed", 200);
    }

    // Helper method for consistent error response
    private function getResponseWithError($error, $status)
    {
        return response()->json(['error' => $error, 'status' => $status], $status);
    }

    // Helper method for consistent success response
    private function getResponseWithData($data, $status)
    {
        return response()->json(['data' => $data, 'status' => $status], $status);
    }



    public function refresh(){
        $current_token  = JWTAuth::getToken();
        $token          = JWTAuth::refresh($current_token);
        $this->response['current'] = $current_token;
        $this->response['new'] = $token;
        return $this->getResponse();
    }

    public function deauthenticate(){
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['token' => NULL]);
    }

    protected function respondWithToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function getAuthenticatedUser(){
      try {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
          return response()->json(['user_not_found'], 404);
        }
      } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
        return response()->json(['token_expired'], $e->getStatusCode());
      } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
        return response()->json(['token_invalid'], $e->getStatusCode());
      } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
        return response()->json(['token_absent'], $e->getStatusCode());
      }

      // the token is valid and we have found the user via the sub claim
      if($user){
        $account = app('App\Http\Controllers\AccountDetailsController')->retrieveByParameter(array('col', '=', 'id', 'value', '=', $user->id));
        $user['details'] = $account ? $account['data'] : null;
      }
      return response()->json($user);
  }
    // Authenticate


}
