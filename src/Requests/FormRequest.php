<?php

namespace AppCompass\AppCompass\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;
use Illuminate\Http\Request;
use AppCompass\FormBuilder\Models\Form;
use AppCompass\AppCompass\Models\Resource;
use Route;

class FormRequest extends BaseFormRequest
{
    protected $resource;

    private function getResource()
    {
        if ($this->resource) {
            return $this->resource;
        }
        $this->resource = Resource::whereName(request()->route()->getName())->with('form')->first();

        return $this->resource;
    }

    public function authorize()
    {
        $resource = $this->getResource();
        // @TODO: check if usr has access to this resource.
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
        //@TODO: we should only be calling this class when we need to validate the request, so the below check would not be needed.
        if (!in_array($request->getMethod(), ['POST', 'PUT'])) {
            return [];
        }

        $resource = $this->getResource();
        if (isset($resource->form)) {
            $rules = $resource->form->rules();
            //This is specifically for situations like a validation rule of 'unique:users,email' on update.
            // @TODO: find a better way to do this.
            $record_id = $request->get('id');

            foreach ($rules as &$rule) {
                $rule = explode('|', $rule);
                if ($record_id){
                    foreach ($rule as &$single) {
                        if (preg_match('/^unique\:(.*),(.*)/', $single, $checks)){
                            $single .=','.$record_id;
                        }
                    }
                }
            }
            return $rules;
        }
        return [];
    }
}
