<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\forgetPasswords;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Mail\Message;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Exception;
class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function guard()
    {
        return Auth::guard();
    }

    public function register(Request $request)
    {
        $credentials = $request->only('name', 'email', 'password');

        $rules = [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users'
        ];

        $validator = Validator::make($credentials, $rules);

        if($validator->fails())
        {
            return response()->json(['success'=> false, 'error'=> $validator->messages()]);
        }

        $name = $request->name;
        $email = $request->email;
        $password = $request->password;

        $user = User::create(['name' => $name, 'email' => $email, 'password' => Hash::make($password)]);

        $verification_code = str_random(30);

        DB::table('user_verifications')->insert(['user_id'=>$user->id,'token'=>$verification_code]);

        $email_template = 'email.verify';

        $subject = "Please verify your email address.";

        $this->sendEmailNotification($email_template, $email, $name, $subject, $verification_code);

        return response()->json(['success'=> true, 'message'=> 'Thanks for signing up! Please check your email to complete your registration.']);

    }

    public function verifyUser($verification_code)
    {
        $check = DB::table('user_verifications')->where('token',$verification_code)->first();

        if(!is_null($check))
        {

            $user = User::find($check->user_id);

            if($user->is_verified == 1){
                return response()->json([
                    'success'=> true,
                    'message'=> 'Account already verified..'
                ]);
            }

            $user->update(['is_verified' => 1]);

            DB::table('user_verifications')->where('token',$verification_code)->delete();

            return response()->json([
                'success'=> true,
                'message'=> 'You have successfully verified your email address.'
            ]);

        }

        return response()->json(['success'=> false, 'error'=> "Verification code is invalid."]);

    }
    public function login(Request $request)
    {
      try{
        $credentials = $request->only('email', 'password');

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($credentials, $rules);

        if($validator->fails()) {
            throw new Exception('UserController-001');

        }

        $credentials['is_verified'] = 1;

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials))
            {
                return response()->json(['success' => false, 'error' => 'We cant find an account with this credentials. Please make sure you entered the right information and you have verified your email address.'], 404);
            }
        } catch (JWTException $e)
        {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to login, please try again.'], 500);
        }

      //  $token = $this->respondWithToken($token);
        return $this->setSuccessResponse([],"SUCCESS-LOGIN",$token);

      }
      catch(\Exception $ex){
        return $this->setErrorResponse($ex->getMessage());
      }


        // all good so return the token

    }

    public function getoffer(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($credentials, $rules);

        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()], 401);
        }

        $credentials['is_verified'] = 1;

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials))
            {
                return response()->json(['success' => false, 'error' => 'We cant find an account with this credentials. Please make sure you entered the right information and you have verified your email address.'], 404);
            }
        } catch (JWTException $e)
        {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to login, please try again.'], 500);
        }

        $token = $this->respondWithToken($token);

        // all good so return the token
        return response()->json(['success' => true, 'data'=> [ 'token' => $token ]], 200);
    }

    public function recoverRequest(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $error_message = "Your email address was not found.";
            return response()->json(['success' => false, 'error' => ['email'=> $error_message]], 401);
        }

        $name = $user->name;
        $email = $user->email;
        $subject = "Password Recovery Instruction";
        $email_template = 'email.recovery_instruction';

        $temp_pass_code = str_random(30);

        $forgetPasswords = new forgetPasswords;
            $forgetPasswords->user_id = $user->id;
            $forgetPasswords->temp_pass_code = $temp_pass_code;
        $forgetPasswords->save();

        $this->sendEmailNotification($email_template, $email, $name, $subject, $temp_pass_code);

        return response()->json([
            'success' => true, 'message'=> 'A email has been sent to your email for password recovery instruction.'
        ]);
    }

    public function verifyTempPassword(Request $request)
    {
        $user_id = $request->user_id;
        $temp_pass_code = $request->temp_pass_code;
        $currentDateTime = date('Y-m-d H:i:s');

        $passCodeAvailable = forgetPasswords::where('user_id', $user_id)
                                            ->where('temp_pass_code', $temp_pass_code)
                                            ->where('created_at', '<' , $temp_pass_code)
                                            ->first();

        if ($passCodeAvailable)
        {
            return response()->json(['success' => true, 'message' => 'Thank you for your co-operation. Please reset your password.'], 200);
        }
        else
        {
            return response()->json(['success' => false, 'message' => 'Temp pass code is expired, Please try for another.'], 400);
        }

    }

    public function setNewPassword(Request $request)
    {
        $user_id = $request->user_id;
        $new_password = $request->new_password;
        $retype_new_password = $request->retype_new_password;

        if (!empty($new_password) && ($new_password === $retype_new_password))
        {
            $user = User::where('id', $user_id)
                        ->update(['password' => Hash::make($new_password)]);

            $passCodeAvailable = forgetPasswords::where('user_id', $user_id)
                                            ->delete();

            return response()->json(['success' => true, 'message'=> "Password successfully changed."]);
        }
        else
        {
            return response()->json(['success' => false, 'message' => 'Details are not currect, please try again'], 400);
        }

    }

    public function logout(Request $request)
    {
        $this->validate($request, ['token' => 'required']);

        try
        {
            $test = JWTAuth::invalidate($request->input('token'));
            return response()->json(['success' => true, 'message'=> "You have successfully logged out."]);
        }
        catch (JWTException $e)
        {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function sendEmailNotification($email_template, $to_email, $to_name, $to_subject, $verification_code)
    {

        $from_email = env('MAIL_USERNAME');
        $from_name = "Siddique API";
        $to_email = $to_email;
        $to_name = $to_name;
        $to_subject = $to_subject;

        try{

            $sentMail = Mail::send($email_template, ['name' => $to_name, 'verification_code' => $verification_code ], function($mail) use ($from_email, $from_name, $to_email, $to_name, $to_subject)
                {
                    $mail->from($from_email, $from_name);
                    $mail->to($to_email, $to_name);
                    $mail->subject($to_subject);
                }
            );
        }
        catch (\Exception $e)
        {
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }

    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'token',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

}
