<?php


namespace P3in\Traits;

use Illuminate\Support\Collection;

trait HasApiOutput
{
    protected $validation_message = 'The given data was invalid.';

    public function error($message, $code = 400, $context = null)
    {
        return $this->output([
            'success' => false,
            'message' => $message,
            'data'   => $context,
        ], $code);
    }

    public function success($data)
    {
        $this->cleanupFormat($data);

        $rtn = ['success' => true];
        $rtn['data'] = $data;

        return $this->output($rtn);
    }

    public function paginated($data)
    {
        $this->cleanupFormat($data['data']);

        $data['success'] = true;

        return $this->output($data);
    }

    public function output($data, $code = 200)
    {
        return response()->json($data, $code);
    }

    public function cleanupFormat(&$data)
    {
        if (is_array($data) || is_object($data) || $data instanceof Collection) {
            foreach ($data as &$row) {
                $this->cleanupFormat($row);
            }
        } else {
            if (is_numeric($data)) {
                if (strpos($data, '0') !== 0) {
                    $data = floatval($data);
                } else {
                    $data = trim($data);
                }
            } else {
                $data = trim($data);
            }
        }
    }
}
