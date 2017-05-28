<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;

interface ModelTransformerInterface
{
    /**
     * Get model's array representation.
     *
     * @param Model $model The model to transform
     *
     * @return array
     */
    public function transform(Model $model);
}
