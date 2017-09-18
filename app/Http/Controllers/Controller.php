<?php

namespace App\Http\Controllers;

use App\Exceptions\BaseException;
use App\Managers\ModelManagerInterface;
use App\Managers\ResponseManagerInterface;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    /** @var string */
    protected $model;

    /** @var ModelManagerInterface */
    protected $modelManager;

    /** @var ResponseManagerInterface */
    protected $responseManager;

    public function __construct(ModelManagerInterface $modelManager, ResponseManagerInterface $responseManager)
    {
        if (null === $this->model) {
            throw new BaseException(sprintf('Controller %s does not define a model as property', __CLASS__));
        }

        $modelClass = $this->model;

        $this->modelManager = $modelManager->setModel(new $modelClass());
        $this->responseManager = $responseManager->setModelManager($modelManager);
    }

    /**
     * Returns response for one item.
     *
     * @param $headers
     * @param $options
     *
     * @return Response
     */
    protected function item(array $headers = [], int $options = 0)
    {
        return $this->responseManager->item($headers, $options);
    }

    /**
     * Returns response for a collection of items.
     *
     * @param $headers
     * @param $options
     *
     * @return Response
     */
    protected function collection(array $headers = [], int $options = 0)
    {
        return $this->responseManager->collection($headers, $options);
    }

    /**
     * Returns response for a paginated collection of items.
     *
     * @param array $headers
     * @param int $options
     *
     * @return Response
     */
    protected function paginate(array $headers = [], int $options = 0)
    {
        return $this->responseManager->paginate($headers, $options);
    }

    /**
     * Handle includes and excludes parsing for the given request.
     *
     * @param Request $request
     *
     * @return void
     */
    protected function handleIncludesAndExcludes(Request $request)
    {
        if (null !== $includes = $request->get(ResponseManagerInterface::INCLUDES_ATTRIBUTE)) {
            $this->responseManager->parseIncludes($includes);
        }
        if (null !== $excludes = $request->get(ResponseManagerInterface::EXCLUDES_ATTRIBUTE)) {
            $this->responseManager->parseExcludes($excludes);
        }
    }

    public function index(Request $request)
    {
        $this->modelManager->paginate($request->get(ModelManagerInterface::PROPERTY_MODEL_LIMIT));
        $this->handleIncludesAndExcludes($request);

        return $this->paginate();
    }

    public function show(Request $request, $primaryKey)
    {
        $this->modelManager->show($primaryKey);
        $this->handleIncludesAndExcludes($request);

        return $this->item();
    }

    public function store(Request $request)
    {
        $this->modelManager->validate($inputs = $request->all(), 'store');
        $this->modelManager->store($inputs);

        return $this->item();
    }

    public function update(Request $request, $primaryKey)
    {
        $this->modelManager->validate($inputs = $request->all(), 'update');
        $this->modelManager->update($primaryKey, $inputs);

        return $this->item();
    }

    public function destroy($primaryKey)
    {
        $this->modelManager->delete($primaryKey);

        return $this->item();
    }
}
