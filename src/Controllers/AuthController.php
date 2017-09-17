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
use P3in\Events\UserCheck;
use P3in\Events\UserUpdated;
use P3in\Rules\UserPassword;
use P3in\Traits\RegistersUsers;

class AuthController extends BaseController
{
    use AuthenticatesUsers, RegistersUsers;

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

    public function user($fire_event = true)
    {
        $user = auth()->user();

        if ($fire_event){
            event(new UserCheck($user));
        }

        $user->makeHidden([
            'roles',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        return $this->success($user);
    }

    public function updateUser(Request $request)
    {
        $user = $request->user();

        $rules = User::$rules;

        $rules['current_password'] = ['required_with:password', new UserPassword($user)];

        $data = $request->validate($rules);

        //@TODO: look into laravel to see if there is a better way to exclude a value from validated data.  i.e. check only.
        unset($data['current_password']);

        $user->update($data);

        event(new UserUpdated($user));

        return $this->user(false);
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
