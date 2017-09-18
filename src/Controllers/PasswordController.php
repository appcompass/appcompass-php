<?php

namespace P3in\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordController extends BaseController
{

    use SendsPasswordResetEmails, ResetsPasswords;

    protected function broker()
    {
        return Password::broker();
    }

    protected function sendResetLinkResponse($response)
    {
        return $this->success(['status' => trans($response)]);
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return $this->error(['email' => [trans($response)]], 422);
    }

    protected function sendResetResponse($response)
    {
        return $this->success(['status' => trans($response)]);
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        return $this->error(['token' => [trans($response)]], 422);
    }

    protected function rules()
    {
        return [
            'token'    => 'required',
            'email'    => 'required|email|exists:users',
            'password' => 'required|confirmed|min:6',
        ];
    }

    protected function validationErrorMessages()
    {
        return [
            'email.exists' => trans('passwords.user'),
        ];
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = $password;

        $user->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));

        $this->guard()->login($user);
    }
}
