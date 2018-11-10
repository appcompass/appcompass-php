<?php

namespace AppCompass\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name',
    ];


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
     * items
     *
     * @return     HasMany  [MenuItem]
     */
    public function items()
    {
        return $this->hasMany(MenuItem::class)->with('navigatable')->orderBy('order', 'ASC');
    }

    /**
     * add
     *
     * @param      \App\MenuItem $menu_item The navigation item
     *
     * @return     MenuItem instance
     */
    public function add(MenuItem $menu_item)
    {
        return $menu_item->menu()->associate($this)->save();
    }

    /**
     * Drop MenuItem
     *
     * @param      \App\MenuItem $menu_item The navigation item(s)
     */
    public function drop($item)
    {
        if (is_array($item)) {
            foreach ($item as $single) {
                $this->drop($single);
            }

            return $this;
        }

        if (is_int($item)) {
            $menu_item = $this->items()->where('id', $item)->firstOrFail();
        } elseif ($item instanceof MenuItem) {
            $menu_item = $this->items()->where('id', $item->id)->firstOrFail();
        }

        if ($menu_item->delete() && $this->clean($menu_item)) {
            return true;
        } else {
            throw new \Exception("Errors while removing MenuItem");
        }
    }

    /**
     * Recursively cleans MenuItem and children
     *
     * @param      \AppCompass\Models\MenuItem $menuitem The menuitem
     */
    private function clean(MenuItem $menuitem)
    {
        $children = MenuItem::where('parent_id', $menuitem->id)->get();

        if ($children) {
            foreach ($children as $child) {
                $this->clean($child);
            }
        }

        $menuitem->delete();

        return true;
    }

    /**
     * render
     */
    public function render($clean = false)
    {
        if ($clean) {
            $this->makeHidden(['id', 'crated_at']);

            $this->items->each(function ($item) {
                $item->makeHidden([
                    'id',
                    'menu_id',
                    'parent_id',
                    'req_perm',
                    'navigatable_id',
                    'navigatable_type',
                    'created_at',
                ]);
            });
        }

        return $this->buildTree($this->items, null);
    }

    /**
     * Builds a menu tree recursively.
     *
     * @param      array $items The items
     * @param      <type>  $parent_id  The parent identifier
     *
     * @return     array   The tree.
     */
    private function buildTree(Collection &$items, $parent_id = null, $permissions = null)
    {
        $tree = [];
        if (is_null($permissions)){
            $permissions = Permission::getAuthPerms();
        }
        foreach ($items as $key => &$node) {
            if ($node->parent_id === $parent_id) {
                $children = $this->buildTree($items, $node->id, $permissions);

                $node->children = $children ?? [];

                if (empty($node->children) && empty($node->url)) {
                    continue;
                }

                if ($node->req_perm) {
                    if (in_array($node->req_perm, $permissions)) {
                        $tree[] = $node;
                    }
                } else {
                    $tree[] = $node;
                }
                //
                // $tree[] = $node;

                unset($items[$key]);
            }
        }

        return $tree;
    }
}
