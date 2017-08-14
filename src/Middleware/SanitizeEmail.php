<?php

namespace P3in\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class SanitizeEmail extends TransformsRequest
{

    /**
     * Transform the given value.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if ($key === 'email') {
            return strtolower($value);
        }

        return $value;
    }
}
