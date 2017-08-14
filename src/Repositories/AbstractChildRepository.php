<?php

namespace P3in\Repositories;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AbstractChildRepository extends AbstractRepository
{

    // There are situations where we need to only display the children.
    const CHILDREN_ONLY = 0;

    // child model
    protected $parent;

    // name of child relation ()
    protected $relationName = null;

    // @TODO better explanations for this
    protected $parentToChild = null;

    // depending on the relation we return an array containing the ids
    // owned by the parent
    protected $owned = null;

    /**
     * Sets the parent model.
     *
     * @NOTE       explicit model binding lets us resolve the class directly, se we don't need to fetch sheit
     *
     * @param      <type>  $parent_id  The parent id
     */
    public function setParent($parent)
    {
        // $this->parent->findOrFail($parent_id);
        $this->parent = $parent; //$this->parent->findOrFail($parent_id);

        return $this;
    }

    /**
     * Gets the parent.
     *
     * @return     <type>  The parent.
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Gets the relation.
     */
    private function getRelation()
    {
        try {
            $relation = $this->model->{$this->relationName}();
        } catch (\BadMethodCallException $e) {
            $relation = $this->parent->{$this->relationName}();
        }

        $exploded = explode('\\', get_class($relation));

        $this->relation = $exploded[count($exploded) - 1];

        // \Log::info('ChildRepository evaluates: ' . $this->relation);

        return $this->relation;
    }

    /**
     * Gets the builder.
     *
     * @throws     \Exception  (description)
     *
     * @return     <type>      The builder.
     */
    public function getBuilder()
    {
        if (!isset($this->parent)) {
            throw new \Exception('AbstractChildRepository: unable to instantiate Child builder without a parent. Please specify the parent in your Repository concretion.');
        }

        if (is_null($this->relationName)) {
            throw new \Exception('AbstractChildRepository: cannot getBuilder, relationName has not been set on the Repository.');
        }

        // @TODO this shouldn't be invoked manually, but constructor is not really an option
        $this->getRelation();

        switch ($this->relation) {
            case 'BelongsToMany':
                if (static::CHILDREN_ONLY) {
                    $this->builder = $this->parent->{$this->parentToChild}();
                } else {
                    $this->builder = $this->model;
                }
                break;

            case 'BelongsTo':
                $this->builder = $this->parent->{$this->parentToChild}();
                break;

            case 'HasMany':
                $this->builder = $this->parent->{$this->relationName}();
                break;

            default:
                $this->builder = $this->parent
                    ->where($this->parent->getKeyName(), $this->parent->id)
                    ->with($this->relationName)
                    ->first()
                    ->{$this->relationName}();
        }

        return $this->builder;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $attributes  The attributes
     *
     * @return     array   ( description_of_the_return_value )
     */
    public function store($attributes)
    {
        // @TODO a bit hacky but this allows us to attach `request`-ed models in the controllers
        if ($attributes instanceof Request) {
            $attributes = $this->checkRequirements($attributes);
        }

        $this->getRelation();

        switch ($this->relation) {

            case 'BelongsToMany':
                $this->parent->{$this->parentToChild}()->sync($attributes);
                break;

            case 'BelongsTo':
                $this->parent->{$this->parentToChild}()->save($attributes);
                break;

            case 'HasMany':
                $this->model = new $this->model($attributes);

                // if there is a file in the attributes, lets store it.
                $this->checkForUploads($attributes);

                $this->parent->{$this->relationName}()->save($this->model);

                break;

        }

        // how about returning the chaineld models/id: parent/id/child
        return ['id' => $this->model->id, 'model' => $this->model];
    }

    /**
     * returns owned instances for BelongsToMany relation types
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function populateOwned()
    {
        if ($this->relation !== 'BelongsToMany') {
            return;
        }

        $this->owned = $this->parent->{$this->parentToChild}->pluck($this->model->getKeyName());

        return $this->owned;
    }

    public function setDiskConfig()
    {
        dd($this->parent);
        // // not a fan of doing this this way, we should find a better,
        // // more automatic, way of handling the create config object.
        // $this->create_config = [
        //     'disk' =>
        // ];
    }

    /**
     * { function_description }
     *
     * @param      <type>   $page      The page
     * @param      integer $per_page The per page
     *
     * @return     <type>   ( description_of_the_return_value )
     */
    public function paginate($per_page = 50, $page = null)
    {
        $data = $this->make()
            ->builder
            ->paginate($per_page, ['*'], 'page', $page);

        $this->populateOwned();

        if (static::EDIT_OWNED && !Auth::user()->isAdmin()) {
            foreach ($data as $record) {
                if (Auth::user()->id === $record[$this->owned_key]) {
                    $record['abilities'] = ['edit', 'view', 'create', 'destroy'];
                } else {
                    $record['abilities'] = ['view', 'create'];
                }
            }
        } else {
            foreach ($data as $record) {
                $record['abilities'] = ['edit', 'view', 'create', 'destroy'];
            }
        }

        return array_except($data->toArray(), ['next_page_url', 'path', 'prev_page_url']);
    }
}
