<?php

namespace P3in\Controllers;

use Gate;
use Illuminate\Database\Eloquent\Model;
use P3in\Controllers\BaseController;
use P3in\Models\Form;
use P3in\Policies\ResourcesPolicy;
use P3in\Requests\FormRequest;
use Route;

abstract class AbstractController extends BaseController
{

    protected $repo;

    /**
     * Resolves a policy for the repo or defaults to ResourcesPolicy
     */
    private function checkPolicy()
    {
        if (!Gate::getPolicyFor($this->repo)) {

            Gate::policy(get_class($this->repo), ResourcesPolicy::class);

        }

        return;
    }

    // @TODO generalize and refactor (don't make it crazy, it just looks like there's too much repetition)
    // IDEA __call()  $method in_array [crud methods] ? set parent/child using ...args -> gate authorize(method checkPolicy()) -> call specialised method

    public function index(FormRequest $request)
    {
        $this->checkPolicy();

        Gate::authorize('index', $this->repo);

        return $this->repo->get();
    }

    public function show(FormRequest $request, Model $model)
    {
        $this->repo->setModel($model);

        $this->checkPolicy();

        Gate::authorize('show', $this->repo);

        return $this->repo->findByPrimaryKey($model->id);
    }

    public function update(FormRequest $request, Model $model)
    {
        $this->repo->setModel($model);

        $this->checkPolicy();

        Gate::authorize('update', $this->repo);

        $model->update($request->all());

        return response()->json(['message' => 'Model updated.']);
    }

    public function create(FormRequest $request)
    {
        return;
    }

    public function destroy(Model $model)
    {
        $this->repo->setModel($model);

        $this->checkPolicy();

        Gate::authorize('destroy', $this->repo);

        if ($model->delete()) {

            return response()->json(['message' => 'Model deleted.']);

        } else {

            throw new \Exception('Cannot delete model.');

        }
    }

    public function store(FormRequest $request)
    {
        $model = $this->repo->create($request);

        if ($model) {

            return response()->json([
                'message' => 'Model Created.',
                'id' => $model['id'],
                // 'model' => $success['model']
                'model' => $model
            ]);

        } else {

            return response()->json([
                'message' => 'Error'
            ], 503);

        }

    }

}
