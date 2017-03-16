<?php

namespace P3in\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use P3in\Events\Login;
use P3in\Events\Logout;
use P3in\Models\User;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    public function logout(Request $request)
    {
        $user = $this->guard()->user();

        $this->guard()->logout(true);

        event(new Logout($user));

        return response()->json([
            'message' => 'Logged out',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function register(Request $request)
    {
        // for testing.
        User::where('email', $request->email)->delete();
        $this->validate($request, $this->rules(), $this->validationMessages());

        $user = $this->create($request->all());

        $user->sendRegistrationConfirmationNotification();

        event(new Registered($user));

        return $this->registered($request, $user);
    }

    public function activate(Request $request, $code)
    {
        try {
            $user = User::where('activation_code', $code)->firstOrFail();

            if ($user->active) {
                return $this->alreadyActiveResponse($request);
            }
            $user->active = true;
            $user->save();

            if($this->afterLoginAttempt(($this->guard()->login($user)))) {
                return $this->sendLoginResponse($request);
            }else{
                return $this->sendFailedLoginResponse($request);
            }

        } catch (ModelNotFoundException $e) {
            return $this->noCodeResponse($request);
        }
        return $user;
    }

    protected function rules()
    {

        return [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|min:10',
            'password' => 'required|min:6|confirmed',
        ];
    }

    protected function validationMessages()
    {
        return [
        ];
    }

    protected function create(array $data)
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
            'activation_code' => str_random(64)
        ]);
    }

    // we need to do things a bit differently using JWTAuth since it doesn't
    // fire events and the remember.  We also need the token to be setfor later
    // use in the controller, not sure why JWT doesn't do it internally...
    protected function attemptLogin(Request $request)
    {
        if($token = $this->guard()->attempt($this->credentials($request))) {
            return $this->afterLoginAttempt($token);
        }
        return $token;
    }

    protected function afterLoginAttempt($token)
    {
        $this->guard()->setToken($token);
        $user = $this->guard()->user();

        // jwt auth does not use events.
        event(new Login($user, $token));

        return $token;
    }

    protected function validateLogin(Request $request)
    {
        // we add remember => true to the request since all token auth are set to remember (no session).
        $request->merge(array('remember' => true));
        $this->validate($request, [
            $this->username() => 'required', 'password' => 'required',
        ]);
    }

    protected function credentials(Request $request)
    {
        $creds = $request->only($this->username(), 'password');
        $creds['active'] = 1;
        return $creds;
    }

    protected function authenticated(Request $request, $user)
    {
        if ($token = $this->guard()->getToken()) {
            $token = $token->get();
        }else{
            return $this->sendFailedLoginResponse($request);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => $user
        ]);
    }

    protected function registered(Request $request, $user)
    {
        return response()->json([
            'message' => trans('registration.check-email'),
            'user' => $user
        ]);
    }

    // @TODO: needs error code, can't be 401.
    protected function noCodeResponse(Request $request)
    {
        return response()->json([
            'message' => trans('registration.activation-failed'),
        ]);
    }

    // @TODO: needs error code, can't be 401.
    protected function alreadyActiveResponse(Request $request)
    {
        return response()->json([
            'message' => trans('registration.already-active'),
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return response()->json([
            'message' => trans('auth.failed'),
        ], 401);
    }

}
