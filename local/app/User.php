<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Exception;
class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    protected $fillable = [
        'name', 'email', 'password', 'is_verified'
    ];

    protected $hidden = [
        'password',
    ];
    protected function validateNetwork(){
        if( $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || strtoupper($_SERVER['REMOTE_ADDR']) == 'LOCALHOST'){
            return true;
        }

        $config_network = Cache::get('config_network');
        if( !is_array($config_network) ){
            $config_network = [];
        }

        foreach( $config_network as $key => $network){
            if( strtotime($network['EXPIRY']) <= strtotime('now')){
                unset($config_network[$key]);
            }
        }

        Cache::forget('config_network');
        Cache::forever('config_network', $config_network);

        $user_ip = trim($_SERVER['REMOTE_ADDR']);
        $access_url = "http://api.ipstack.com/";
        $access_key = "?access_key=cf96ebd2eacbc68c6f43a91475b80c0c";

        $ip_data = json_decode(file_get_contents($access_url . $user_ip . $access_key), true);
        $distance = 1000;

        foreach( $config_network as $key => $network){
            if( $network['VERIFY'] == 0){
                continue;
            }
            $network_data = json_decode(file_get_contents($access_url . $network['IP'] . $access_key), true);
            if( $network_data ){
                $lat1 = $network_data['latitude'];
                $lon1 = $network_data['longitude'];

                $lat2 = $ip_data['latitude'];
                $lon2 = $ip_data['longitude'];

                $theta = $lon1 - $lon2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $dist = $dist * 60 * 1.1515 * 1.609344;

                if( $dist < $distance){
                    $distance = $dist;
                }
            }
        }


        if( $distance > 1){
            throw new Exception("User-000");
        }

        return true;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    
     public function login_($data) {
       $user_data = $this->where(
                      function ($query) use ($data) {
                  $query->where(
                          function ($query) use ($data) {
                      $query->where('email', '=', $data['email']);
                      $query->where('password', '=', md5($data['password']));
                  });
                  $query->orWhere(
                          function ($query) use ($data) {
                      $query->where('email', '=', $data['email']);
                      $query->where('password', '=', md5($data['password']));
                  });
              })
              ->where('status', '=', '1')
              ->where('is_deleted', '=', '0')
              ->first();

      if (!$user_data) {
          throw new Exception("User-001");
      }

      if ( !in_array($user_data->id,[1,2]) ) {
          $this->validateNetwork();
      }
      //----------------
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

      //-----------------
     }
}
