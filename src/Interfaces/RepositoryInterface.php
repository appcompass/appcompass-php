<?php

namespace AppCompass\AppCompass\Interfaces;


interface RepositoryInterface
{

    public function all($columns = ['*']);

    public function paginate($perPage = 15, $columns = ['*']);

    public function create(array $data);

    public function saveModel(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function find($id, $columns = ['*']);

    public function findBy($field, $value, $columns = ['*']);

    public function findAllBy($field, $value, $columns = array('*'));

    public function findWhere($where, $columns = array('*'));
}