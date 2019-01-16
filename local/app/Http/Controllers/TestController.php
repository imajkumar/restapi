<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use Validator;
use Illuminate\Mail\Message;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class TestController extends Controller
{
    
    public function allUser(Request $request)
    {

        // $user = JWTAuth::toUser($token);

        try {
            
            $allUser = DB::table('users')->get();

            return response()->json(['success'=> true, 'message'=> 'Thanks for signing up! Please check your email to complete your registration.', 'data' => $allUser]);

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e)
        {
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }
        catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e)
        {
            return response()->json(['success' => false, 'error' => 'Your token is expired, please login first.'], 500);
        }
        catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e)
        {
            return response()->json(['success' => false, 'error' => 'Your token is invalid, please try again.'], 500);
        }
        
    }

}