<?php

namespace P3in\Controllers;

use Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use P3in\Models\Resource;
use P3in\Policies\ResourcesPolicy;
use P3in\Repositories\Criteria\FilterBySearch;
use P3in\Repositories\Criteria\FilterBySort;
use P3in\Requests\FormRequest;
use P3in\Traits\UsesRoute;

abstract class AbstractBaseResourceController extends BaseController
{

    use UsesRoute;

    protected $repo;
    protected $param_name;

    protected $rules;

    protected $selectable = [];
    protected $per_page = 20;
    protected $columns = ['*'];

    protected $view_types = ['Table'];
    protected $create_type = 'Page';
    protected $update_type = 'Page';

    abstract public function getPolicy();

    /**
     * Resolves a policy for the repo or defaults to ResourcesPolicy
     */
    private function checkPolicy()
    {
        if (!Gate::getPolicyFor($this->repo)) {
            Gate::policy(get_class($this->repo), $this->getPolicy());
        }

        return;
    }

    public function callAction($method, $params)
    {
        if (method_exists($this, $method)){
            $this->setRouteInfo();

            $this->checkPolicy();

            Gate::authorize($method, $this->repo);

            return call_user_func_array(array($this,$method), $params);
        }
        return parent::callAction($method, $params);
    }

    public function index()
    {
        $this->repo->pushCriteria(new FilterBySearch());
        $this->repo->pushCriteria(new FilterBySort());

        $result = $this->repo->paginate($this->per_page, $this->columns);

        // @TODO: refactor to make properly functional.
        foreach ($result as $record) {
            $record['abilities'] = ['edit', 'view', 'create', 'destroy'];
        }

        $array = $result->toArray();

        $data = [
            'pagination' => array_except($array, ['data']),
            'data'       => $array['data'],
        ];

        return $this->output($data);
    }

    public function create()
    {
        return $this->output([]);
    }

    public function show()
    {
        $id = $this->getRouteParam($this->param_name);

        $data = ['data' => $this->repo->find($id, $this->columns)];

        return $this->output($data);
    }

    public function edit()
    {
        return $this->show();
    }

    public function update(Request $request)
    {
        // $data = $request->validate($this->rules());
        $data = $request->all();

        $id = $this->getRouteParam($this->param_name);

        $result = ['data' => $this->repo->updateRich($data, $id)];

        return $this->output($result);
    }

    public function store(Request $request)
    {
        // $data = $request->validate($this->rules());
        $data = $request->all();
        $result =  $this->repo->create($data);

        return $this->output($result);
    }

    public function destroy()
    {
        $id = $this->getRouteParam($this->param_name);

        $result = $this->repo->delete($id);

        return $this->output($result);
    }

    public function output($data, $code = 200)
    {
        $this->cleanupFormat($data);

        $structured = array_merge([
            'route'       => $this->route_name,
            'parameters'  => $this->route_params,
            'api_url'     => $this->getApiUrl(),
            'view_types'  => $this->view_types,
            'create_type' => $this->create_type,
            'update_type' => $this->update_type,
            'abilities'   => ['create', 'edit', 'destroy', 'index', 'show'],
            // @TODO show is per-item in the collection
            'form'        => $this->getResourceForm(),
            'selectable'       => $this->selectable,
        ], (array) $data);

        return response()->json($structured, $code);
    }

    public function getResourceForm()
    {
        $resource = Resource::byAllowed()
            ->where('name', $this->route_name)
            ->with('form')
            ->first()
        ;

        if (!empty($resource->form)) {
            return $resource->renderForm($this->getRouteType());
        }
    }
}
