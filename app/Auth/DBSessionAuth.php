<?php
namespace App\Auth;

use App\Models\AuthSession;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Authenticatable;
use Hash;
use Illuminate\Support\Str;

class DBSessionAuth implements Guard{

    private $sessionName = 'wms_session_';
    private $provider;
    private $guardName;
    private $userData;
    private $tempUserData;

    private $additionalWhere = [
        ['status','=',1]
    ];

    public function __construct($provider,$guardName){
        $this->sessionName  .= $guardName;
        $this->provider     = $provider;
        $this->guardName    = $guardName;
    }

    public function check(){

        if($this->userData){
            return true;
        }

        $accessToken = session($this->sessionName);

        if(!$accessToken){
            return false;
        }

        $getSessionData = AuthSession::where(
            [
                ['guard_name',$this->guardName],
                ['access_token',$accessToken]
            ]
        )
            ->first();

        if(!$getSessionData){
            return false;
        }

        $getUserData = $this->provider::where('id',$getSessionData->user_id);

        if(!empty($this->additionalWhere)){
            $getUserData->where($this->additionalWhere);
        }

        $getUserData = $getUserData->first();

        $getSessionData->update([
            'updated_at'=> \Carbon::now()
        ]);

        if(!$getUserData){
            return false;
        }

        $this->setUser($getUserData);

        return true;

    }

    public function guest(){
        if($this->user()){
            return true;
        }
        return false;
    }

    public function user(){
        if($this->check()){
            return $this->userData;
        }
        return null;
    }

    public function id(){
        if($this->check()){
            return $this->userData->id;
        }
        return null;
    }

    public function attempt(array $credentials = [], $remember = false){

        if(empty($credentials)){
            return false;
        }

        $password = '';
        $handleWhere = [];
        foreach ($credentials as $key => $value){
            if($key == 'password'){
                $password = $value;
                continue;
            }
            $handleWhere[] = [
                $key,$value
            ];
        }

        if(!$password){
            return false;
        }

        $getUserData = $this->provider::where($handleWhere);

        if(!empty($this->additionalWhere)){
            $getUserData->where($this->additionalWhere);
        }

        $getUserData = $getUserData->first();


        if(!$getUserData){
            return false;
        }

        if(!Hash::check($password, $getUserData->password)){
            return false;
        }

        $accessToken = md5(time().(time()+rand(9,9999999)).session()->getId().Str::random(40).uniqid().random_bytes(10).$getUserData->id);

        // Delete Old Users
//        AuthSession::where([
//            ['guard_name', $this->guardName],
//            ['user_id', $getUserData->id],
//        ])->delete();

        AuthSession::create([
            'guard_name'=> $this->guardName,
            'access_token'=> $accessToken,
            'user_id'=> $getUserData->id,
            'ip'=> getRealIP(),
            'user_agent'=> getUserAgent()
        ]);

        session()->put($this->sessionName,$accessToken);
        $this->setUser($getUserData);

        return true;
    }

    public function loginUsingId($id,$remember = false){

        $getUserData = $this->provider::where('id',$id)->first();
        if(!$getUserData){
            return false;
        }

        $accessToken = md5(time().(time()+rand(9,9999999)).session()->getId().Str::random(40).uniqid().random_bytes(10).$getUserData->id);

        // Delete Old Users
//        AuthSession::where([
//            ['guard_name', $this->guardName],
//            ['user_id', $getUserData->user_id],
//        ])->delete();
        AuthSession::create([
            'guard_name'=> $this->guardName,
            'access_token'=> $accessToken,
            'user_id'=> $getUserData->id,
            'ip'=> getRealIP(),
            'user_agent'=> getUserAgent()
        ]);

        session()->put($this->sessionName,$accessToken);
        $this->setUser($getUserData);
        return true;

    }

    public function setUser(Authenticatable $user){
        $this->userData = $user;
        return $this;
    }

    public function logout(){
        AuthSession::where([
            ['guard_name', $this->guardName],
            ['access_token', session($this->sessionName)],
        ])->delete();
        session()->forget($this->sessionName);

        return true;
    }

    public function validate(array $credentials = []){

        $password = '';
        $handleWhere = [];
        foreach ($credentials as $key => $value){
            if($key == 'password'){
                $password = $value;
                continue;
            }
            $handleWhere[] = [
                $key,$value
            ];
        }

        if(!$password){
            return false;
        }

        $getUserData = $this->provider::where($handleWhere);

        if(!empty($this->additionalWhere)){
            $getUserData->where($this->additionalWhere);
        }

        $getUserData = $getUserData->first();

        if(!$getUserData){
            return false;
        }

        if(!Hash::check($password, $getUserData->password)){
            return false;
        }

        $this->tempUserData = $getUserData;

        return true;
    }

    public function once(array $credentials = []){
        if($this->validate($credentials)){
            $this->setUser($this->tempUserData);
            return true;
        }
        return false;
    }

    public function viaRemember(){
        return false;
    }
    public function hasUser(){
        if($this->check()){
            return $this->userData;
        }
        return null;
    }
}
