<?php

namespace App\Managers;

use App\Exceptions\BaseErrorException;
use App\Managers\Exceptions\ModelManagerException;
use App\Repositories\ModelRepositoryInterface;
use App\Transformers\ModelTransformerInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

interface ModelManagerInterface
{
    const PROPERTY_REPOSITORY = 'repository';
    const PROPERTY_TRANSFORMER = 'transformer';
    const PROPERTY_MODEL_KEY = 'key';
    const PROPERTY_MODEL_PRIMARY_KEY = 'primary_key';
    const PROPERTY_MODEL_RULES = 'rules';
    const PROPERTY_MODEL_LIMIT = 'limit';

    const MODEL_DEFAULT_RULES_SET = 'default';
    const MODEL_DEFAULT_PRIMARY_KEY = 'id';
    const MODEL_DEFAULT_LIMIT = 15;

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
     * Sets current model instance.
     *
     * @param null|Model|Collection|Paginator $current
     *
     * @return self
     */
    public function setCurrent($current = null);

    /**
     * Get current model instance.
     *
     * @return null|Model|Collection|Paginator
     */
    public function getCurrent();

    /**
     * Sets repository.
     *
     * @param ModelRepositoryInterface $repository
     * @param bool $setModel Define if the model is set based on the repository
     *
     * @return self
     */
    public function setRepository(ModelRepositoryInterface $repository, bool $setModel = false);

    /**
     * Get model repository.
     *
     * @return ModelRepositoryInterface
     *
     * @throws ModelManagerException If model defines the property but the class does not exist
     */
    public function getRepository();

    /**
     * Sets transformer.
     *
     * @param ModelTransformerInterface $transformer
     *
     * @return self
     */
    public function setTransformer(ModelTransformerInterface $transformer);

    /**
     * Get model transformer.
     *
     * @return ModelTransformerInterface
     *
     * @throws ModelManagerException If model defines the property but the class does not exist
     */
    public function getTransformer();

    /**
     * Get current model key.
     * Used to generate response sent to the user.
     *
     * @return string
     */
    public function getModelKey();

    /**
     * Get current model primary key.
     * Used to retrieve model in database.
     *
     * @return string
     *
     * @throws ModelManagerException If model defines a primary key which does not exist in its attributes
     */
    public function getModelPrimaryKey();

    /**
     * Get current model limit.
     * Used to retrieve a paginated list of models in database.
     *
     * @return int
     */
    public function getModelLimit();

    /**
     * Validate inputs for a given set of rules.
     *
     * @param array  $inputs The array of inputs to validate
     * @param string $set  The name of the rules set to use
     *
     * @return void
     *
     * @throws ModelManagerException If model does not define validation rules
     *                               If model defines validation rules but not as an array
     *                               If model does not define validation rules set or as empty
     * @throws BaseErrorException If validation fails
     */
    public function validate(array $inputs, string $set);

    /**
     * Get a paginated list of models managed by the repository.
     *
     * @param int|null $limit The number of models per page
     *
     * @return Paginator
     */
    public function paginate(int $limit = null);

    /**
     * Get a single model by its primary key.
     *
     * @param string|int $primaryKeyValue The value of the primary key
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     */
    public function show($primaryKeyValue);

    /**
     * Store a new model.
     *
     * @param array $inputs The values of the new model
     *
     * @return Model
     */
    public function store(array $inputs);

    /**
     * Update a model by its primary key.
     *
     * @param string|int $primaryKeyValue The value of the primary key
     * @param array      $inputs          The new values of the model
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     */
    public function update($primaryKeyValue, array $inputs);

    /**
     * Delete a model by its primary key.
     *
     * @param string $primaryKeyValue The value of the primary key
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     *                            If model can't be deleted
     */
    public function delete($primaryKeyValue);

    /**
     * Determine if the object is supported by the manager.
     *
     * @param Model|Collection|Paginator $object The object to test
     *
     * @throws ModelManagerException If $object parameter is not an object
     *
     * @return bool
     */
    public function supports($object);
}
