<?php

namespace Tests\Feature\App\Repository\Eloquent;

use App\Models\User;
use App\Repository\Contracts\UserRepositoryInterface;
use App\Repository\Exceptions\NotFoundException;
use App\Repository\Eloquent\UserRepository;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    protected $repository;

    protected function setUp(): void
    {
        $this->repository = new UserRepository(new User());

        parent::setUp();
    }

    public function test_implements_interface()
    {
        $this->assertInstanceOf(
            UserRepositoryInterface::class,
            $this->repository
        );
    }

    public function test_find_all_empty()
    {
        $response = $this->repository->findAll();

        $this->assertIsArray($response);
        $this->assertCount(0, $response);
    }

    public function test_find_all()
    {
        User::factory()->count(10)->create();
        $response = $this->repository->findAll();

        $this->assertIsArray($response);
        $this->assertCount(10, $response);
    }

    public function test_create()
    {
        $data = [
            'name' => 'Lendro Willian',
            'email' => 'leandro@email.com',
            'password' => bcrypt('123456789')
        ];

        $response = $this->repository->create($data);

        $this->assertNotNull($response);
        $this->assertIsObject($response);
        $this->assertDatabaseHas('users', [
            'email' => $data['email']
        ]);
    }


    public function test_create_exception()
    {
        $this->expectException(QueryException::class);

        $data = [
            'name' => 'Lendro Willian',
            'password' => bcrypt('123456789')
        ];

        $this->repository->create($data);
    }

    public function test_update()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'new Name'
        ];

        $response = $this->repository->update($user->email, $data);

        $this->assertNotNull($response);
        $this->assertIsObject($response);
        $this->assertDatabaseHas('users', [
            'name' => $data['name']
        ]);
    }

    public function test_delete()
    {
        $user = User::factory()->create();

        $deleted = $this->repository->delete($user->email);

        $this->assertTrue($deleted);

        $this->assertDatabaseMissing('users', [
            'email' => $user->email
        ]);
    }

    public function test_delete_not_found()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->delete('fake_email@email.com');

        // try {
        //     $this->repository->delete('fake_email@email.com');

        //     $this->assertTrue(false);
        // } catch (\Throwable $th) {
        //     $this->assertInstanceOf(NotFoundException::class, $th);
        // }
    }

    public function test_find()
    {
        $user = User::factory()->create();

        $response = $this->repository->find($user->email);

        $this->assertIsObject($response);
    }

    public function test_find_not_found()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->find('fake_email@email.com');
    }
}
