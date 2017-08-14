<?php

namespace P3in\Repositories;

use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use P3in\Interfaces\AbstractRepositoryInterface;
use P3in\Models\Form;
use P3in\Models\Resource;
use P3in\Models\User;

abstract class AbstractRepository implements AbstractRepositoryInterface
{

    // @TODO PHP 7.1 allows for public/private constants. consider
    // logged user only can see owned items
    const SEE_OWNED = 0;

    // looged user sees any, edits owned
    const EDIT_OWNED = 0;

    // each model may require a permission that the user must have in order for it to be returned.
    const REQUIRES_PERMISSION = 0;

    // key on the model representing relation
    // @TODO explore dot.separation (maybe after loading relations)
    protected $owned_key = 'user_id';

    // repo locks if use doesn't have permissions
    private $locked = false;

    // always null in normal context, populated in child context.
    // @TODO: feels off setting this here, but the current alternative is more code.
    protected $owned = null;

    // Builder
    protected $builder;

    // Model
    protected $model;

    // Link parent/children
    protected $with = [];

    // appends attributes to output.
    protected $appends = [];

    // @TODO it works well and it's simple but not enough. maybe.
    // list of items that must be defined when persising the repo
    // ['user' => ['from' => 'id', 'to' => 'user_id']]
    protected $requires = [
        'methods' => [],
        'props'   => [],
    ];

    protected $route_name;
    protected $route_params;
    // // model's default list view
    // protected $view = 'Table';
    // view types: ['Table','Card','Map', 'Chart', 'etc'] and what ever other types a module in the future may need.
    // @TODO: consider moving additional ones into the models rather than repositories.
    // i.e. make use of HasCardView trait for Card layouts, and other traits for other layout types.
    protected $view_types = ['Table'];
    // create types: 'Page' - 'Add New' button that leads to new create view,
    // 'Inline' - instead of taking the user to a page, it renders the form inline
    // where normally the Add New button would be for Page types.
    protected $create_type = 'Page';
    //update types: 'Page' - normal full page behavior, 'Modal' - modal edit view, like for a photo when clicked on a grid.
    protected $update_type = 'Page';

    // in rare instances we want to exclude all the extra data on the output structure.
    public $raw_data = false;

    /**
     * { function_description }
     */
    protected function make()
    {
        if (!$this->builder) {
            $this->builder = $this->getBuilder();
        }

        // @TODO consider how to work with relations, it could happen the Model doesn't have a user_id directly (we prob should avoid that to keep it simpler)
        // SEE_OWNED has the highest priority
        if (static::SEE_OWNED) {
            if (!Auth::check()) {
                $this->locked = true;
            }

            if (!Auth::user()->isAdmin()) {
                $this->builder->where($this->owned_key, \Auth::user()->id);
            }
        }

        if (static::REQUIRES_PERMISSION) {
            // kinda hackish, but it doesn't let us just do it by ref.
            $this->builder = $this->builder->byAllowed();
        }
        $this->loadRelations();

        $this->sort();

        $this->search();

        return $this;
    }

    /**
     * Gets the builder.
     *
     * @return     <type>  The builder.
     */
    public function getBuilder()
    {
        if (!$this->builder) {
            $this->builder = $this->model->newQuery();
        }

        return $this->builder;
    }

    /**
     * Loads relations.
     */
    private function loadRelations()
    {
        foreach ($this->with as $relation) {
            $this->builder->with($relation);
        }

        return $this;
    }

    // @TODO: look into a way to apply this directly to the model so the output is handled automatically.
    private function setAppends(&$collection)
    {
        if ($this->appends) {
            $collection->each(function ($row) {
                $row->setAppends($this->appends);
            });
        }

        return $this;
    }

    /**
     *
     */
    public function fromModel(Model $model)
    {
        $this->setModel($model);

        $this->builder = $model->newQuery();

        $this->loadRelations();

        return $this;
    }

    /**
     * Sets the model.
     *
     * @param      \Illuminate\Database\Eloquent\Model $model The model
     *
     * @return     <type>                               ( description_of_the_return_value )
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Gets the model.
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Searches for the first match.
     *
     * @param      <type>  $search  The search
     */
    public function search($search = null)
    {
        if (!request()->has('search') && is_null($search)) {
            return;
        }

        if (is_null($search)) {
            // request sometimes sends empty object string '{}' instead of an
            // actual object, absolute query string values and what not.
            $search = json_decode(request()->search, true);
            if (is_string($search)) {
                $search = json_decode($search, true);
            }
        }

        foreach ((array)$search as $column => $string) {
            $string = strtolower($string);
            $string = DB::connection()->getPdo()->quote("%$string%");
            $this->builder->whereRaw("lower(cast($column as varchar)) LIKE {$string}");
        }
    }

    /**
     * { function_description }
     *
     * @param      <type>  $sorters  The sorters
     */
    public function sort($sorters = null)
    {
        if (!request()->has('sorters') && is_null($sorters)) {
            return;
        }

        if (is_null($sorters)) {
            $sorters = request()->sorters;
            if (is_string($sorters)) {
                $sorters = json_decode($sorters, true);
            }
        }

        foreach ((array)$sorters as $field => $order) {
            $this->builder->orderBy($field, $order);
        }
    }

    /**
     * { function_description }
     *
     * @param      <type>  $attributes  The attributes
     */
    public function store($request)
    {
        $request = $this->checkRequirements($request);

        // if there is a file in the request, lets store it.
        $this->checkForUploads($request);

        $this->model->fill($request);

        if ($this->model->save()) {
            return $this->model;
        } else {
            return false;
        }
    }

    /**
     * sort
     *
     * @param      <type>  $order  The order
     */
    public function reorder($order, $field = 'order')
    {
        // @TODO this depends on relation, delegate, refactor this into absChild
        $rel = $this->model->{$this->relationName};

        // get the whole list of stuff we're sorting
        $items = $rel->whereIn('id', $order);

        foreach ($order as $single) {
            $coll[] = $items->find($single);
        }

        for ($i = 0; $i < count($coll); $i++) {
            $coll[$i]->order = $i;

            $coll[$i]->save();
        }

        return $this;
    }

    /**
     * checkRequirements
     *
     * @param      <type>      $attributes  The attributes
     *
     * @throws     \Exception  (description)
     *
     * @return     <type>      ( description_of_the_return_value )
     */
    protected function checkRequirements($request)
    {
        $res = $request->all();

        foreach ($this->requires['props'] as $requirement => $field) {
            if (!property_exists($request, $requirement)) {
                throw new \Exception('Requirement not satisfied: ' . $requirement);
            }

            $res[$field['to']] = $request->{$requirement}->{$field['from']};
        }

        foreach ($this->requires['methods'] as $requirement => $field) {
            if (!method_exists($request, $requirement)) {
                throw new \Exception('Requirement not satisfied: ' . $requirement);
            }

            $res[$field['to']] = $request->{$requirement}()->{$field['from']};
        }

        return $res;
    }


    /**
     * { function_description }
     *
     * @param      <type>  $attributes  The attributes
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function update($attributes)
    {
        // $this->model->fill($attributes); // does not trigger any mutators.
        foreach ($attributes as $key => $val) {
            $this->model->{$key} = $val;
        }
        $this->checkForUploads($attributes);

        return $this->model->save();
    }

    /**
     * destroy model
     */
    public function destroy()
    {
        return $this->model->delete();
    }

    /**
     * { function_description }
     *
     * @param      integer $limit The limit
     *
     * @return     <type>   ( description_of_the_return_value )
     */
    public function limit($limit = 15)
    {
        $this->make();

        $this->builder->limit($limit);

        return $this;
    }

    /**
     *
     */
    public function findByPrimaryKey($id)
    {
        return $this->output($this->make()
            ->builder
            ->where($this->model->getTable() . '.' . $this->model->getKeyName(), $id)
            ->firstOrFail());
    }

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function get()
    {

        // repo locks if use doesn't have permissions
        if ($this->locked) {

            // return;
            return $this->output([], 401);
        }

        // for show() if a model has been set we only wanna load rels
        if ($this->model->id) {
            $data = $this->model;
        }

        if (request()->has('page')) {
            $data = $this->paginate(request()->per_page, request()->page);
        } else {
            $data = $this->paginate();
        }

        return $this->output($data);
    }

    /**
     * { function_description }
     *
     * @param      <type>   $page      The page
     * @param      integer $per_page The per page
     *
     * @return     <type>   ( description_of_the_return_value )
     */
    public function paginate($per_page = 25, $page = null)
    {
        // @TODO per_page sometimes comes through null
        if (!$per_page) {
            $per_page = 25;
        }

        // when paginating evaluate EDIT_OWNED, if true get the results and loop through them
        // attach `abilities` to the resulting data
        // abilities = [create, edit, destoy, index, show]
        $data = $this->make()->builder->paginate($per_page, ['*'], 'page', $page);

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

        return $data;
    }

    /**
     * Check for uploads
     *
     * @param      <type>      $attributes  The attributes
     *
     * @throws     \Exception  (description)
     */
    public function checkForUploads($attributes)
    {
        $file = head(array_where($attributes, function ($val) {
            return is_a($val, UploadedFile::class);
        }));

        $storage = $this->model->storage ? $this->model->storage->name : head(array_where($attributes,
            function ($val, $key) {
                return $key == 'disk';
            }));

        if ($file) {
            if (!$storage) {
                throw new \Exception('Must specify a Disk instance to store uploaded file.');
            }

            info("File '{$file->getClientOriginalName()}' submitted for storage in the '{$storage}' disk.");
            // $this->model->storeFile($storage, $file, true);
            $this->model->storeFile($storage, $file, true);
        }
    }

    public function create()
    {
        // output already takes care of binding the form for the route.
        return $this->output([]);
    }

    public function output($data, $code = 200)
    {
        $this->setRouteInfo();
        $this->setAppends($data);
        if ($this->raw_data) {
            $rtn = [
                'abilities'  => ['create', 'edit', 'destroy', 'index', 'show'],
                // @TODO show is per-item in the collection
                'form'       => $this->getResourceForm(),
                'collection' => $data,
            ];

            return response()->json($rtn, $code);
        } else {
            $rtn = [
                'route'       => $this->route_name,
                'parameters'  => $this->route_params,
                'api_url'     => $this->getApiUrl(),
                'view_types'  => $this->view_types,
                'create_type' => $this->create_type,
                'update_type' => $this->update_type,
                'owned'       => $this->owned,
                'abilities'   => ['create', 'edit', 'destroy', 'index', 'show'],
                // @TODO show is per-item in the collection
                'form'        => $this->getResourceForm(),
                'collection'  => $data,
            ];

            return response()->json($rtn, $code);
        }
    }

    public function getApiUrl()
    {
        $keys = explode('.', $this->route_name);
        $values = array_values(array_map(function ($param) {
            return $param->getKey();
        }, $this->route_params));

        $segments = [''];
        $route_type = $this->getRouteType();

        for ($i = 0; $i < count($keys); $i++) {
            if ($keys[$i] !== $route_type) {
                $segments[] = $keys[$i];
                if (isset($values[$i])) {
                    $segments[] = $values[$i];
                }
            }
        }

        return implode('/', $segments);
    }

    public function getResourceForm()
    {
        $resource = Resource::byAllowed()
            ->where('resource', $this->route_name)
            ->with('form')
            ->first();

        if (!empty($resource->form)) {
            return $resource->form->render($this->getRouteType());
        }
    }

    public function getRouteType()
    {
        return substr($this->route_name, strrpos($this->route_name, '.') + 1);
    }

    public function setRouteInfo()
    {
        $route = Route::current();
        $this->route_name = $route->getName();
        $this->route_params = $route->parameters();
    }
}
