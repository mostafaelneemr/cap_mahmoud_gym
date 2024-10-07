<?php namespace App\Auth;

use App\Models\AuthApi;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Authenticatable;
use Hash;
use Illuminate\Support\Str;

class ApiAuth implements Guard{

    private $provider;
    private $guardName;
    private $userData;
    private $tempUserData;

    private $additionalWhere = [
        ['status','=','active']
    ];

    public function __construct($provider,$guardName){
        $this->provider     = $provider;
        $this->guardName    = $guardName;
    }

    public function check(){

        if($this->userData){
            return true;
        }

        $accessToken = request()->bearerToken();

        if(!$accessToken){
            return false;
        }

        $getSessionData = AuthApi::where(
            [
                ['guard_name',$this->guardName],
                ['access_token',$accessToken]
            ]
        )
            ->first();


        if(!$getSessionData){
            return false;
        }
        $getUserData = $this->provider::where('id',$getSessionData->id);

        if(!empty($this->additionalWhere)){
            $getUserData->where($this->additionalWhere);
        }

        $getUserData = $getUserData->first();

        if(!$getUserData){
            return false;
        }

        $getSessionData->update([
            'updated_at'=> \Carbon::now()
        ]);

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
            return [
                'status'=> false,
                'token'=> null
            ];
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
            return [
                'status'=> false,
                'token'=> null
            ];
        }

        $getUserData = $this->provider::where($handleWhere);

        if(!empty($this->additionalWhere)){
            $getUserData->where($this->additionalWhere);
        }

        $getUserData = $getUserData->first();


        if(!$getUserData){
            return [
                'status'=> false,
                'token'=> null
            ];
        }

        if(!Hash::check($password, $getUserData->password)){
            return [
                'status'=> false,
                'token'=> null
            ];
        }

        $accessToken = hash('sha256',time().(time()+rand(9,9999999)).session()->getId().Str::random(100).uniqid().random_bytes(20).$getUserData->id);

        // Delete Old Users
        AuthApi::where([
            ['guard_name', $this->guardName],
            ['user_id', $getUserData->id],
        ])->delete();

        AuthApi::create([
            'guard_name'=> $this->guardName,
            'access_token'=> $accessToken,
            'user_id'=> $getUserData->id,
            'ip'=> getRealIP(),
            'user_agent'=> getUserAgent()
        ]);

        $this->setUser($getUserData);

        return [
            'status'=> true,
            'token'=> $accessToken
        ];
    }

    public function loginUsingId($id,$remember = false){
        $getUserData = $this->provider::where('id',$id)->first();

        if(!$getUserData){
            return [
                'status'=> false,
                'token'=> null
            ];
        }

        $accessToken = hash('sha256',time().(time()+rand(9,9999999)).session()->getId().Str::random(100).uniqid().random_bytes(20).$getUserData->id);


        // Delete Old Users
        AuthApi::where([
            ['guard_name', $this->guardName],
            ['user_id', $getUserData->id],
        ])->delete();

        AuthApi::create([
            'guard_name'=> $this->guardName,
            'access_token'=> $accessToken,
            'user_id'=> $getUserData->id,
            'ip'=> getRealIP(),
            'user_agent'=> getUserAgent()
        ]);

        $this->setUser($getUserData);

        return [
            'status'=> true,
            'token'=> $accessToken
        ];

    }

    public function setUser(Authenticatable $user){
        $this->userData = $user;
        return $this;
    }

    public function logout(){
        AuthApi::where([
            ['guard_name', $this->guardName],
            ['access_token', session($this->sessionName)],
        ])->delete();


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
}
