<?php

namespace P3in\Providers;

use App\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use P3in\Commands\AddUserCommand;
use P3in\Middleware\SanitizeEmail;
use P3in\Middleware\ValidateControlPanel;
use P3in\Middleware\ValidateWebProperty;
use P3in\Models\Field;
use P3in\Models\Form;
use P3in\Models\Menu;
use P3in\Models\Permission;
use P3in\Models\Resource;
use P3in\Models\Role;
use P3in\Observers\FieldObserver;
use P3in\Observers\PermissionObserver;
use Tymon\JWTAuth\Http\Middleware\RefreshToken;
use Tymon\JWTAuth\Providers\LaravelServiceProvider;

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
        'auth' => [
            Authenticate::class,
            // 'jwt.refresh'
        ],
        'api'  => [
            'web',
            // @TODO: fix this so we don't have to use the internal web middleware that does extra stuff we don't need in an API based system.
//            'throttle:60,1',
//            SubstituteBindings::class,
            ValidateWebProperty::class,
            SanitizeEmail::class,
        ],
        'cp'   => [
            ValidateControlPanel::class,
            ValidatePostSize::class,
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
//        'jwt.auth'    => GetUserFromToken::class,
        'jwt.refresh' => RefreshToken::class,

    ];

    protected $commands = [
        AddUserCommand::class,
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

        $this->registerDependentPackages();
    }

    public function boot()
    {
        parent::boot();

        $this->publishes([
            __DIR__ . '/../config/app-compass.php' => config_path('app-compass.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
    }

    // @TODO: remove with Laravel 5.5

    /**
     * Load dependent packages.
     */
    protected function registerDependentPackages()
    {
        $this->app->register(ModuleServiceProvider::class);
        $this->app->register(FormBuilderServiceProvider::class);
        $this->app->register(AppCompassEventServiceProvider::class);
        $this->app->register(LaravelServiceProvider::class);
    }
}
