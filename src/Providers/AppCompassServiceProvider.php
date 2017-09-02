<?php

namespace P3in\Providers;

use App\User;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Laravel\Passport\Passport;
use P3in\Commands\AddUser;
use P3in\Commands\Install;
use P3in\Middleware\SanitizeEmail;
use P3in\Middleware\ValidateWebProperty;
use P3in\Models\Field;
use P3in\Models\Form;
use P3in\Models\Menu;
use P3in\Models\Permission;
use P3in\Models\Resource;
use P3in\Models\Role;
use P3in\Observers\FieldObserver;
use P3in\Observers\PermissionObserver;
use Tymon\JWTAuth\Http\Middleware\Check;
use Tymon\JWTAuth\Http\Middleware\RefreshToken;
use Tymon\JWTAuth\Http\Middleware\Authenticate;

class AppCompassServiceProvider extends BaseServiceProvider
{

    // @TODO: Implement the other middleware types commented out here.

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'app_compass_auth' => [
            Authenticate::class,
            // 'jwt.refresh'
        ],
        'app_compass_api'  => [
            // // 'throttle:60,1',
            // Check::class,
            ValidateWebProperty::class,
            SubstituteBindings::class,
            SanitizeEmail::class,
            //     ValidatePostSize::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        // 'jwt.auth'    => GetUserFromToken::class,
        'app_compass_refresh_token' => RefreshToken::class,
    ];

    protected $commands = [
        Install::class,
        AddUser::class,
    ];

    protected $observe = [
        PermissionObserver::class => Permission::class,
        FieldObserver::class      => Field::class,
    ];

    protected $appBindings = [
    ];

    protected $routeBindings = [
        'user'       => User::class,
        'permission' => Permission::class,
        'role'       => Role::class,
        'menu'       => Menu::class,
        'resource'   => Resource::class,
        'form'       => Form::class,
    ];

    /**
     * List of policies to bind
     */
    protected $policies = [
    ];

    /**
     * Register
     */
    public function register()
    {
        parent::register();
    }

    public function boot()
    {
        parent::boot();

        $this->publishes([
            __DIR__ . '/../config/app-compass.php' => config_path('app-compass.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/../routes.php');

        Passport::routes();
    }
}
