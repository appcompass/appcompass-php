<?php

namespace AppCompass\Models;

use Illuminate\Database\Eloquent\Model;
use AppCompass\Interfaces\Linkable;

use AppCompass\Traits\HasPermission;

class Link extends Model implements Linkable
{
    use HasPermission;

    protected $fillable = [
        'title',
        'url',
        'alt',
        'new_tab',
        'clickable',
        'icon',
        'content',
    ];

    private $rules = [
        'title'   => 'required',
        'url'     => 'required_if:clickable,true',
        'alt'     => 'required',
        'new_tab' => 'required',
        // 'clickable' => ''
    ];

    public $appends = ['type'];

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
     * @param      integer $order The order
     *
     * @return     MenuItem  ( description_of_the_return_value )
     */
    public function makeMenuItem($order = 0) : MenuItem
    {
        $attributes = collect($this->getAttributes())
            ->only(['title', 'alt', 'order', 'new_tab', 'url', 'clickable', 'icon']);
        $item = new MenuItem($attributes->all());
        $item->order = $order;

        $item->navigatable()->associate($this);

        return $item;
    }

    public function getTypeAttribute()
    {
        return self::class;
//        return 'Link';
    }
}
