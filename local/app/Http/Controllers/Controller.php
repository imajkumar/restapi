<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function setSuccessResponse($data = [], $message_code = 'Success',$api_token="", $message_action = 0){
        return response()->json([
            "data" => $data,
            "api_token" =>$api_token,
            "log" => '',
            "code"=> 200,
            "message" => $message_code,
            "message_code" => $message_code,
            "message_action" => (int) $message_action,
            "status" => 1
        ]);
    }

     protected function setErrorResponse( $message_code = 'ERROR', $message_action = 0){
        $message = "";
        if( is_array($message_code) ){
            foreach($message_code as $msg){
                $message = $msg;
            }

            $message_code = implode(',', $message_code);
        } else {
            $message = $message_code;
        }

        return response()->json([
            "data" => '', //this will be for data
            "api_token" => '',//this is hold token after success login
            "log" => '',//provide log id
            "code"=> 200, //response code
            "message" => $message,
            "message_code" => $message_code,
            "message_action" => (int) $message_action, //if any action requieed then suggest
            "status" => 0
        ]);
    }


}
