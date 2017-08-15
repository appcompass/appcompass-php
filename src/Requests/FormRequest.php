<?php

namespace P3in\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Http\Request;
use P3in\Models\Form;
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

        $form = Form::byResource(Route::current()->getName())->first();

        if ($form) {
            return $form->rules();
        } else {

            // @TODO we hit a route that has a path parameter (not final)
            if ($this->route('path')) {
                $form_name = $this->route('path') . '.store';

                $form = Form::byResource($form_name)->first();

                if ($form) {
                    return $form->rules();
                }
            }

            return [];
        }
    }
}
