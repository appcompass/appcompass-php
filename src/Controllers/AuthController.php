<?php

namespace P3in\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use P3in\Events\Login;
use P3in\Events\Logout;
use App\User;
use P3in\Traits\HasApiOutput;
use P3in\Traits\RegistersUsers;

class AuthController extends Controller
{
    use AuthenticatesUsers, RegistersUsers, HasApiOutput;

    public function logout()
    {
        $user = $this->guard()->user();

        $this->guard()->logout(true);

        event(new Logout($user));

        return $this->success('Logged out');
    }

    public function refreshToken()
    {
        if ($token = $this->guard()->getToken()) {
            $token = $token->get();
        } else {
            return $this->sendFailedTokenRefreshResponse();
        }

        return $this->success([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => config('jwt.ttl') * 60,
        ]);
    }

    public function user(Request $request)
    {
        return $this->success($request->user());
    }

    // we need to do things a bit differently using JWTAuth since it doesn't
    // fire events and the remember.  We also need the token to be setfor later
    // use in the controller, not sure why JWT doesn't do it internally...
    protected function attemptLogin(Request $request)
    {
        if ($token = $this->guard()->attempt($this->credentials($request))) {
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

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        return $this->authenticated($this->guard()->user());
    }

    protected function validateLogin(Request $request)
    {
        // we add remember => true to the request since all token auth are set to remember (no session).
        $request->merge(['remember' => true]);

        return $this->validate($request, [
            'remember'        => 'boolean',
            $this->username() => 'required',
            'password'        => 'required',
        ]);
    }

    protected function credentials(Request $request)
    {
        $creds = $request->only($this->username(), 'password');
        $creds['active'] = 1;

        return $creds;
    }

    protected function authenticated($user)
    {
        if ($token = $this->guard()->getToken()) {
            $token = $token->get();
        } else {
            return $this->sendFailedLoginResponse();
        }
        $user->makeHidden([
            'roles',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        return $this->success([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => config('jwt.ttl') * 60,
            'user'         => $user,
        ]);
    }
}
