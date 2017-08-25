<?php

namespace P3in\Commands;

use Illuminate\Console\Command;

class Install extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'app-compass:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install App Compass.';

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

        $this->call('migrate');
        $this->call('passport:install');

    }
}
