<?php

namespace App\Managers;

use App\Exceptions\BaseErrorException;
use App\Managers\Exceptions\ModelManagerException;
use App\Repositories\BaseRepository;
use App\Repositories\ModelRepositoryInterface;
use App\Transformers\BaseTransformer;
use App\Transformers\ModelTransformerInterface;
use Doctrine\Common\Inflector\Inflector;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class BaseModelManager implements ModelManagerInterface
{
    /** @var array */
    protected $defaultObjects = [
        self::PROPERTY_REPOSITORY => BaseRepository::class,
        self::PROPERTY_TRANSFORMER => BaseTransformer::class
    ];

    /** @var Model */
    protected $model;

    /** @var null|Model|Collection */
    protected $current;

    /** @var ModelRepositoryInterface */
    protected $repository;

    /** @var ModelTransformerInterface */
    protected $transformer;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Handle dynamic method calls on the manager using the repository.
     *
     * @param string $method    The method called
     * @param array  $arguments The array of arguments
     *
     * @return Model|Collection|null
     */
    public function __call($method, $arguments)
    {
        $this->current = $this->getRepository()->$method(...$arguments);

        return $this->current;
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
     * Get current model instance.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Sets current model instance.
     *
     * @param null|Model|Collection $current
     *
     * @return self
     */
    public function setCurrent($current = null)
    {
        $this->current = $current;

        return $this;
    }

    /**
     * Get current model instance.
     *
     * @return null|Model|Collection|Paginator
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Sets repository.
     *
     * @param ModelRepositoryInterface $repository
     * @param bool $setModel Define if the model is set based on the repository
     *
     * @return self
     */
    public function setRepository(ModelRepositoryInterface $repository, bool $setModel = false)
    {
        $this->repository = $repository;

        if ($setModel) {
            $this->model = $repository->getModel();
        }

        return $this;
    }

    /**
     * Get model repository.
     *
     * @return ModelRepositoryInterface
     *
     * @throws ModelManagerException If model defines the property but the class does not exist
     */
    public function getRepository()
    {
        if (null !== $this->repository) {
            return $this->repository;
        }

        $class = $this->getClassFromModel(self::PROPERTY_REPOSITORY);

        $this->repository = new $class($this->model);

        return $this->repository;
    }

    /**
     * Sets transformer.
     *
     * @param ModelTransformerInterface $transformer
     *
     * @return self
     */
    public function setTransformer(ModelTransformerInterface $transformer)
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * Get model transformer.
     *
     * @return ModelTransformerInterface
     *
     * @throws ModelManagerException If model defines the property but the class does not exist
     */
    public function getTransformer()
    {
        if (null !== $this->transformer) {
            return $this->transformer;
        }

        $class = $this->getClassFromModel(self::PROPERTY_TRANSFORMER);

        $this->transformer = new $class();

        return $this->transformer;
    }

    /**
     * Get current model key.
     * Used to generate response sent to the user.
     *
     * @return string
     */
    public function getModelKey()
    {
        if (property_exists($this->model, $key = self::PROPERTY_MODEL_KEY)) {
            return $this->model->$key;
        }

        return $this->determineModelKey();
    }

    /**
     * Get current model primary key.
     * Used to retrieve model in database.
     *
     * @return string
     *
     * @throws ModelManagerException If model defines a primary key which does not exist in its attributes
     */
    public function getModelPrimaryKey()
    {
        if (!property_exists($this->model, $primaryKey = self::PROPERTY_MODEL_PRIMARY_KEY)) {
            return self::MODEL_DEFAULT_PRIMARY_KEY;
        }

        if (!in_array($primaryKey, $this->model->getAttributes())) {
            throw new ModelManagerException(sprintf(
                'Model %s defines a primary key as %s = %s, but this key is not defined in model attributes.',
                get_class($this->model),
                $primaryKey,
                $this->model->$primaryKey
            ));
        }

        return $this->model->$primaryKey;
    }

    /**
     * Get current model limit.
     * Used to retrieve a paginated list of models in database.
     *
     * @return int
     */
    public function getModelLimit()
    {
        if (!property_exists($this->model, $limit = self::PROPERTY_MODEL_LIMIT)) {
            return self::MODEL_DEFAULT_LIMIT;
        }

        return (int) $this->model->$limit;
    }

    /**
     * Get class name from model object if it defines it. Otherwise returns the default one.
     *
     * @param string $object The property name to check
     *
     * @return string|null
     *
     * @throws ModelManagerException If model defines the property but the class does not exist
     */
    protected function getClassFromModel(string $object)
    {
        if (property_exists($this->model, $object)) {
            if (!class_exists($this->model->$object)) {
                throw new ModelManagerException(sprintf(
                    'Model %s defines property %s as %s but class does not exist.',
                    get_class($this->model),
                    $object,
                    $this->model->$object
                ));
            }

            return $this->model->$object;
        }

        return $this->defaultObjects[$object];
    }

    /**
     * Returns model key based on model class name.
     *
     * @return string
     */
    protected function determineModelKey()
    {
        $class = strtolower((new \ReflectionClass($this->model))->getShortName());

        return Inflector::pluralize($class);
    }

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
    public function validate(array $inputs, string $set)
    {
        $setEmpty = false;

        if (!property_exists($this->model, $property = self::PROPERTY_MODEL_RULES)) {
            throw new ModelManagerException(sprintf(
                'Model %s does not define validation rules.',
                get_class($this->model)
            ));
        }

        if (!is_array($rules = $this->model->$property)) {
            throw new ModelManagerException(sprintf(
                'Model %s defines validation rules but not as an array, %s given.',
                get_class($this->model),
                gettype($rules)
            ));
        }

        if ($setEmpty = empty($rules[$set]) && empty($rules[self::MODEL_DEFAULT_RULES_SET])) {
            throw new ModelManagerException(sprintf(
                'Rules set %s does not exist and no %s rules set is defined.',
                $set,
                self::MODEL_DEFAULT_RULES_SET
            ));
        }

        $set = !$setEmpty ? $set : self::MODEL_DEFAULT_RULES_SET;
        $validator = Validator::make($inputs, $rules[$set]);

        if ($validator->fails()) {
            throw (new BaseErrorException())
                ->setStatus('400')
                ->setInternalCode('10001')
                ->setTitle('Data validation')
                ->setDetails(implode(', ', $validator->errors()->all()))
            ;
        }
    }

    /**
     * Get a paginated list of models managed by the repository.
     *
     * @param int|null $limit The number of models per page
     *
     * @return Paginator
     */
    public function paginate(int $limit = null)
    {
        if (null === $limit) {
            $limit = $this->getModelLimit();
        }

        $this->current = $this->getRepository()->paginate($limit);

        return $this->current;
    }

    /**
     * Get a single model by its primary key.
     *
     * @param string|int $primaryKeyValue The value of the primary key
     *
     * @return Model
     *
     * @throws BaseErrorException If model not found
     */
    public function show($primaryKeyValue)
    {
        $this->current = $this->getRepository()->getOneByAttribute(
            $this->getModelPrimaryKey(),
            $primaryKeyValue
        );

        return $this->current;
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
        $this->current = $this->getRepository()->store($inputs);

        return $this->current;
    }

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
    public function update($primaryKeyValue, array $inputs)
    {
        $this->current = $this->getRepository()->updateByPrimaryKey(
            $this->getModelPrimaryKey(),
            $primaryKeyValue,
            $inputs
        );

        return $this->current;
    }

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
    public function delete($primaryKeyValue)
    {
        $this->current = $this->getRepository()->deleteByPrimaryKey(
            $this->getModelPrimaryKey(),
            $primaryKeyValue
        );

        return $this->current;
    }
}
