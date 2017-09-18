<?php

namespace App\Repositories;

use App\Exceptions\BaseErrorException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ModelRepositoryInterface
{
    /**
     * Set model object.
     *
     * @param Model $model The model object to manage
     *
     * @return self
     */
    public function setModel(Model $model);

    /**
     * Get model object.
     *
     * @return Model
     */
    public function getModel();

    /**
     * Get all models managed by the repository.
     *
     * @return Collection
     *
     * @throws BaseErrorException If items not found
     */
    public function all();

    /**
     * Get a paginated list of models managed by the repository.
     *
     * @param int $limit The number of models per page
     *
     * @return LengthAwarePaginator
     *
     * @throws BaseErrorException If items not found
     */
    public function paginate(int $limit);

    /**
     * Get one model by its id.
     *
     * @param int $id The id of model
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     */
    public function getOneById(int $id);

    /**
     * Get one model by one attribute.
     *
     * @param string $attribute The name of attribute
     * @param mixed  $value     The value of attribute
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     */
    public function getOneByAttribute(string $attribute, $value);

    /**
     * Get one model by multiple attributes.
     *
     * @param array $attributes The list of attributes as name => value
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     */
    public function getOneByAttributes(array $attributes);

    /**
     * Get multiple models by one attribute.
     *
     * @param string $attribute The name of attribute
     * @param mixed  $value     The value of attribute
     *
     * @return Collection
     *
     * @throws BaseErrorException If items not found
     */
    public function getByAttribute(string $attribute, $value);

    /**
     * Get multiple models by multiple attributes.
     *
     * @param array $attributes The list of attributes as name => value
     *
     * @return Collection
     *
     * @throws BaseErrorException If items not found
     */
    public function getByAttributes(array $attributes);

    /**
     * Store a new model.
     *
     * @param array $inputs The values of the new model
     *
     * @return Model
     *
     * @throws BaseErrorException If model not stored
     */
    public function store(array $inputs);

    /**
     * Update an existing model.
     *
     * @param int   $id     The id of model
     * @param array $inputs The new values of the model
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     *                            If model not updated
     */
    public function update(int $id, array $inputs);

    /**
     * Update an existing model by its primary key.
     *
     * @param string     $primaryKey The name of the primary key attribute
     * @param string|int $value      The value of the primary key
     * @param array      $inputs     The new values of the model
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     *                            If model not updated
     */
    public function updateByPrimaryKey(string $primaryKey, $value, array $inputs);

    /**
     * Delete an existing model.
     *
     * @param int $id The id of model
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     *                            If model not deleted
     */
    public function delete(int $id);

    /**
     * Delete an existing model by its primary key.
     *
     * @param string     $primaryKey The name of the primary key attribute
     * @param int|string $value      The value of the primary key
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     *                            If model not deleted
     */
    public function deleteByPrimaryKey(string $primaryKey, $value);
}
