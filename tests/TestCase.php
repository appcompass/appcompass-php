<?php
namespace AppCompass\AppCompass\Tests;

use AppCompass\AppCompass\Providers\AppCompassServiceProvider;
use AppCompass\FormBuilder\Providers\FormBuilderServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Faker\Factory as Faker;

class TestCase extends OrchestraTestCase
{
    protected $faker;

    protected function getPackageProviders($app)
    {
        return [
            AppCompassServiceProvider::class,
            FormBuilderServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app-compass', (include __DIR__ . '/../src/config/app-compass.php'));
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create();
        $this->loadLaravelMigrations();
        $this->artisan('migrate')->run();
    }

    public function tearDown(): void
    {
        $this->artisan('migrate:reset')->run();
        parent::tearDown();
    }
}
