<?php

namespace P3in\Commands;

use Illuminate\Console\Command;
use P3in\Models\Group;
use P3in\Models\Permission;
use P3in\Models\User;

class AddUserCommand extends Command
{
    /**
    * The name and signature of the console command.
    *
    * @var string
    */
    protected $name = 'p3cms:create-users';

    /**
    * The console command description.
    *
    * @var string
    */
    protected $description = 'Create a super admin User';

    /**
    * Create a new command instance.
    *
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * Execute the console command.
    *
    * @return mixed
    */
    public function handle()
    {

        $this->info('Lets get started!');
        $userInput = $this->fetchInput();

    }

    private function fetchInput()
    {
        $first_name = $this->ask('What\'s your first name?');
        $last_name = $this->ask('What\'s your last name?');
        $phone = $this->ask('What\'s your phone number?');
        $email = $this->fetchEmail();
        $password = $this->fetchPassword();

        $user = User::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => $password,
            'phone' => $phone,
            'active' => true
        ]);

        $user->permissions()
            ->attach(Permission::firstOrFail()->id);

        $user->addToGroup(Group::where('name', 'cp-admin')->firstOrFail());
        $this->info('User created successfully!');

    }
    private function fetchEmail()
    {
        $email = $this->ask('Enter your email address to be used as your username.');

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error('Invalid email address.');
            $email = $this->fetchEmail();
        }

        if(User::where('email', $email)->count()){
            $this->error('email is already being used, try resetting your password.');
            die();
        }

        return $email;
    }
    private function fetchPassword()
    {
        $password = $this->secret('Enter your desired password.');
        $password_again = $this->secret('Retype your password.');

        if ($password != $password_again) {
            $this->error('Passwords did not match, please try again.');
            $password = $this->fetchPassword();
        }

        return $password;
    }
}
