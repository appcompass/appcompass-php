<?php

namespace P3in\Builders;

use Closure;
use P3in\Interfaces\WebPropertyModelInterface;
use P3in\Models\Page;
use P3in\Models\Link;
use P3in\Models\Menu;
use P3in\Models\MenuItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MenuBuilder
{

    /**
     * Menu instance
     */
    private $menu = null;

    /**
     * Current MenuItem Instance
     */
    private $menu_item = null;

    /**
     * Parent instance
     */
    private $parent = null;

    private function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     * new
     *
     * @param      Menu $menu The menu
     *
     * @return     <type>   ( description_of_the_return_value )
     */
    public static function new($name, WebPropertyModelInterface $web_property, Closure $closure = null)
    {
        $menu = new Menu(['name' => $name]);
        $web_property->menus()->save($menu);

        $instance = new static($menu);

        if ($closure) {
            $closure($instance);
        }

        return $instance;
    }

    /**
     * edits a menu
     *
     * @param      <type>       $menu  The menu being edited
     *
     * @throws     \Exception   Menu must be set
     *
     * @return     MenuBuilder  MenuBuilder instance
     */
    public static function edit($menu, Closure $closure = null, $parent = null) : MenuBuilder
    {
        if (!$menu instanceof Menu && !is_int($menu)) {
            throw new \Exception('Must pass id or menu instance');
        }

        if (is_int($menu)) {
            $menu = Menu::findOrFail($menu);
        }

        $instance = (new static($menu))->setParent($parent);

        if (!is_null($closure)) {
            $closure($this);
        }

        return $instance;
    }

    /**
     * UI update, takes whatever the UI sends back
     *
     * @param      \P3in\Models\Menu $menu The menu
     * @param      (array)            $structure  The menu structure coming from the UI
     */
    public static function update(Menu $menu, array $structure)
    {
        $instance = static::edit($menu);

        $instance->menu->drop($structure['deletions']);

        foreach ($instance->flatten($structure['menu']) as $menuitem) {
            if (is_null($menuitem->menu_id)) {
                $instance->add($menuitem);
            }
        }
    }

    /**
     * Adds an item.
     *
     * @param      <type>      $item   The item
     * @param      integer $order The order
     *
     * @throws     \Exception  (description)
     *
     * @return     <type>      ( description_of_the_return_value )
     */
    public function add($item, $order = 0)
    {
        if (!isset($this->menu)) {
            throw new \Exception('Menu not selected.');
        }

        if (is_array($item)) {
            $item = new Link($item);
            if ($web_property = $this->menu->web_property) {
                $item->web_property()->associate($web_property);
            }
            $item->save();
            // relying on default values requires a fresh() copy of the model
            $item = $item->fresh();
        }

        if ($item instanceof MenuItem) {
            $this->menu_item = $item;
        } else {
            $this->menu_item = MenuItem::fromModel($item, $order);
        }

        if ($this->hasParent()) {

            // we are pointing the last inserted menu_item aka the parent -f
            $this->menu_item->setParent($this->parent->menu_item);
        }

        if ($this->menu->add($this->menu_item)) {

            // to have both worlds (return a MenuBuilder or a MenuItem) we return $this here
            // and we use a magic __call to check if we're calling methods MenuItem has -f
            return $this;
        } else {
            throw new \Exception("Something went wrong while adding the MenuItem {$this->menu_item->id} to Menu {$this->menu->id}");
        }
    }

    /**
     * { function_description }
     *
     * @return     self  ( description_of_the_return_value )
     */
    public function parent()
    {
        if (!$this->hasParent()) {
            // @TODO wouldn't end a while cycle
            return $this;
        }

        return $this->parent;
    }

    /**
     *
     */
    public function sub()
    {
        return MenuBuilder::edit($this->menu, null, $this);
    }

    /**
     * Sets the parent.
     *
     * @param      MenuBuilder $menu_item The menu builder
     */
    public function setParent(MenuBuilder $menu_builder = null)
    {
        $this->parent = $menu_builder;

        return $this;
    }

    /**
     * Determines if it has parent.
     *
     * @return     boolean  True if has parent, False otherwise.
     */
    public function hasParent()
    {
        return !!$this->parent;
    }

    /**
     * Edit a related MenuItem
     *
     * @param      <type>      $item   The item
     *
     * @throws     \Exception  (description)
     *
     * @return     <type>      ( description_of_the_return_value )
     */
    public function item($item) : MenuBuilder
    {
        $this->menu_item = $this->findItem($item);

        return $this;
    }

    /**
     * Finds a MenuItem from current menu
     *
     * @param      <type>  $item   The item
     *
     * @return     array   ( description_of_the_return_value )
     */
    private function findItem($item) : MenuItem
    {
        try {
            if (is_int($item)) {
                return $this->menu->items()->where('id', $item)->firstOrFail();
            } elseif (is_string($item)) {
                $items = $this->menu->items()->whereTitle($item)->get();

                if (!$items || count($items) > 1) {
                    throw new \Exception('More than one instance found. Stopping.');
                }

                if (!$items->first()) {
                    throw new \Exception("Item <$item> not found in this menu <{$this->menu->name}>.");
                }

                return $items->first(); // and only
            } elseif ($item instanceof MenuItem) {
                return $this->menu->items()->where('id', $item->id)->firstOrFail();
            }
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Passed item is not part of the current Menu. Weird.');
        }
    }

    /**
     * Gets the menu.
     *
     * @return     <type>  The menu.
     */
    public function getMenu() : Menu
    {
        return $this->menu;
    }

    /**
     * Gets the current MenuItem.
     *
     * @return     MenuItem  The MenuItem.
     */
    public function getMenuItem()
    {
        return $this->menu_item;
    }

    /**
     *
     * @return     Function  ( description_of_the_return_value )
     */
    public function done()
    {
        // @TODO this should return the root instance menu -f

        return $this->menu;
    }

    /**
     * Obtains a MenuItem from an array of properties expects either `type` or
     * `navigatable_id` to be set
     *
     * @param      array $item Properties of the item
     *
     * @return     array  The menu item.
     */
    private function resolveMenuItem($item) : MenuItem
    {
        if ($item instanceof MenuItem) {
            return $item;
        }

        // item is a MenuItem (has the polymorphic ref)
        if (isset($item['navigatable_id'])) {
            $menuitem = MenuItem::findOrFail($item['id']);
        } else {

//            // doing the extra mile here to greatly simplify the frontend stuff
//            switch ($item['type']) {
//                case 'Link':
//                    $class_name = Link::class;
//                    break;
//                case 'Page':
//                    $class_name = Page::class;
//                    break;
//            }

            // otherwise generate a MenuItem instance from model being added
            $menuitem = MenuItem::fromModel($item['type']::findOrFail($item['id']), $item['order']);
        }

        $menuitem->fill($item)->save();

        return $menuitem;
    }

    /**
     * flattens a menu, converts item into MenuItem
     *
     * @param      array   $menu  The menu
     * @param      <type>   $parent_id  The parent identifier
     * @param      integer $order The order
     *
     * @return     array    ( description_of_the_return_value )
     */
    private function flatten(array $menu, $parent_id = null, $order = null)
    {
        $res = [];

        if (is_null($order)) {
            $order = 0;
        }

        foreach ($menu as $branch) {
            $branch['order'] = $order++;

            $menuitem = $this->resolveMenuItem($branch);

            $menuitem->setParent(MenuItem::find($parent_id));

            $menuitem->save();

            $children = $branch['children'];

            $res[] = $menuitem;

            if (count($children)) {
                $res = array_merge($res, $this->flatten($children, $menuitem->id, $order));
            }
        }

        return $res;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $method  The method
     * @param      <type>  $args    The arguments
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->menu_item, $method)) {

            // @NOTE ignore and return this -f
            return $this;
        }

        call_user_func_array([$this->menu_item, $method], $args);

        return $this;
    }
}
