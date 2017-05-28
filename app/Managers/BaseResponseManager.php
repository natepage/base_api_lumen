<?php

namespace App\Managers;

use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceAbstract;
use Symfony\Component\HttpFoundation\Response;

class BaseResponseManager implements ResponseManagerInterface
{
    const RESOURCE_ITEM = Item::class;
    const RESOURCE_COLLECTION = Collection::class;

    /** @var ModelManagerInterface */
    protected $modelManager;

    /** @var Manager */
    protected $fractalManager;

    public function __construct(Manager $fractalManager)
    {
        $this->fractalManager = $fractalManager;
    }

    /**
     * Sets model manager.
     *
     * @param ModelManagerInterface $modelManager
     *
     * @return self
     */
    public function setModelManager(ModelManagerInterface $modelManager)
    {
        $this->modelManager = $modelManager;

        return $this;
    }

    /**
     * Parse items to include in the response.
     *
     * @param string $includes The list of item names to include (separated using coma).
     *
     * @return void
     */
    public function parseIncludes(string $includes)
    {
        $this->fractalManager->parseIncludes($includes);
    }

    /**
     * Parse items to exclude in the response.
     *
     * @param string $excludes The list of item names to exclude (separated using coma).
     *
     * @return void
     */
    public function parseExcludes(string $excludes)
    {
        $this->fractalManager->parseExcludes($excludes);
    }

    /**
     * Returns response for one item.
     *
     * @param array $headers
     * @param int $options
     *
     * @return Response
     */
    public function item(array $headers = [], int $options = 0)
    {
        return $this->resourceResponse(
            $this->createResource(self::RESOURCE_ITEM),
            $headers,
            $options
        );
    }

    /**
     * Returns response for a collection of items.
     *
     * @param array $headers
     * @param int $options
     *
     * @return Response
     */
    public function collection(array $headers = [], int $options = 0)
    {
        return $this->resourceResponse(
            $this->createResource(self::RESOURCE_COLLECTION),
            $headers,
            $options
        );
    }

    /**
     * Returns response for a paginated collection of items.
     *
     * @param array $headers
     * @param int $options
     *
     * @return Response
     */
    public function paginate(array $headers = [], int $options = 0)
    {
        $resourceClass = self::RESOURCE_COLLECTION;

        $resource = new $resourceClass(
            $this->modelManager->getCurrent()->getCollection(),
            $this->modelManager->getTransformer(),
            $this->modelManager->getModelKey()
        );
        $resource->setPaginator(new IlluminatePaginatorAdapter(
            $this->modelManager->getCurrent()
        ));

        return $this->resourceResponse($resource, $headers, $options);
    }

    /**
     * Returns a response based on the given data.
     *
     * @param array $data
     * @param int   $status
     * @param array $headers
     * @param int   $options
     *
     * @return Response
     */
    public function response(array $data = null, int $status = Response::HTTP_OK, array $headers = [], int $options = 0)
    {
        return new JsonResponse($data, $status, $headers, $options);
    }

    /**
     * Create a fractal resource for the given class.
     *
     * @param string $resourceClass The name of the resource's class
     *
     * @return ResourceAbstract
     */
    protected function createResource(string $resourceClass)
    {
        return new $resourceClass(
            $this->modelManager->getCurrent(),
            $this->modelManager->getTransformer(),
            $this->modelManager->getModelKey()
        );
    }

    /**
     * Returns a response for a given resource.
     *
     * @param ResourceAbstract $resource
     * @param array            $headers
     * @param int              $options
     *
     * @return Response
     */
    protected function resourceResponse(ResourceAbstract $resource, array $headers, int $options)
    {
        $data = $this->fractalManager->createData($resource)->toArray();

        return $this->response($data, Response::HTTP_OK, $headers, $options);
    }

    /**
     * Returns a response based on the given list of errors.
     *
     * @param array $errors
     * @param int   $status
     * @param array $headers
     * @param int   $options
     *
     * @return Response
     */
    public function errors(array $errors, int $status = Response::HTTP_BAD_REQUEST, array $headers = [], int $options = 0)
    {
        return $this->response(['errors' => $errors], $status, $headers, $options);
    }
}
