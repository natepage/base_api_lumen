<?php

namespace Tests\Utils;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait AssertEloquentModels
{
    /**
     * Assert if two models are equal.
     *
     * @param Model $expected
     * @param Model $test
     */
    protected function assertSameModel(Model $expected, Model $test)
    {
        $this->assertEquals(get_class($expected), get_class($test));
        $this->assertEquals($expected->id, $test->id);
    }

    /**
     * Assert equals on collection.
     *
     * @param Collection $collection
     * @param array      $attributes
     */
    protected function assertMultipleEquals(Collection $collection, array $attributes)
    {
        $collection->each(function(Model $model) use ($attributes) {
            foreach ($attributes as $attribute => $expected) {
                $this->assertEquals($expected, $model->$attribute);
            }
        });
    }
}
