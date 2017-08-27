<?php


namespace P3in\Traits;

use Illuminate\Http\Request;
use P3in\Events\Registered;
use P3in\Events\Registering;
use P3in\Models\User;

trait RegistersUsers
{
    public function register(Request $request)
    {
        $data = $request->validate($this->registrationValidationRules(), $this->registrationValidationMessages());

        event(new Registering($data));

        $user = $this->createRegisteredUser($data);

        $user->sendRegistrationConfirmationNotification();

        event(new Registered($user, $data));

        $user->makeHidden(['id','updated_at', 'created_at']);
        return $this->registered($user);
    }

    public function activate(Request $request, $code)
    {
        try {
            $user = User::where('activation_code', $code)->firstOrFail();

            if ($user->active) {
                return $this->alreadyActiveResponse();
            }
            $user->active = true;
            $user->save();

            if ($this->afterLoginAttempt(($this->guard()->login($user)))) {
                return $this->sendLoginResponse($request);
            } else {
                return $this->sendFailedLoginResponse();
            }
        } catch (ModelNotFoundException $e) {
            return $this->noCodeResponse();
        }

        return $user;
    }

    protected function registrationValidationRules()
    {
        $rules = User::$rules;
        $rules['email'] .= '|unique:users';
        $rules['password'] .= '|required';

        return $rules;
    }

    protected function registrationValidationMessages()
    {
        return [
        ];
    }

    protected function createRegisteredUser(array $data)
    {
        $user = new User($data);
        $user->activation_code = str_random(64);
        $user->save();

        return $user;
    }

    protected function registered($user)
    {
        return $this->success([
            'message' => trans('registration.check-email'),
            'user'    => $user,
        ]);
    }

    protected function noCodeResponse()
    {
        return $this->error(trans('registration.activation-failed'), 422);
    }

    protected function alreadyActiveResponse()
    {
        return $this->error(trans('registration.already-active'), 422);
    }
}
