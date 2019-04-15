<?php

namespace AppCompass\AppCompass\Providers;

use Illuminate\Routing\Middleware\SubstituteBindings;
use Laravel\Passport\Passport;
use AppCompass\AppCompass\Commands\AddUser;
use AppCompass\AppCompass\Commands\Install;
use AppCompass\AppCompass\Listeners\UserEventSubscriber;
use AppCompass\AppCompass\Middleware\SanitizeEmail;
use AppCompass\AppCompass\Middleware\ValidateWebProperty;
use AppCompass\AppCompass\Models\User;
use App\Company;
use AppCompass\FormBuilder\Models\Field;
use AppCompass\FormBuilder\Models\Form;
use AppCompass\AppCompass\Models\Menu;
use AppCompass\AppCompass\Models\Permission;
use AppCompass\AppCompass\Models\Resource;
use AppCompass\AppCompass\Models\Role;
use AppCompass\FormBuilder\Observers\FieldObserver;
use AppCompass\AppCompass\Observers\PermissionObserver;
use AppCompass\AppCompass\Observers\UserObserver;
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
        UserObserver::class       => User::class,
    ];

    protected $subscribe = [
        UserEventSubscriber::class,
    ];

    protected $appBindings = [
    ];

    protected $routeBindings = [
        'company'    => Company::class,
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


        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'app-compass');

        $this->publishes([
            __DIR__ . '/../config/app-compass.php' => config_path('app-compass.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/../routes.php');

        Passport::routes();
    }
}
