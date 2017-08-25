<?php

namespace P3in\Traits;

use P3in\Models\Menu;
use P3in\Models\Scopes\WebPropertyScope;
use P3in\Models\WebProperty;
use P3in\Observers\IsWebPropertyObserver;

trait IsWebProperty
{
    protected static function bootIsWebProperty()
    {
        //for handling changes.
        static::observe(IsWebPropertyObserver::class);
        
        // for handling reads.
        static::addGlobalScope(new WebPropertyScope());
    }

    public function web_property()
    {
        return $this->belongsTo(WebProperty::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'web_property_id', 'web_property_id');
    }

    public function scopeByHost($query, $host)
    {
        $query->where('web_properties.host', $host);
    }

    public static function create(array $attributes = [])
    {
        $model = static::query()->create($attributes);

        $model->refresh();

        return $model;
    }
}
