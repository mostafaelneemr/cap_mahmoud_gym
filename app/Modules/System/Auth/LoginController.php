<?php

namespace App\Modules\System\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Models\Trainee;
use Illuminate\Http\Request;
use App\Modules\System\SystemController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

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
        $this->middleware('guest:user')->except('logout', 'updatePassword', 'redirectToGoogle', 'handleGoogleCallback');
    }

    public function guard()
    {
        return \Auth::guard('user');
    }

    public function login(LoginRequest $request)
    {
        // --- Try User guard first ---
        $user_query = User::select(['id', 'email', 'password', 'user_type', 'status', 'permission_group_id'])
            ->where('email', $request->email)
            ->first();

        if ($user_query && Hash::check($request->password, $user_query->password)) {
            \Auth::guard('user')->loginUsingId($user_query->id);

            $user = auth('user')->user();

            if ($user->user_type == 1 || $user->user_type == null) {
                $route = $user->permission_group && $user->permission_group->new_admin_default_route
                    ? route($user->permission_group->new_admin_default_route)
                    : route('system.dashboard');
            } elseif ($user->user_type == 2) {
                $route = route('system.dashboard.trainer');
            } else {
                $route = route('system.dashboard');
            }

            return $this->success(__('Logged In successfully'), ['url' => $route]);
        }

        // --- Try Trainee guard ---
        $trainee_query = \App\Models\Trainee::where('email', $request->email)->first();

        if ($trainee_query && Hash::check($request->password, $trainee_query->password)) {
            if (!in_array($trainee_query->status, ['active', '1', 1])) {
                return $this->fail(__('Your account is inactive. Please contact the gym administration.'));
            }

            auth('trainee')->login($trainee_query, true);
            $request->session()->regenerate();

            return $this->success(__('Logged In successfully'), ['url' => route('system.dashboard.trainer')]);
        }

        return $this->fail(__('Wrong Email or Password'));
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', __('Google authentication failed. Please try again.'));
        }
        $email = $googleUser->getEmail();
        $trainee = Trainee::where('email', $email)->first();

        if ($trainee && in_array($trainee->status, ['active', '1', 1])) {

            if (empty($trainee->google_id)) {
                $trainee->google_id = $googleUser->getId();
                $trainee->save();
            }

            // Regenerate session BEFORE login to prevent session fixation
            request()->session()->regenerate();
            auth('trainee')->login($trainee, true);

            return redirect()->route('system.dashboard.trainer');
        }

        return redirect()->route('login')->with('error', 'عفواً، هذا الحساب غير مسجل كمتدرب. يرجى التواصل مع إدارة الجيم لتسجيل حسابك أولاً.');
    }

    protected function logout(Request $request)
    {
        if (auth('user')->check()) {
            auth('user')->logout();
        }
        if (auth('trainee')->check()) {
            auth('trainee')->logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/system/login');
    }

    public function updatePassword(ResetPasswordRequest $request)
    {
        $user =  auth('user')->user();
        $user->update(['force_reset_password' => 0, 'password' => Hash::make($request->password)]);
        return $this->success(__('Password reset successfully!'), ['url' => route('system.dashboard')]);
    }
}

