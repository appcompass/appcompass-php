<?php

namespace P3in;

class AppCompassModule extends BaseModule
{
    public $module_name = 'app-compass';

    protected $publishes = [
    ];

    // @TODO: programatically run: php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\JWTAuthServiceProvider"
    public function __construct()
    {
        // \Log::info('Loading <Cp> Module');
    }

    public function bootstrap()
    {
    }

    public function register()
    {
    }
}
