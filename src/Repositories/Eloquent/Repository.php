<?php

namespace P3in\Repositories\Eloquent;

use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use P3in\Interfaces\RepositoryInterface;
use P3in\Interfaces\CriteriaInterface;
use P3in\Repositories\Criteria\AbstractCriteria;
use P3in\Repositories\Relationships\Relationship;

abstract class Repository implements RepositoryInterface, CriteriaInterface
{
    protected $app;
    protected $model;
    protected $related;
    protected $criteria;
    protected $skipCriteria = false;
    protected $preventCriteriaOverwriting = true;

    public function __construct(App $app, Collection $collection)
    {
        $this->app = $app;
        $this->criteria = $collection;
        $this->resetScope();
        $this->setModel();
    }

    abstract public function getModel();

    public function setModel()
    {
        $model = $this->app->make($this->getModel());

        if (!$model instanceof Model) {
            // RepositoryException
            throw new \Exception("Class {$this->getModel()} must be an instance of " . Model::class);
        }

        return $this->model = $model;
    }

    public function related() : Relationship
    {
        if (!$this->related){
            $this->related = new Relationship($this);
        }

        return $this->related;
    }

    public function all($columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->get($columns);
    }

    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    public function lists($value, $key = null)
    {
        $this->applyCriteria();
        $lists = $this->model->lists($value, $key);
        if (is_array($lists)) {
            return $lists;
        }

        return $lists->all();
    }

    public function paginate($perPage = 15, $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->paginate($perPage, $columns);
    }

    public function create(array $data)
    {
        if ($this->related) {
            return $this->related()->create($data);
        }

        return $this->model->update($data);
    }

    public function saveModel(array $data)
    {
        foreach ($data as $k => $v) {
            $this->model->{$k} = $v;
        }

        return $this->model->save();
    }

    public function update(array $data, $id, $attribute = "id")
    {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    public function updateRich(array $data, $id)
    {
        if (!($model = $this->model->find($id))) {
            return false;
        }

        return $model->fill($data)->save();
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function find($id, $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->find($id, $columns);
    }

    public function findBy($attribute, $value, $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    public function findAllBy($attribute, $value, $columns = ['*'])
    {
        $this->applyCriteria();

        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    public function findWhere($where, $columns = ['*'], $or = false)
    {
        $this->applyCriteria();
        $model = $this->model;
        foreach ($where as $field => $value) {
            if ($value instanceof \Closure) {
                $model = (!$or) ? $model->where($value) : $model->orWhere($value);
            } elseif (is_array($value)) {
                if (count($value) === 3) {
                    list($field, $operator, $search) = $value;
                    $model = (!$or) ? $model->where($field, $operator, $search) : $model->orWhere($field, $operator, $search);
                } elseif (count($value) === 2) {
                    list($field, $search) = $value;
                    $model = (!$or) ? $model->where($field, '=', $search) : $model->orWhere($field, '=', $search);
                }
            } else {
                $model = (!$or) ? $model->where($field, '=', $value) : $model->orWhere($field, '=', $value);
            }
        }

        return $model->get($columns);
    }

    public function resetScope()
    {
        $this->skipCriteria(false);

        return $this;
    }

    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    public function getCriteria()
    {
        return $this->criteria;
    }

    public function getByCriteria(AbstractCriteria $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);

        return $this;
    }

    public function pushCriteria(AbstractCriteria $criteria)
    {
        if ($this->preventCriteriaOverwriting) {
            // Find existing criteria
            $key = $this->criteria->search(function ($item) use ($criteria) {
                return (is_object($item) && (get_class($item) == get_class($criteria)));
            });
            // Remove old criteria
            if (is_int($key)) {
                $this->criteria->offsetUnset($key);
            }
        }
        $this->criteria->push($criteria);

        return $this;
    }

    public function applyCriteria()
    {
        if ($this->skipCriteria === true) {
            return $this;
        }

        foreach ($this->getCriteria() as $criteria) {
            if ($criteria instanceof AbstractCriteria) {
                $this->model = $criteria->apply($this->model, $this);
            }
        }

        return $this;
    }
}
