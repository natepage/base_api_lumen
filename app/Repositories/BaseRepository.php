<?php

namespace App\Repositories;

use App\Exceptions\BaseErrorException;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BaseRepository implements ModelRepositoryInterface
{
    /** @var Model */
    protected $model;

    public function __construct(Model $model = null)
    {
        $this->model = $model;
    }

    /**
     * Set model object.
     *
     * @param Model $model The model object to manage
     *
     * @return self
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model object.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get all models managed by the repository.
     *
     * @return Collection
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Get a paginated list of models managed by the repository.
     *
     * @param int $limit The number of models per page
     *
     * @return Paginator
     */
    public function paginate(int $limit)
    {
        return $this->model->paginate($limit);
    }

    /**
     * Get one model by its id.
     *
     * @param int $id The id of model
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     */
    public function getOneById(int $id)
    {
        try {
            return $this->model->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw (new BaseErrorException())
                ->setStatus('404')
                ->setInternalCode('10002')
                ->setTitle('Item not found')
                ->setDetails(sprintf('Item with id %d does not exist.', $id))
            ;
        }
    }

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
    public function getOneByAttribute(string $attribute, $value)
    {
        return $this->getOneByAttributes([$attribute => $value]);
    }

    /**
     * Get one model by multiple attributes.
     *
     * @param array $attributes The list of attributes as name => value
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     */
    public function getOneByAttributes(array $attributes)
    {
        try {
            return $this->model->where($attributes)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw (new BaseErrorException())
                ->setStatus('404')
                ->setInternalCode('10003')
                ->setTitle('Item not found')
                ->setDetails(sprintf('Item with attributes %s does not exist.', json_encode($attributes)))
            ;
        }
    }

    /**
     * Get multiple models by one attribute.
     *
     * @param string $attribute The name of attribute
     * @param mixed  $value     The value of attribute
     *
     * @return Collection
     */
    public function getByAttribute(string $attribute, $value)
    {
        return $this->getByAttributes([$attribute => $value]);
    }

    /**
     * Get multiple models by multiple attributes.
     *
     * @param array $attributes The list of attributes as name => value
     *
     * @return Collection
     */
    public function getByAttributes(array $attributes)
    {
        return $this->model->where($attributes)->get();
    }

    /**
     * Store a new model.
     *
     * @param array $inputs The values of the new model
     *
     * @return Model
     */
    public function store(array $inputs)
    {
        return $this->model->create($inputs);
    }

    /**
     * Update an existing model.
     *
     * @param int   $id     The id of model
     * @param array $inputs The new values of the model
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     */
    public function update(int $id, array $inputs)
    {
        $updated = $this->getOneById($id);
        $updated->update($inputs);

        return $updated;
    }

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
     */
    public function updateByPrimaryKey(string $primaryKey, $value, array $inputs)
    {
        $updated = $this->getOneByAttribute($primaryKey, $value);
        $updated->update($inputs);

        return $updated;
    }

    /**
     * Delete an existing model.
     *
     * @param int $id The id of model
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     *                            If model can't be deleted
     */
    public function delete(int $id)
    {
        $deleted = $this->getOneById($id);

        try {
            $deleted->delete();
        } catch (Exception $e) {
            throw (new BaseErrorException())
                ->setStatus('500')
                ->setInternalCode('10003')
                ->setTitle('Item cannot be deleted')
                ->setDetails($e->getMessage())
            ;
        }

        return $deleted;
    }

    /**
     * Delete an existing model by its primary key.
     *
     * @param string     $primaryKey The name of the primary key attribute
     * @param string|int $value      The value of the primary key
     *
     * @return Model
     *
     * @throws Exception If model can't be deleted
     * @throws BaseErrorException If model not found
     */
    public function deleteByPrimaryKey(string $primaryKey, $value)
    {
        $deleted = $this->getOneByAttribute($primaryKey, $value);

        try {
            $deleted->delete();
        } catch (Exception $e) {
            throw (new BaseErrorException())
                ->setStatus('500')
                ->setInternalCode('10003')
                ->setTitle('Item cannot be deleted')
                ->setDetails($e->getMessage())
            ;
        }

        return $deleted;
    }
}
