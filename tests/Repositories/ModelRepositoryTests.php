<?php

namespace Tests\Repositories;

use App\Exceptions\BaseErrorException;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\ModelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Tests\Utils\AssertEloquentModels;

class ModelRepositoryTests extends \TestCase
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
     * @return ModelRepositoryInterface
     */
    protected function getRepository(Model $model = null)
    {
        return new BaseRepository($model);
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

    public function testSetAndGetModel()
    {
        $repository = $this->getRepository();

        $this->assertEquals(null, $repository->getModel());

        $repository->setModel(new User());

        $this->assertInstanceOf(User::class, $repository->getModel());
    }

    public function testStoreModel()
    {
        $user = $this->getRepository(new User())->store($this->user);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($this->user['email'], $user->email);
    }

    public function testUpdateModel()
    {
        $repository = $this->getRepository(new User());
        $user = $repository->store($this->user);
        $updated = $repository->update($user->id, ['enabled' => true]);

        $this->assertInstanceOf(User::class, $updated);
        $this->assertTrue($updated->enabled);
    }

    public function testDeleteModel()
    {
        $this->expectException(BaseErrorException::class);

        $repository = $this->getRepository(new User());
        $user = $repository->store($this->user);

        $repository->delete($user->id);
        $repository->getOneById($user->id);
    }

    public function testUpdateModelByPrimaryKey()
    {
        $repository = $this->getRepository(new User());
        $user = $repository->store($this->user);
        $updated = $repository->updateByPrimaryKey('email', $user->email, ['enabled' => true]);

        $this->assertInstanceOf(User::class, $updated);
        $this->assertTrue($updated->enabled);
    }

    public function testDeleteModelByPrimaryKey()
    {
        $this->expectException(BaseErrorException::class);

        $repository = $this->getRepository(new User());
        $user = $repository->store($this->user);

        $repository->deleteByPrimaryKey('email', $user->email);
        $repository->getOneById($user->id);
    }

    public function testGetOneModelFunctions()
    {
        $repository = $this->getRepository(new User());
        $user = $repository->store($this->user);

        $byId = $repository->getOneById($user->id);
        $byAttribute = $repository->getOneByAttribute('email', $user->email);
        $byAttributes = $repository->getOneByAttributes(['name' => $user->name, 'email' => $user->email]);

        $this->assertSameModel($user, $byId);
        $this->assertSameModel($user, $byAttribute);
        $this->assertSameModel($user, $byAttributes);
    }

    public function testGetMultipleModelsFunctions()
    {
        $usersArray = $this->getUsersArray();
        $repository = $this->getRepository(new User());
        $users = [];

        foreach ($usersArray as $user) {
            $users[] = $repository->store($user);
        }

        $byAttribute = $repository->getByAttribute('enabled', true);
        $byAttributes = $repository->getByAttributes(['enabled' => false, 'password' => 'password']);

        $this->assertInstanceOf(Collection::class, $byAttribute);
        $this->assertInstanceOf(Collection::class, $byAttributes);
        $this->assertEquals(2, $byAttribute->count());
        $this->assertEquals(4, $byAttributes->count());
        $this->assertMultipleEquals($byAttribute, ['enabled' => true]);
        $this->assertMultipleEquals($byAttributes, ['enabled' => false, 'password' => 'password']);
    }

    public function testGetAllModels()
    {
        $usersArray = $this->getUsersArray();
        $repository = $this->getRepository(new User());
        $users = [];

        $all = $repository->all();
        $this->assertEquals(0, $all->count());

        foreach ($usersArray as $user) {
            $users[] = $repository->store($user);
        }

        $all = $repository->all();
        $this->assertEquals(6, $all->count());
    }

    public function testGetPaginateModels()
    {
        $usersArray = $this->getUsersArray();
        $repository = $this->getRepository(new User());
        $users = [];

        $paginate = $repository->paginate(2);
        $this->assertEquals(0, $paginate->count());

        foreach ($usersArray as $user) {
            $users[] = $repository->store($user);
        }

        $paginate = $repository->paginate(2);
        $this->assertEquals(2, $paginate->count());
    }
}
