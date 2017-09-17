<?php


namespace P3in\Traits;

use Illuminate\Support\Collection;

trait HasApiOutput
{
    public function error($error, $code = 400)
    {
        return $this->output([
            'success' => false,
            'error'   => $error,
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
            $data = is_numeric($data) ? floatval($data) : trim($data);
        }
    }
}
