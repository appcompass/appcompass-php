<?php

namespace P3in\Traits;

use P3in\Models\WebProperty;

trait IsAWebProperty
{
    public function __construct()
    {
        $this->append(['scheme', 'host', 'name', 'url']);
        parent::__construct();
    }

    public static function create(array $attributes = [])
    {
        $keys = ['scheme', 'host', 'name'];

        $webProperty = WebProperty::create(array_only($attributes, $keys));

        $website = new self(array_except($attributes, $keys));

        $website->web_property()->associate($webProperty);

        $website->save();
    }

    public function web_property()
    {
        return $this->belongsTo(WebProperty::class);
    }

    /**
     * Gets the url attribute.
     *
     * @return     <type>  The url attribute.
     */
    public function getUrlAttribute()
    {
        return $this->web_property->attributes['scheme'] . '://' . $this->web_property->attributes['host'];
    }

    public function getNameAttribute()
    {
        return $this->web_property->attributes['name'];
    }

    public function getSchemeAttribute()
    {
        return $this->web_property->attributes['scheme'];
    }

    public function getHostAttribute()
    {
        return $this->web_property->attributes['host'];
    }
}
