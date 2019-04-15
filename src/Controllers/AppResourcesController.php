<?php

namespace AppCompass\AppCompass\Controllers;

use Illuminate\Http\Request;
use AppCompass\AppCompass\Models\Resource;

class AppResourcesController extends BaseController
{
    public function getDashboard(Request $request)
    {
        return response()->json(['coming soon.']);
    }

    public function routes(Request $request)
    {
        if ($routes = $request->web_property->buildRoutesTree()){
            $data = [
                'routes' => $routes,
            ];
            return $this->output($data);
        }
        if ($user = $request->user()){
            $message = "You are not authorized to use this website.";
            if ($user->companies->count() >= 2){
                $name = $user->current_company->name;
                $message .= " Please contact your {$name} Administrator.";
            }
            return $this->error($message, 401);
        }

        return $this->error("You must be logged in to use this website.", 401);
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
            ->menus()->with(['items' => function ($query) {
                $query->byAllowed();
            }])->get();

        foreach ($menus as $menu) {
            $rtn[$menu->name] = $menu->render(true);
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
