<?php

namespace P3in\Observers;

use Illuminate\Database\Eloquent\Model;
use P3in\Models\WebProperty;

class IsWebPropertyObserver
{
    public function saving(Model $model)
    {
        $keys = ['scheme', 'host', 'name'];
        $webPropertyAttributes = array_only($model->getAttributes(), $keys);

        if ($webPropertyAttributes) {
            $webProperty = WebProperty::firstOrCreate(array_only($model->getAttributes(), $keys));

            $keys[] = 'url';

            $model->setRawAttributes(array_except($model->getAttributes(), $keys));

            $model->web_property()->associate($webProperty);
        }
    }

    // @TODO: add clean up for deletions and restores.
    public function deleting(Model $model)
    {
    }

    public function restoring(Model $model)
    {
    }
}
