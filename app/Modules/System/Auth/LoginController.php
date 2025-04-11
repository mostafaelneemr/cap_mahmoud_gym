<?php

namespace App\Modules\System\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Modules\System\SystemController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;

class LoginController extends SystemController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    function showLoginForm()
    {
        return parent::view('auth.login');
    }

    protected $redirectTo = '/system';


    public function __construct()
    {
        $this->middleware('guest:user')->except('logout','updatePassword');
    }

    public function guard()
    {
        return \Auth::guard('user');
    }

    public function login(LoginRequest $request)
    {
         $user_query = User::select(['email','password'])->where('email', $request->email)->first();

        if ($user_query && Hash::check($request->password,$user_query->password)) {
            session()->put('user', $user_query);
            $session_user = session()->get('user');
            $user = User::where('email', $session_user->email)->first();
            \Auth::guard('user')->loginUsingId($user->id);
            if ($user->user_type == 1 || $user->user_type == null) {
                $route = auth('user')->user()->permission_group->new_admin_default_route ? route(auth('user')->user()->permission_group->new_admin_default_route) : route('system.dashboard');
            }elseif ($user->user_type == 2) {
                $route = route('system.dashboard.trainer');
            }
             return $this->success(__('Logged In successfully'), ['url' => $route]);
         } else {
            return $this->fail(__('Wrong Email or Password'));
        }
    }

    protected function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect('/system/login');
    }

    public function updatePassword(ResetPasswordRequest $request)
    {
        $user =  auth('user')->user() ;
        $user->update(['force_reset_password' => 0,'password'=>Hash::make($request->password)]);
        return $this->success(__('Password reset successfully!'), ['url' => route('system.dashboard')]);
    }

}
