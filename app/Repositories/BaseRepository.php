<?php

namespace App\Repositories;

use App\Exceptions\BaseErrorException;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
     *
     * @throws BaseErrorException If items not found
     */
    public function all()
    {
        try {
            return $this->model->all();
        } catch (Exception $e) {
            throw $this->errorException('400', '10004', 'Items not found', $e->getMessage());
        }
    }

    /**
     * Get a paginated list of models managed by the repository.
     *
     * @param int $limit The number of models per page
     *
     * @return LengthAwarePaginator
     *
     * @throws BaseErrorException If items not found
     */
    public function paginate(int $limit)
    {
        try {
            return $this->model->paginate($limit);
        } catch (Exception $e) {
            throw $this->errorException('400', '10004', 'Items not found', $e->getMessage());
        }
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
            $details = sprintf('Item with id %d does not exist.', $id);

            throw $this->errorException('404', '10002', 'Item not found', $details);
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
            $details = sprintf('Item with attributes %s does not exist.', json_encode($attributes));

            throw $this->errorException('404', '10003', 'Items not found', $details);
        }
    }

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
     *
     * @throws BaseErrorException If items not found
     */
    public function getByAttributes(array $attributes)
    {
        try {
            return $this->model->where($attributes)->get();
        } catch (ModelNotFoundException $e) {
            $details = sprintf('Items with attributes %s does not exist.', json_encode($attributes));

            throw $this->errorException('404', '10004', 'Items not found', $details);
        }
    }

    /**
     * Store a new model.
     *
     * @param array $inputs The values of the new model
     *
     * @return Model
     *
     * @throws BaseErrorException If model not stored
     */
    public function store(array $inputs)
    {
        try {
            return $this->model->create($inputs);
        } catch (Exception $e) {
            $details = sprintf('Item with inputs %s could not be stored. %s',
                json_encode($inputs),
                $e->getMessage()
            );

            throw $this->errorException('400', '10005', 'Item not stored', $details);
        }
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
     *                            If model not updated
     */
    public function update(int $id, array $inputs)
    {
        $updated = $this->getOneById($id);

        try {
            $updated->update($inputs);
        } catch (Exception $e) {
            $details = sprintf('Item[%d] with inputs %s could not be updated. %s',
                $id,
                json_encode($inputs),
                $e->getMessage()
            );

            throw $this->errorException('400', '10006', 'Item not updated', $details);
        }

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
     *                            If model not updated
     */
    public function updateByPrimaryKey(string $primaryKey, $value, array $inputs)
    {
        $updated = $this->getOneByAttribute($primaryKey, $value);

        try {
            $updated->update($inputs);
        } catch (Exception $e) {
            $details = sprintf('Item[%s=%s] with inputs %s could not be updated. %s',
                $primaryKey,
                $value,
                json_encode($inputs),
                $e->getMessage()
            );

            throw $this->errorException('400', '10006', 'Item not updated', $details);
        }

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
     *                            If model not deleted
     */
    public function delete(int $id)
    {
        $deleted = $this->getOneById($id);

        try {
            $deleted->delete();
        } catch (Exception $e) {
            throw $this->errorException('500', '10003', 'Item not deleted', $e->getMessage());
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
     * @throws BaseErrorException If model not found
     *                            If model not deleted
     */
    public function deleteByPrimaryKey(string $primaryKey, $value)
    {
        $deleted = $this->getOneByAttribute($primaryKey, $value);

        try {
            $deleted->delete();
        } catch (Exception $e) {
            throw $this->errorException('500', '10003', 'Item not deleted', $e->getMessage());
        }

        return $deleted;
    }

    /**
     * Create error exception.
     *
     * @param string $status
     * @param string $internalCode
     * @param string $title
     * @param string $details
     *
     * @return BaseErrorException
     */
    private function errorException(string $status, string $internalCode, string $title, string $details)
    {
        return (new BaseErrorException())
            ->setStatus($status)
            ->setInternalCode($internalCode)
            ->setTitle($title)
            ->setDetails($details)
            ;
    }
}
