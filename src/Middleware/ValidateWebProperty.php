<?php

namespace AppCompass\AppCompass\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use AppCompass\AppCompass\Models\WebProperty;

class ValidateWebProperty
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
        $request->web_property = $property = WebProperty::fromRequest($request);

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
