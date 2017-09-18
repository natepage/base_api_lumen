<?php

namespace App\Transformers;

use App\Managers\ModelManagerInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

class BaseTransformer extends TransformerAbstract implements ModelTransformerInterface
{
    /**
     * Get model's array representation.
     *
     * @param Model $model The model to transform
     *
     * @return array
     */
    public function transform(Model $model)
    {
        return $model->toArray();
    }

    /**
     * Include one model as item.
     *
     * @param Model $model
     *
     * @return \League\Fractal\Resource\Item
     */
    protected function includeItem(Model $model)
    {
        /** @var ModelManagerInterface $modelManager */
        $modelManager = clone app(ModelManagerInterface::class);
        $modelManager->setModel($model);

        return $this->item($model, $modelManager->getTransformer(), $modelManager->getModelKey());
    }

    /**
     * Include collection of model as collection.
     *
     * @param string     $modelClass
     * @param Collection $collection
     *
     * @return \League\Fractal\Resource\Collection
     */
    protected function includeCollection(string $modelClass, Collection $collection)
    {
        /** @var ModelManagerInterface $modelManager */
        $modelManager = clone app(ModelManagerInterface::class);
        $modelManager->setModel(new $modelClass());

        return $this->collection($collection, $modelManager->getTransformer(), $modelManager->getModelKey());
    }
}
