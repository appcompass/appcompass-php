<?php

namespace P3in\Controllers;

use Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use P3in\Models\Resource;
use P3in\Policies\ResourcesPolicy;
use P3in\Requests\FormRequest;
use P3in\Traits\UsesRoute;

class BaseResourceController extends BaseController
{
    use UsesRoute;

    protected $repo;
    protected $param_name;

    protected $per_page = 20;
    protected $columns = ['*'];

    protected $view_types = ['Table'];
    protected $create_type = 'Page';
    protected $update_type = 'Page';

    // /**
    //  * Resolves a policy for the repo or defaults to ResourcesPolicy
    //  */
    // private function checkPolicy()
    // {
    //     if (!Gate::getPolicyFor($this->repo)) {
    //         Gate::policy(get_class($this->repo), ResourcesPolicy::class);
    //     }
    //
    //     return;
    // }

    public function index()
    {
        // $this->repo->all();
        $data = $this->repo->paginate($this->per_page, $this->columns);
        foreach ($data as $record) {
            $record['abilities'] = ['edit', 'view', 'create', 'destroy'];
        }

        return $this->formatOutput($data);
        // $this->checkPolicy();
        //
        // Gate::authorize('index', $this->repo);
        //
        // return $this->repo->get();
    }

    public function create()
    {

        // send form structure.

        // $this->checkPolicy();
        //
        // Gate::authorize('create', $this->repo);
        //
        // return $this->repo->create();
    }

    public function show()
    {
        $id = $this->getRouteParam($this->param_name);

        $data = $this->repo->find($id, $this->columns);
        return $this->formatOutput($data);

        // $this->repo->setModel($model);
        //
        // $this->checkPolicy();
        //
        // Gate::authorize('show', $this->repo);
        //
        // return $this->repo->findByPrimaryKey($model->id);
    }

    public function edit()
    {
        return $this->show();
    }

    public function update(Request $request)
    {
        $data = $request->validate($this->rules());
        $id = $this->getRouteParam($this->param_name);

        return $this->repo->update($data, $id);
        // $this->checkPolicy();

//         Gate::authorize('update', $this->repo);
//
//         $this->repo->update($request->validated());
//
// //        $model->update($request->validated());
//
//         return response()->json(['message' => 'Model updated.']);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        return $this->repo->store($data);
    }

    public function destroy()
    {
        // $this->checkPolicy();
        //
        // Gate::authorize('destroy', $this->repo);
        $id = $this->getRouteParam($this->param_name);
        return $this->repo->delete($id);
    }

    public function formatOutput($data)
    {
        return [
            'route'       => $this->route_name,
            'parameters'  => $this->route_params,
            'api_url'     => $this->getApiUrl(),
            'view_types'  => $this->view_types,
            'create_type' => $this->create_type,
            'update_type' => $this->update_type,
            // 'owned'       => $this->repo->owned,
            'abilities'   => ['create', 'edit', 'destroy', 'index', 'show'],
            // @TODO show is per-item in the collection
            'form'        => $this->getResourceForm(),
            'collection'  => $data,
        ];

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
