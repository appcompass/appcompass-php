<?php

namespace P3in\Traits;


use Illuminate\Support\Facades\Route;

trait UsesRoute
{
    protected $route_params = null;
    protected $route_name = null;

    public function setRouteInfo($force = false)
    {
        if (is_null($this->route_params) || $force){
            $route = Route::current();

            $this->route_name = $route->getName();
            $this->route_params = $route->parameters();
        }

    }

    public function getRouteType()
    {
        $this->setRouteInfo();

        return substr($this->route_name, strrpos($this->route_name, '.') + 1);
    }

    public function getApiUrl()
    {
        $this->setRouteInfo();

        $keys = explode('.', $this->route_name);
        $values = array_values(array_map(function ($param) {
            if (is_string($param)){
                return $param;
            }
            return $param->getKey();
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

    public function getRouteParam($name)
    {
        $this->setRouteInfo();

        if (isset($this->route_params[$name])){
            return $this->route_params[$name];
        }

        throw new \Exception("No route param exist by the name of '{$name}'");
    }
}