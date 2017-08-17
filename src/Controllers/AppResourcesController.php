<?php

namespace P3in\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use P3in\Models\Permission;
use P3in\Models\Resource;

class AppResourcesController extends Controller
{
    public function getDashboard(Request $request)
    {
        return response()->json(['coming soon.']);
    }

    public function routes(Request $request)
    {
        $cacheKey = $request->web_property->id . '_' . (Auth::check() ? Auth::user()->id : 'guest');
        // forever? we would then need to clear this cache when updating a user permission though.
        // @TODO: fix form render so it's not running queries in loops.
        $data = [
            // 'resources' => $this->getResources(),
            'routes' => $request->web_property->buildRoutesTree(),
        ];

        return response()->json($data);
    }

    public function resources(Request $request, string $route = null)
    {
        return response()->json($this->getResources($route));
    }

    public function getMenus(Request $request)
    {
        $rtn = [];

        $menus = $request
            ->web_property
            ->menus()->with('items')->get();

        $permIds = Auth::check() ? (array)Cache::tags('auth_permissions')->get(Auth::user()->id) : [];
        foreach ($menus as $menu) {
            $rtn[$menu->name] = $menu->render(true, $permIds);
        }

        return $rtn;
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
                $route_type = substr($route, strrpos($route, '.') + 1);

                $resource->form = $resource->renderForm($route_type);
            }
        });

        return $route ? $resources->first() : [
            'resources' => $resources,
        ];
    }
}