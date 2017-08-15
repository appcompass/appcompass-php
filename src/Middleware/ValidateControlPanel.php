<?php

namespace P3in\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use P3in\Models\WebProperty;

class ValidateControlPanel
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //        dd(config('app-compass'));
        $request->web_property = $property = WebProperty::fromRequest($request, config('app-compass.admin_site_host'));

        Config::set('app.url', $property->url);
        Config::set('app.name', $property->name);
        Config::set('mail.from.address', 'website@' . $property->host);
        Config::set('mail.from.name', $property->name);

        return $next($request);
    }

    public function terminate()
    {
    }
}
