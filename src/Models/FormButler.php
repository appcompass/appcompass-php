<?php

namespace AppCompass\AppCompass\Models;

use AppCompass\AppCompass\Requests\FormRequest;

// in case we move this

class FormButler
{

    /**
     * { function_description }
     *
     * @param      <type>  $form_name  The form name
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function get($form_name)
    {
        return (new static)->resolveFormFromString($form_name)->render();
    }

    /**
     * { function_description }
     *
     * @param      \AppCompass\Requests\FormRequest $request The request
     *
     * @return     <type>                      ( description_of_the_return_value )
     */
    private function parseRequest(FormRequest $request)
    {
        if ($request->has('form')) {
            return $this->resolveFormFromString($request->form)->render();
        }

        return;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $form_name  The form name
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function resolveFormFromString($form_name)
    {
        return Form::whereName($form_name)->firstOrFail();
    }

    /**
     * { function_description }
     *
     * @param      <type>  $form_name  The form name
     * @param      <type>  $content    The content
     *
     * @return     array   ( description_of_the_return_value )
     */
    public static function store($form_name, $content)
    {
        // @TODO vvvv not really: what we wanna do here is resolve the form, get the linked Model
        // and fill it up
        // avoid breaking things for now
//        if (isset($content['id'])) {
//            MenuItem::findOrFail($content['id'])->update($content);
//        } else {
        $form = Form::whereName($form_name)->firstOrFail();

        $form->store($content);

//        }

        return ['success' => ['Updated']];
    }
}
