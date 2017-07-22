<?php

namespace Tests\Transformers;

use App\Models\User;
use App\Transformers\BaseTransformer;
use App\Transformers\ModelTransformerInterface;

class ModelTransformerTests extends \TestCase
{
    /** @var User */
    protected $user;

    /** @var ModelTransformerInterface */
    protected $transformer;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->make();
        $this->transformer = new BaseTransformer();
    }

    public function testReturnIsArray()
    {
        $this->assertTrue(is_array($this->transformer->transform($this->user)));
    }

    public function testArrayIsValid()
    {
        $this->assertEquals(
            $this->user->toArray(),
            $this->transformer->transform($this->user)
        );
    }
}
