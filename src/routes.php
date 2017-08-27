<?php
Route::group([
    'middleware' => ['app_compass_api'],
    'namespace'  => 'P3in\Controllers',
], function ($router) {
    $router->get('routes', 'AppResourcesController@routes');
    $router->get('get-resources/{route?}', 'AppResourcesController@resources');
    $router->get('menus', 'AppResourcesController@getMenus');
});

Route::group([
    'prefix'     => 'auth',
    'middleware' => ['app_compass_api'],
    'namespace'  => 'P3in\Controllers',
], function ($router) {
    // login and auth check
    $router->post('login', 'AuthController@login');
    $router->get('logout', 'AuthController@logout')->middleware(['app_compass_api']);
    $router->get('user', 'AuthController@user')->middleware('app_compass_api');

    // jwt token stuff.
    $router->get('token/refresh', 'AuthController@refreshToken')->middleware('app_compass_refresh_token');

    // registration
    $router->post('register', 'AuthController@register');
    $router->get('activate/{code}', 'AuthController@activate')->name('cp-activate-account');

    // password reset
    $router->post('password/email', 'PasswordController@sendResetLinkEmail');
    $router->post('password/reset', 'PasswordController@reset');
});

Route::group([
    'namespace'  => 'P3in\Controllers',
    'middleware' => ['app_compass_auth', 'app_compass_api'],
], function ($router) {
    $router->get('dashboard', 'AppResourcesController@getDashboard')->name('cp-dashboard');
    $router->resource('users', UsersController::class);
    $router->resource('roles', RolesController::class);
    $router->resource('roles.permissions', RolePermissionsController::class);
    $router->resource('permissions', PermissionsController::class);
    $router->resource('users.roles', UserRolesController::class);
    $router->resource('users.permissions', UserPermissionsController::class);

    $router->resource('resources', ResourcesController::class);
    $router->resource('forms', FormsController::class);
});
