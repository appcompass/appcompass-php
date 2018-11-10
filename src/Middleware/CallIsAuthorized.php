<?php

namespace AppCompass\Middleware;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use AppCompass\Models\PermissionsRequired;
use AppCompass\Models\PermissionsRequired\PermissionItems\Route;

class CallIsAuthorized
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $ability = null, $boundModelName = null)
    {

        /*
         * @TODO way of resolving models out of the route
         * this will be moved into a generic validation
         * middleware. can also be used to check permission
         * on a single item level, since we have the exact instance resolved
         */
        foreach ($request->route()->parameters() as $key => $value) {
            if (is_object($request->route($key))) {
                $model = $request->route($key);

                // \Log::info($model);

                if (property_exists($model, 'rules')) {

                    // @TODO This can be used for automatic validation instead of request

                    // \Log::info($model::$rules);
                }

                // \Log::info(get_class($model));

                // \Log::info($model->id);

                // AppCompass\Models\Website@2 req user@7
            }
        }

        $route = $request->route();

        $req_perm = PermissionsRequired::retrieve(new Route($route->uri()));

        if ($req_perm->count()) {
            if ($request->user()->hasPermission($req_perm->type)) {
                return $next($request);
            } else {
                return response(['message' => 'Unauthorized.'], 403);
            }
        }

        return $next($request);
    }

    /**
     *
     */
    public function getModelFromRequest(Request $request, $boundModelName)
    {
        if (is_null($boundModelName)) {
            return;
        }

        return $request->route($boundModelName);
    }
}
