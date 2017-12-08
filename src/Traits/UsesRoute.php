<?php

namespace P3in\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use P3in\Models\Resource;

trait UsesRoute
{
    protected $route_params = null;
    protected $route_name = null;
    protected $resource = null;

    public function setRouteInfo($force = false)
    {
        if (is_null($this->route_params) || $force) {
            $route = Route::current();
            if ($route){
                $this->route_name = $route->getName();
                $this->route_params = $route->parameters();

                $this->resource = Resource::
                    // @TODO: fix this to bring back byAllowed()
                    // byAllowed()
                    where('name', $this->route_name)
                    ->with('form')
                    ->first()
                ;

            }

        }
    }

    public function getRouteType()
    {
        $this->setRouteInfo();
        if(strrpos($this->route_name, '.') !== false){
            return substr($this->route_name, strrpos($this->route_name, '.') + 1);
        }
        return $this->route_name;
    }

    public function getApiUrl()
    {
        $this->setRouteInfo();

        $keys = explode('.', $this->route_name);
        $values = array_values(array_map(function ($param) {
            return $this->getKeyFromParam($param);
        }, $this->route_params));

        $segments = [''];
        $route_type = $this->getRouteType();

        for ($i = 0; $i < count($keys); $i++) {
            if ($keys[$i] !== $route_type) {
                $segments[] = $keys[$i];
                if (isset($values[$i])) {
                    $segments[] = $values[$i];
                }
            }
        }

        return implode('/', $segments);
    }

    public function getResourceUrl($depth = null)
    {
        $this->setRouteInfo();

        if ($ext_length = strlen($this->getRouteType())){
            $ext_length = -(1+$ext_length);
        }
        $route_name = substr($this->route_name, 0, $ext_length);

        $keys = explode('.', $route_name);
        $values = array_values(array_map(function ($param) {
            return $this->getKeyFromParam($param);
        }, $this->route_params));

        $segments = [''];

        for ($i = 0; $i < count($keys); $i++) {
            $segments[] = $keys[$i];
            if (isset($values[$i])) {
                $segments[] = $values[$i];
            }
        }

        if (is_int($depth)){
            $filtered = array_values(array_filter($segments));
            $segments = [''];
            for ($i = 0; $i < $depth; $i++) {
                $segments[] = $filtered[$i];
            }
        }

        return implode('/', $segments);
    }

    public function getRouteParam($name, $getModel = false)
    {
        $this->setRouteInfo($getModel);

        if (isset($this->route_params[$name])) {
            if ($getModel) {
                return $this->route_params[$name];
            }
            return $this->getKeyFromParam($this->route_params[$name]);
        }

        if (!app()->runningInConsole()){
            return null;
            // throw new \Exception("No route param exist by the name of '{$name}'");
        }
    }

    private function getKeyFromParam($param)
    {
        if ($param instanceof Model) {
            return $param->getKey();
        }
        return $param;
    }
}
