<?php

namespace P3in\Controllers;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;
use P3in\Policies\ResourcesPolicy;
use P3in\Requests\FormRequest;
use Gate;

abstract class AbstractChildController extends Controller
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

    public function index(FormRequest $request, Model $parent)
    {
        $this->repo->setParent($parent);

        $this->checkPolicy();

        Gate::authorize('index', $this->repo);

        return $this->repo->get();
    }

    public function create(FormRequest $request, Model $parent)
    {
        return $this->repo
            ->setParent($parent)
            ->create();
    }

    public function store(FormRequest $request, Model $parent)
    {
        // $this->repo->setParent($parent);

        return $this->repo
            ->setParent($parent)
            ->store($request);
    }

    public function show(FormRequest $request, Model $parent, Model $model)
    {
        // $this->repo->setParent($parent);

        // $this->repo->setModel($model);

        return $this->repo
            ->setParent($parent)
            ->setModel($model)
            ->findByPrimaryKey($model->id);
    }

    public function update(FormRequest $request, Model $parent, Model $model)
    {
        // $this->repo->setParent($parent);

        // $this->repo->setModel($model);

        $this->repo
            ->setParent($parent)
            ->fromModel($model)
            ->update($request->except(['pivot']));

        return ['message' => 'Model updated.'];
    }

    public function destroy(FormRequest $request, Model $parent, Model $model)
    {
        // $this->repo->setParent($parent);

        // $this->repo->setModel($model);

        $success = $this->repo
            ->setParent($parent)
            ->fromModel($model)
            ->destroy();

        if ($success) {
            return ['message' => 'Model deleted.'];
        }
    }
}
