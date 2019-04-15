<?php

namespace AppCompass\AppCompass\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use AppCompass\AppCompass\Traits\HasPermission;

class MenuItem extends Model
{
    use HasPermission;

    protected $fillable = [
        'title',
        'alt',
        'new_tab',
        'clickable',
        'order',
        'icon',
        'url',
        'req_perm',
    ];

    protected $hidden = [
        'navigatable',
    ];

    public $appends = [
        'content', // get content through the Link if available
        'url',  // make sure we fetch the url from the linked element
        'type', // [Page|Link] for now, but the type in general
    ];

    /**
     * polymorphic
     *
     * @return     MorphTo
     */
    public function navigatable()
    {
        return $this->morphTo();
    }

    /**
     * parent
     *
     * @return     BelongsTo
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * parent
     *
     * @return     BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * children
     *
     * @return     HasMany
     */
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }

    public function allowNullPermission()
    {
        return true;
    }
    /**
     * Gets the url attribute.
     *
     * @return     <type>  The url attribute.
     */
    public function getUrlAttribute()
    {
        // @TODO this to allow an override on Link->url for the current Item
        if (isset($this->attributes['url']) && !is_null($this->attributes['url'])) {
            return $this->attributes['url'];
        }

        if (is_null($this->navigatable)) {
            return null;
        }

        return $this->navigatable->url;
    }

    /**
     * Sets the url attribute.
     *
     * @param      <type>      $url    The url
     *
     * @throws     \Exception  (description)
     */
    public function setUrlAttribute($url)
    {

        // this affects item creation, so we only care if navigatable is set
        if (isset($this->navigatable) && get_class($this->navigatable) !== Link::class) {
            return;
        }

        $this->attributes['url'] = $url;
    }

    /**
     * Gets the local req_perm attribute only if it's populated, otherwise get the item's relationship req_perm if it's
     * set.
     *
     * @return     <int|null>  The req_perm attribute.
     */
    public function getReqPermAttribute()
    {
        if (!empty($this->attributes['req_perm'])) {
            return $this->attributes['req_perm'];
        } elseif (!is_null($this->navigatable)) {
            return $this->navigatable->req_perm;
        } else {
            return null;
        }
    }

    /**
     * Gets the content attribute.
     *
     * @return     <type>  The content attribute.
     */
    public function getContentAttribute()
    {
        if (get_class($this->navigatable) === Link::class) {
            return $this->navigatable->content;
        }

        return null;
    }

    /**
     * Gets the type attribute.
     */
    public function getTypeAttribute()
    {
        return (new \ReflectionClass($this->navigatable_type))->getShortName();
//        // @TODO this should return the navigatable type, but that would involve more queries -f
//        switch ($this->navigatable_type) {
//            case Link::class:
//                return 'Link';
//                break;
//            case Page::class:
//                return 'Page';
//                break;
//        }
    }

    /**
     * MenuItems factory
     *
     * @param      <type>     $model  The model
     *
     * @throws     Exception  Model not allowed
     *
     * @return     <type>     MenuItem generator
     */
    public static function fromModel($model, $order = 0)
    {
        return $model->makeMenuItem($order);
    }

    /**
     * Sets the parent.
     *
     * @param      MenuItem $item The item
     *
     * @return     <type>   ( description_of_the_return_value )
     */
    public function setParent(MenuItem $item = null)
    {
        if (is_null($item)) {
            $this->parent()->dissociate();
        } else {
            $this->parent()->associate($item);
        }

        $this->save();

        return $this;
    }

    /**
     * Adds a child.
     *
     * @param      MenuItem $item The item
     *
     * @return     <type>    ( description_of_the_return_value )
     */
    public function addChild(MenuItem $item)
    {
        $this->children()->save($item);

        return $this;
    }

    /**
     * Sets the url. (only Link type)
     *
     * @param      <type>      $url    The url
     *
     * @throws     \Exception  (description)
     *
     * @return     self        ( description_of_the_return_value )
     */
    public function setUrl($url)
    {
        $this->url = $url;

        if ($this->save()) {
            return $this;
        } else {
            throw new \Exception('Unable to set url on MenuItem');
        }
    }

    /**
     * Makes a MenuItem unclickable
     *
     * clickable defaults to true, so we revert it with this method
     *
     * @param      boolean $clickable The clickable
     */
    public function unclickable($clickable = false)
    {
        $this->clickable = $clickable;

        if ($this->save()) {
            return $this;
        } else {
            throw new \Exception('Unable to set clickable on MenuItem');
        }
    }

    /**
     * new tab
     *
     * @param      boolean $new_tab The new tab
     *
     * @throws     \Exception  (description)
     *
     * @return     self        ( description_of_the_return_value )
     */
    public function newtab($new_tab = true)
    {
        $this->new_tab = $new_tab;

        if ($this->save()) {
            return $this;
        } else {
            throw new \Exception('Unable to set new_tab on MenuItem');
        }
    }

    /**
     * Icon
     *
     * @param      <type>  $name   The font-awesome name
     */
    public function icon($name)
    {
        $this->icon = $name;

        if ($this->save()) {
            return $this;
        } else {
            throw new \Exception('Unable to set icon');
        }
    }

}
