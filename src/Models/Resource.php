<?php

namespace P3in\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use P3in\Interfaces\Linkable;
use P3in\Models\MenuItem;
use P3in\Traits\HasJsonConfigFieldTrait;
use P3in\Traits\HasPermission;

class Resource extends Model implements Linkable
{
    use HasPermission, HasJsonConfigFieldTrait;

    protected $fillable = [
        'form_id',
        'config',
        'resource',
    ];


    protected $casts = [
        'config' => 'object'
    ];

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Makes a menu item.
     *
     * @return     MenuItem
     */
    public function makeMenuItem($order = 0): MenuItem
    {

        // @TODO find a way to auto-determine order based on previous insertions

        $item = new MenuItem([
            'title' => $this->getConfig('meta.title'),
            'alt' => $this->getConfig('meta.title'),
            'order' => $order,
            'new_tab' => false,
            'url' => null,
            'clickable' => true,
            'icon' => null,
        ]);

        $item->navigatable()->associate($this);

        return $item;
    }

    /**
     * Menu Handling
     *
     * @return     <type>  The type attribute.
     */
    public function getTypeAttribute()
    {
        return 'Resource';
    }

    public function getUrlAttribute()
    {
        $name = $this->attributes['resource'];

        // Validate the route.  It must exist in the list of routes for this app.
        $router = app()->make('router');
        $route = $router->getRoutes()->getByName($name);

        if (is_null($route)) {
            throw new \Exception('The Resource ('.$name.') does not have a coresponding route definition.  Please specify it in the routes.php file and then proceed.');

        }

        $params = $route->parameterNames();
        array_walk($params, function(&$val){
            $val = ':'.str_plural($val);
            return $val;
        });

        $url = route($name, $params, false);

        return preg_replace(['/\/edit$/', '/\/show$/'], ['/',''], $url);
    }

    /**
     * Sets the form.
     *
     * @param      Form    $form   The form
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function setForm(Form $form)
    {
        return $this->associate($form);
    }

    public static function resolve($name)
    {
        return static::whereResource($name)->firstOrFail();
    }

    public function vueRoute()
    {
        $name = $this->resource;
        $meta = (object) $this->getConfig('meta');

        // @TODO: can prob remove.  here for backwards compatibility only.
        $meta->resource = $name;

        return [
            'path' => $this->url,
            'name' => $name,
            'meta' => $meta,
            'component' => $this->getConfig('component'),
        ];

    }

    public static function build($val)
    {
        $resource = static::create([
            'resource' => $val,
        ]);

        return $resource;
    }

    public function setLayout(string $val = '')
    {
        return $this->setConfig('layout', $val);
    }

    public function setComponent(string $val = '')
    {
        return $this->setConfig('component', $val);
    }

    public function setTitle(string $val = '')
    {
        return $this->setConfig('meta.title', $val);
    }

    public function requiresAuth(bool $val = true)
    {
        return $this->setConfig('meta.requiresAuth', $val);
    }

    public static function byRoute($name)
    {
        return self::whereResource($name)->firstOrFail();
    }
}
