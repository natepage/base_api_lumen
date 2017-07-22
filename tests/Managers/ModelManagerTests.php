<?php

namespace Tests\Managers;

use App\Exceptions\BaseErrorException;
use App\Managers\BaseModelManager;
use App\Managers\Exceptions\ModelManagerException;
use App\Managers\ModelManagerInterface;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\ModelRepositoryInterface;
use App\Transformers\BaseTransformer;
use App\Transformers\ModelTransformerInterface;
use Illuminate\Database\Eloquent\Model;
use Test\Models\TestUser;
use Tests\Utils\AssertEloquentModels;

class ModelManagerTests extends \TestCase
{
    use AssertEloquentModels;

    /** @var array */
    protected $user = [
        'name' => 'John Doe',
        'email' => 'john.doe@gmail.com',
        'password' => 'password'
    ];

    /** ------- UTILS ------- */

    /**
     * Get model repository instance.
     *
     * @param Model|null $model
     *
     * @return ModelManagerInterface
     */
    protected function getModelManager(Model $model = null)
    {
        if (null === $model) {
            $model = new User();
        }

        return new BaseModelManager($model);
    }

    /**
     * Get users array.
     *
     * @return array
     */
    protected function getUsersArray()
    {
        $users = [];

        for ($i = 0; $i <= 5; $i++) {
            $users[] = [
                'name' => 'User_' . $i,
                'email' => 'user_' . $i . '@gmail.com',
                'password' => 'password',
                'enabled' => in_array($i, [2, 3])
            ];
        }

        return $users;
    }

    /** ------- TESTS ------- */

    public function testStoreModel()
    {
        $user = $this->getModelManager()->store($this->user);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($this->user['email'], $user->email);
    }

    public function testUpdateModel()
    {
        $manager = $this->getModelManager();
        $user = $manager->store($this->user);
        $updated = $manager->update($user->id, ['enabled' => true]);

        $this->assertInstanceOf(User::class, $updated);
        $this->assertTrue($updated->enabled);
    }

    public function testShowModel()
    {
        $manager = $this->getModelManager();
        $user = $manager->store($this->user);
        $show = $manager->show($user->id);

        $this->assertInstanceOf(User::class, $show);
    }

    public function testDeleteModel()
    {
        $this->expectException(BaseErrorException::class);

        $manager = $this->getModelManager();
        $user = $manager->store($this->user);

        $manager->delete($user->id);
        $manager->show($user->id);
    }

    public function testGetPaginateModels()
    {
        $usersArray = $this->getUsersArray();
        $manager = $this->getModelManager();
        $users = [];

        $paginate = $manager->paginate(2);
        $this->assertEquals(0, $paginate->count());

        foreach ($usersArray as $user) {
            $users[] = $manager->store($user);
        }

        $paginate = $manager->paginate(2);
        $this->assertEquals(2, $paginate->count());
    }

    public function testGetCurrentIsNull()
    {
        $this->assertNull($this->getModelManager()->getCurrent());
    }

    public function testSetCurrentFromFunction()
    {
        $manager = $this->getModelManager()->setCurrent(factory(User::class)->make());
        $this->assertInstanceOf(User::class, $manager->getCurrent());
    }

    public function testSetCurrentFromRepository()
    {
        $manager = $this->getModelManager();
        $user = $manager->store($this->user);

        $this->assertSameModel($user, $manager->getCurrent());
    }

    public function testSetCurrentErrorHandling()
    {
        $this->expectException(ModelManagerException::class);
        $this->getModelManager()->setCurrent('wrong_type');
    }

    public function testGetDefaultRepository()
    {
        $this->assertInstanceOf(ModelRepositoryInterface::class, $this->getModelManager()->getRepository());
    }

    public function testGetRepositoryFromSet()
    {
        $manager = $this->getModelManager()->setRepository(new BaseRepository());
        $this->assertInstanceOf(BaseRepository::class, $manager->getRepository());
    }

    public function testGetDefaultTransformer()
    {
        $this->assertInstanceOf(ModelTransformerInterface::class, $this->getModelManager()->getTransformer());
    }

    public function testGetTransformerFromSet()
    {
        $manager = $this->getModelManager()->setTransformer(new BaseTransformer());
        $this->assertInstanceOf(BaseTransformer::class, $manager->getTransformer());
    }

    public function testGetDefaultModelKey()
    {
        $this->assertEquals('users', $this->getModelManager()->getModelKey());
    }

    public function testGetModelKey()
    {
        $expected = 'users_key';

        $this->assertEquals($expected, $this->getModelManager(new TestUser())->getModelKey());
    }

    public function testGetDefaultModelPrimaryKey()
    {
        $this->assertEquals(
            ModelManagerInterface::MODEL_DEFAULT_PRIMARY_KEY,
            $this->getModelManager()->getModelPrimaryKey()
        );
    }

    public function testGetModelPrimaryKey()
    {
        $expected = 'custom_primary_key';

        $this->assertEquals($expected, $this->getModelManager(new TestUser())->getModelPrimaryKey());
    }

    public function testGetDefaultModelLimit()
    {
        $this->assertEquals(
            ModelManagerInterface::MODEL_DEFAULT_LIMIT,
            $this->getModelManager()->getModelLimit()
        );
    }

    public function testGetModelLimit()
    {
        $expected = 40;

        $this->assertEquals($expected, $this->getModelManager(new TestUser())->getModelLimit());
    }
}
