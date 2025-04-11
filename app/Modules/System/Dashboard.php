<?php

namespace App\Modules\System;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;


class Dashboard extends SystemController{

    public function __construct(Request $request){
        parent::__construct();
    }

    public function index(Request $request){
        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Home'),
                'url'=> url('system'),
            ]
        ];


        return $this->view('dashboard',$this->viewData);

    }

    public function logout(){
        Auth::logout();
        return redirect()->route('system.dashboard');
    }

    public function changePassword(Request $request){
        // e10adc3949ba59abbe56e057f20f883e
        if($request->method() == 'POST'){

            $this->validate($request,[
                'old_password'          => 'required',
                'password'              => 'required|confirmed',
                'password_confirmation' => 'required'
            ]);

            if (md5($request->old_password) != (Auth::user()->password)){
                return back()
                    ->with('status','danger')
                    ->with('msg',__('Old Password is incorrect'));
            }

            User::find(Auth::id())->update(['password'=>md5($request->password)]);

            return back()
                ->with('status','success')
                ->with('msg',__('Your Password Has been changed successfully'));
        }else{
            $this->viewData['pageTitle'] = __('Change Password');
            return $this->view('dashboard.change-password',$this->viewData);
        }
    }

    public function encrypt(Request $request){
        $type = $request->encrypt_type;
        $text = $request->encrypt_text;

        if(
            !in_array($type,['encrypt','decrypt']) ||
            empty($text)
        ){
            return ['status'=>false,'msg'=>__('Please Enter valid data')];
        }

        if($type == 'encrypt'){
            return ['status'=>true,'data'=> Crypt::encryptString($text)];
        }else{
            return ['status'=>true,'data'=> Crypt::decryptString($text)];
        }

    }


    public function trainerDashboard(Request $request)
    {
        $user = auth()->user();
        dd($user);
    }
}
