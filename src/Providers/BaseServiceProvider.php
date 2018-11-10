<?php

namespace AppCompass\Providers;

use Gate;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider
{

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    // Middleware
    protected $middleware       = [];
    protected $middlewareGroups = [];
    protected $routeMiddleware  = [];

    protected $commands = [];

    // Application bindings (interface -> concretion)
    protected $appBindings = [];

    // Application bindings (route model bindings)
    protected $routeBindings = [];

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Observers
     */
    protected $observe = [];

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [];

    /**
     * Resolved instance of app's dispatcher
     */
    protected $dispatcher;

    /**
     * IoC instance of Gate
     */
    protected $gate;

    /**
     * Register
     */
    public function register()
    {
        $this->registerBinds();

        $this->registerMiddleware();

        $this->commands($this->commands);
    }

    /**
     * Boot
     */
    public function boot()
    {
        $this->dispatcher = App(DispatcherContract::class);

        $this->registerObservers();

        $this->bindToRoute();

        $this->registerPolicies($this->gate);

        $this->registerListeners($this->dispatcher);
    }

    /**
     * Register Bindings
     */
    public function registerBinds()
    {
        foreach ($this->appBindings as $key => $val) {
            $this->app->bind($key, $val);
        }
    }

    /**
     * Bind to route
     */
    public function bindToRoute()
    {
        $loader = AliasLoader::getInstance();

        foreach ($this->routeBindings as $key => $model) {
            $this->app['router']->model($key, $model);
            $loader->alias(class_basename($model), $model);
        }
    }

    /**
     * Register Middleware
     */
    public function registerMiddleware()
    {
        foreach ($this->middleware as $middleware) {
            $this->app['router']->pushMiddleware($middleware);
        }

        foreach ($this->middlewareGroups as $group => $middlewares) {
            foreach ($middlewares as $middleware) {
                $this->app['router']->pushMiddlewareToGroup($group, $middleware);
            }
        }

        foreach ($this->routeMiddleware as $key => $middleware) {
            $this->app['router']->aliasMiddleware($key, $middleware);
        }
    }

    /**
     *  Registers observers
     *
     */
    public function registerObservers()
    {
        foreach ($this->observe as $observer => $models) {
            if (is_array($models)) {
                foreach ($models as $model) {
                    $model::observe($observer);
                }
            } else {
                $models::observe($observer);
            }
        }
    }

    /**
     * Register the application's event listeners.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     */
    public function registerListeners(DispatcherContract $events)
    {
        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }

        foreach ($this->subscribe as $subscriber) {
            $events->subscribe($subscriber);
        }
    }

    /**
     * Register the application's policies.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        return $this->listen;
    }
}
