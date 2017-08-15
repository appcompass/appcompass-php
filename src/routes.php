<?php
Route::group([
    'middleware' => ['web', 'cp'],
    'namespace'  => 'P3in\Controllers',
], function ($router) {
    $router->get('routes', 'CpResourcesController@routes');
    $router->get('get-resources/{route?}', 'CpResourcesController@resources');
});

Route::group([
    'prefix'     => 'auth',
    'middleware' => ['web'],
    'namespace'  => 'P3in\Controllers',
], function ($router) {
    // login and auth check
    $router->post('login', 'AuthController@login');
    $router->get('logout', 'AuthController@logout')->middleware(['auth']);
    $router->get('user', 'AuthController@user')->middleware('auth');

    // registration
    $router->post('register', 'AuthController@register');
    $router->get('activate/{code}', 'AuthController@activate')->name('cp-activate-account');

    // password reset
    $router->post('password/email', 'PasswordController@sendResetLinkEmail');
    $router->post('password/reset', 'PasswordController@reset');
});

Route::group([
    'namespace'  => 'P3in\Controllers',
    'middleware' => ['auth', 'api'],
], function ($router) {
    $router->get('dashboard', 'CpResourcesController@getDashboard')->name('cp-dashboard');
    $router->resource('users', UsersController::class);
    $router->resource('roles', RolesController::class);
    $router->resource('roles.permissions', RolePermissionsController::class);
    $router->resource('permissions', PermissionsController::class);
    $router->resource('users.roles', UserRolesController::class);
    $router->resource('users.permissions', UserPermissionsController::class);

    $router->resource('resources', ResourcesController::class);
    $router->resource('forms', FormsController::class);
});
