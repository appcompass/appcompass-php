<?php

namespace P3in\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use P3in\Events\Login;
use P3in\Events\Logout;
use P3in\Models\Resource;
use P3in\Models\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CpResourcesController extends Controller
{
    public function getDashboard(Request $request)
    {
        return response()->json(['coming soon.']);
    }

    public function routes(Request $request)
    {
        $cacheKey = $request->website->id.'_'.(Auth::check() ? Auth::user()->id : 'guest');
        // forever? we would then need to clear this cache when updating a user permission though.
        // @TODO: fix form render so it's not running queries in loops.
        $data = [
            // 'resources' => $this->getResources(),
            'routes' => $request->website->renderer()->buildRoutesTree(),
        ];

        return response()->json($data);
    }

    public function resources(Request $request, string $route = null)
    {
        return response()->json($this->getResources($route));
    }

    private function getResources(string $route = null)
    {
        $query = Resource::byAllowed();

        if ($route) {
            $query->where('resource', $route);
        }

        $resources = $query->with('form')->get();

        $resources->each(function ($resource) {
            if ($resource->form) {
                $route = $resource->resource;
                $route_type = substr($route, strrpos($route, '.')+1);

                $resource->form = $resource->form->render($route_type);
            }
        });

        return $route ? $resources->first() : [
            'resources' => $resources,
        ];
    }
}
