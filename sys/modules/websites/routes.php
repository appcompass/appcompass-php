<?php

Route::group([
    'prefix' => 'api',
    'namespace' => 'P3in\Controllers',
    'middleware' => 'api',
], function($router) {
    $router->resource('websites', WebsitesController::class);
    $router->resource('websites.pages', WebsitePagesController::class);
    $router->resource('websites.menus', WebsiteMenusController::class);
    $router->resource('menus', MenusController::class);
});