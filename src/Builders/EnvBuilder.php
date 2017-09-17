<?php

namespace P3in\Builders;

use Exception;

class EnvBuilder
{
    public function __construct()
    {
        $this->file = app()->environmentFilePath();
    }

    public function env()
    {
        $contents = file_get_contents($this->file);
        $lines = preg_split('/\n+/', $contents);
        $result = [];

        foreach ($lines as $line) {
            if (preg_match('/^(#\s)/', $line) === 1) {
                continue;
            }
            $entry = explode("=", $line, 2);
            $result[$entry[0]] = isset($entry[1]) ? $entry[1] : null;
        }

        $filtered = array_filter($result, function ($key) {
            return !empty($key);
        }, ARRAY_FILTER_USE_KEY);

        return collect($filtered);
    }

    public function create(array $data = [])
    {
        $this->validateAssociative($data);

        // @NOTE: existing values will NOT be overwritten in the event of key collision.
        $newEnv = $this->env()->union($data);

        return $this->write($newEnv->all());
    }

    public function read(string $key)
    {
        $env = $this->env();

        return $env->get($key);
    }

    public function update(array $data = [])
    {
        $this->validateAssociative($data);

        $env = $this->env()->toArray();

        foreach ($data as $key => $val) {
            foreach (array_keys($env) as $k) {
                if ($key === $k) {
                    $env[$k] = $val;
                }
            }
        }

        return $this->write($env);
    }

    public function delete(array $data = [])
    {
        $this->validateIndexed($data);

        $env = $this->env()->except($data);

        return $this->write($env->all());
    }

    public function updateOrCreate(array $data = [])
    {
        $this->validateAssociative($data);

        if ($matches = $this->checkKeysExists($data)) {
            $update = array_only($data, array_keys($matches));
            $this->update($update);
        }

        $this->create($data);
    }

    public function write(array $data = [])
    {
        $this->validateAssociative($data);

        $newLines = [];
        foreach ($data as $key => $value) {
            $newLines[] = $key . "=" . $this->cleanValue($value);
        }

        $lines = implode("\n", $newLines);

        // a backup is probably a good idea heh.
        if ($backup = $this->backup()) {
            file_put_contents($this->file, $lines);
        }

        return $backup;
    }

    public function backup()
    {
        $backupFile = $this->file . '-backup-' . date("Y_m_d_His");
        copy($this->file, $backupFile);

        return $backupFile;
    }

    // Data Validation stuff.
    private function checkKeysExists(array $keys = [])
    {
        return array_intersect_key($this->env()->toArray(), $keys);
    }

    private function checkForSpaces($value)
    {
        return preg_match('/\s/', $value) > 0;
    }

    private function checkForQuotes($value, $type = '"')
    {
        return strpos($value, $type) === 0 && strpos($value, $type, -0) === 0;
    }

    private function cleanValue($value)
    {
        if ($this->checkForSpaces($value)) {
            if ($this->checkForQuotes($value)) {
                return $value;
            }

            if ($this->checkForQuotes($value, "'")) {
                $value = trim($value, "'");
            }

            return '"' . $value . '"';
        }

        return $value;
    }

    private function validateIndexed(array $array)
    {
        if ($this->isAssociative($array)) {
            throw new Exception("Array must be an indexed array");
        }
    }

    private function validateAssociative(array $array)
    {
        if (!$this->isAssociative($array)) {
            throw new Exception("Array must be an associative array");
        }
    }

    private function emptyCheck(array $array)
    {
        if (empty($array)) {
            throw new Exception("Array cannot be empty");
        }
    }

    private function isAssociative(array $array)
    {
        $this->emptyCheck($array);

        return array_keys($array) !== range(0, count($array) - 1);
    }
}
