<?php

namespace AppCompass\Commands;

use Illuminate\Console\Command;
use AppCompass\Builders\EnvBuilder;

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
        $bar = $this->output->createProgressBar(7);

        $this->call('vendor:publish', [
            '--all' => true,
        ]);
        $bar->advance();

        $app_url = $this->ask('What is your app URL?');
        $bar->advance();
        $cp_site_name = $this->ask('What is your Control Panel Name?');
        $bar->advance();
        $cp_url = $this->getCpUrl();
        $bar->advance();

        list($cp_site_scheme, $cp_site_host) = explode('://', $cp_url);

        $envBuilder = new EnvBuilder();

        $envBuilder->updateOrCreate([
            'APP_URL' => $app_url,
            'ADMIN_WEBSITE_NAME' => $cp_site_name,
            'ADMIN_WEBSITE_SCHEME' => $cp_site_scheme,
            'ADMIN_WEBSITE_HOST' => $cp_site_host,
        ]);

        $bar->advance();
        $this->info('Starting Migrations (this may take a moment).');
        $this->call('migrate');
        $bar->advance();
        $this->info('Running post migration configs.');
        $this->call('passport:install');
        $bar->finish();
    }

    private function getCpUrl()
    {
        $cp_url = $this->ask('What is your Control Panel URL?');

        if (filter_var($cp_url, FILTER_VALIDATE_URL) === false) {
            $this->error('Invalid url.  Make sure you include http://, or https://.');
            $cp_url = $this->getCpUrl();
        }

        return $cp_url;
    }

}
