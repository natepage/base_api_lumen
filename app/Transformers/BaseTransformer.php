<?php

namespace App\Transformers;

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
}
