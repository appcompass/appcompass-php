<?php

namespace P3in\Providers;

use App\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use P3in\Commands\AddUserCommand;
use P3in\Interfaces\FormsRepositoryInterface;
use P3in\Interfaces\PermissionsRepositoryInterface;
use P3in\Interfaces\ResourcesRepositoryInterface;
use P3in\Interfaces\RolePermissionsRepositoryInterface;
use P3in\Interfaces\RolesRepositoryInterface;
use P3in\Interfaces\UserPermissionsRepositoryInterface;
use P3in\Interfaces\UserRolesRepositoryInterface;
use P3in\Interfaces\UsersRepositoryInterface;
use P3in\Middleware\SanitizeEmail;
use P3in\Middleware\ValidateControlPanel;
use P3in\Middleware\ValidateWebProperty;
use P3in\Models\Field;
use P3in\Models\Form;
use P3in\Models\Permission;
use P3in\Models\Resource;
use P3in\Models\Role;
use P3in\Observers\FieldObserver;
use P3in\Observers\PermissionObserver;
use P3in\Repositories\FormsRepository;
use P3in\Repositories\PermissionsRepository;
use P3in\Repositories\ResourcesRepository;
use P3in\Repositories\RolePermissionsRepository;
use P3in\Repositories\RolesRepository;
use P3in\Repositories\UserPermissionsRepository;
use P3in\Repositories\UserRolesRepository;
use P3in\Repositories\UsersRepository;
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
        UsersRepositoryInterface::class           => UsersRepository::class,
        UserPermissionsRepositoryInterface::class => UserPermissionsRepository::class,
        PermissionsRepositoryInterface::class     => PermissionsRepository::class,
        RolesRepositoryInterface::class           => RolesRepository::class,
        RolePermissionsRepositoryInterface::class => RolePermissionsRepository::class,
        UserRolesRepositoryInterface::class       => UserRolesRepository::class,
        ResourcesRepositoryInterface::class       => ResourcesRepository::class,
        FormsRepositoryInterface::class           => FormsRepository::class,
    ];

    protected $routeBindings = [
        'user'       => User::class,
        'permission' => Permission::class,
        'role'       => Role::class,
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
        $this->publishes([
            __DIR__ . '/../config/app-compass.php' => config_path('app-compass.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
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
