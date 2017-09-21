<?php

namespace P3in\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use P3in\Traits\HasApiOutput;

class BaseController extends Controller
{
    use HasApiOutput;

    public function callAction($method, $params)
    {
        try {
            return parent::callAction($method, $params);
        } catch (ValidationException $e) {
            return $this->handleValidationException($e);
        }
    }

    protected function handleValidationException(ValidationException $e)
    {
        return $this->error($e->getMessage(), $e->status, $e->errors());
    }
}
