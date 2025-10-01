<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\UserLoginHistory;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/backend/payments/create';
    protected $redirectTo = '/backend/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        return view('backend.auth.login', [
            'title' => 'Login',
            'loginRoute' => 'backend.login',
            'forgotPasswordRoute' => 'password.request',
        ]);
    }


    public function username()
    {
        $login = strip_tags(trim(request()->input('email')));
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $field = is_string($field) ? $field : 'email';
        request()->merge([$field => $login]);

        return $field; // 👈 Must return a string only!

        // $login = request()->input('email');
        // $login = strip_tags(trim(request()->input('email')));
        // $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        // request()->merge([$field => $login]);
        // return $field;
    }

    protected function logout(Request $request)
    {
        $user = auth()->user();
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();

        DB::table('sessions')->where('user_id', $user->id)->delete();

        $data = [
            'last_logout_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'last_login_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp(),
            'user_agent' => $request->userAgent(),
        ];
        $user->update($data);

        UserLoginHistory::create(array_merge(['user_id' => $user->id], $data));

        activity("User successfully Logout with email-ID [$user->email]")
            ->performedOn($user) // Entry add in table. model name(subject_type) & id(subject_id)
            ->causedBy($user) //causer_id = admin id, causer type = admin model
            ->event(__METHOD__)
            ->withProperties($data)
            ->log('User successfully Logout');

        return redirect()->route('backend.login');
    }

    function authenticated(Request $request, $user)
    {
        // Temporarily disable active check for debugging
        // if ($user->active !== 'Y') {
        //     auth()->logout();
        //     return redirect()->route('backend.login')->withErrors([
        //         'email' => 'Your account is inactive. Please contact the administrator.',
        //     ]);
        // }
        
        $data = [
            'last_login_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp(),
            'user_agent' => $request->userAgent(),
        ];
        $user->update($data);

        UserLoginHistory::create(array_merge(['user_id' => $user->id], $data));

        activity("User Logged-In with email-ID [$user->email]")
            ->performedOn($user) // Entry add in table. model name(subject_type) & id(subject_id)
            ->causedBy($user) //causer_id = admin id, causer type = admin model
            ->event(__METHOD__)
            ->withProperties($data)
            ->log('User successfully logged-In');
    }
    protected function sendFailedLoginResponse(Request $request)
    {
        $login = $request->input('email');

        $user = \App\Models\User::where('email', $login)
            ->orWhere('username', $login)
            ->first();

        // Temporarily disable active check for debugging
        // if ($user && $user->active !== 'Y') {
        //     throw ValidationException::withMessages([
        //         'email' => ['Your account is inactive. Please contact the administrator.'],
        //     ]);
        // }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }
}
