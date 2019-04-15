<?php

namespace AppCompass\AppCompass\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use AppCompass\AppCompass\Interfaces\Linkable;
use AppCompass\FormBuilder\Models\Form;
use AppCompass\FormBuilder\Traits\HasJsonConfigFieldTrait;
use AppCompass\AppCompass\Traits\HasPermission;

class Resource extends Model implements Linkable
{
    use HasPermission, HasJsonConfigFieldTrait;

//    protected $fillable = [
//        'config',
//        'name',
//    ];

    protected static $unguarded = true;

    protected $casts = [
        'config' => 'object',
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
     * WebProperty
     *
     * @return     BelongsTo    WebProperty
     */
    public function web_property()
    {
        return $this->belongsTo(WebProperty::class);
    }

    /**
     * Makes a menu item.
     *
     * @return     MenuItem
     */
    public function makeMenuItem($order = 0) : MenuItem
    {

        // @TODO find a way to auto-determine order based on previous insertions

        $item = new MenuItem([
            'title'     => $this->getConfig('meta.title'),
            'alt'       => $this->getConfig('meta.title'),
            'order'     => $order,
            'new_tab'   => false,
            'url'       => null,
            'clickable' => true,
            'icon'      => null,
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
        return get_class($this);
//        return 'Resource';
    }

    public function getUrlAttribute()
    {
        $name = $this->attributes['name'];

        // Validate the route.  It must exist in the list of routes for this app.
        $router = app()->make('router');
        $route = $router->getRoutes()->getByName($name);

        if (is_null($route)) {
            throw new \Exception('The Resource (' . $name . ') does not have a coresponding route definition.  Please specify it in the routes.php file and then proceed.');
        }

        $params = $route->parameterNames();
        array_walk($params, function (&$val) {
            $val = ':' . str_plural($val);

            return $val;
        });

        $url = route($name, $params, false);

        return preg_replace(['/\/edit$/', '/\/show$/'], ['/', ''], $url);
    }

    /**
     * Sets the form.
     *
     * @param      Form $form The form
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function setForm(Form $form)
    {
        $this->form()->associate($form);

        $this->save();

        return $this;
    }

    public static function resolve($name)
    {
        return static::whereName($name)->firstOrFail();
    }

    public function vueRoute()
    {
        $name = $this->name;
        $meta = (object)$this->getConfig('meta');

        // @TODO: can prob remove.  here for backwards compatibility only.
        $meta->resource = $name;

        return [
            'path'      => $this->url,
            'name'      => $name,
            'meta'      => $meta,
            'component' => $this->getConfig('component'),
        ];
    }

    // @TODO: Convenience vs. Clarity, that is the question...
    public static function build($name, WebProperty $web_property, Form $form = null, $permission = null)
    {
        try {
            $resource = static::byRoute($name);
        } catch (ModelNotFoundException $e) {
            $resource = new static([
                'name' => $name,
            ]);
        }

        $resource->web_property()->associate($web_property);

        if ($form) {
            $resource->setForm($form);
        }
        if ($permission) {
            $resource->setPermission($permission);
        }

        $resource->save();

        return $resource;
    }

    public static function buildAll(array $names, WebProperty $web_property, Form $form = null, $permission = null)
    {
        foreach ($names as $name) {
            static::build($name, $web_property, $form, $permission);
        }
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
        return self::whereName($name)->firstOrFail();
    }

    public function renderForm($mode = null)
    {
        $form = $this->form;

        // $form->setRenderWhere(['type' => 'String']);

        switch ($mode) {
            case 'list': //@TODO: Delete/rename, index is the resource to use.
            case 'index':
                $form->setRenderWhere(['to_list' => true]);
                break;
            case 'edit': //@TODO: Delete/rename, show is the resource to use.
            case 'show': //@TODO: show and update use the same set of fields.
            case 'update':
            case 'create': //@TODO: create and store use the same set of fields.
            case 'store':
            case 'destroy': //@TODO: add field(s) for validation on delete. for example, "hey this is a related field, first please move or delete xyz".
                $form->setRenderWhere(['to_edit' => true]);
                break;
        }

        return $form->render();
    }
}
