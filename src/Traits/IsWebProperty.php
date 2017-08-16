<?php

namespace P3in\Traits;

use P3in\Models\Menu;
use P3in\Models\Scopes\WebPropertyScope;
use P3in\Models\WebProperty;

trait IsWebProperty
{
    protected static function boot()
    {
        parent::boot();

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
}
