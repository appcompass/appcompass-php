<?php

namespace P3in\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Http\Request;
use P3in\Models\Form;
use P3in\Models\Resource;
use Route;

class FormRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Fetch form rules based on resource name
     *
     * @param      \Illuminate\Http\Request $request The request
     *
     * @return     array                     ( description_of_the_return_value )
     */
    public function rules(Request $request)
    {
        if (!in_array($request->getMethod(), ['POST', 'PUT'])) {
            return [];
        }

        $resource = Resource::whereName(Route::current()->getName())->with('form')->first();

        if (isset($resource->form)) {
            return $resource->form->rules();
        } else {

            // @TODO we hit a route that has a path parameter (not final)
            if ($this->route('path')) {
                $form_name = $this->route('path') . '.store';

                $resource = Resource::whereName($form_name)->with('form')->first();

                if (isset($resource->form)) {
                    return $resource->form->rules();
                }
            }

            return [];
        }
    }
}
