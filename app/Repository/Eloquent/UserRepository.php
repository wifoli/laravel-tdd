<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\Contracts\UserRepositoryInterface;
use App\Repository\Exceptions\NotFoundException;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        protected User $model
    ) {
    }

    public function findAll(): array
    {
        return $this->model->get()->toArray();
    }

    public function create(array $data): object
    {
        return $this->model->create($data);
    }

    public function update(string $email, array $data): object
    {
        $user = $this->find($email);
        $user->update($data);

        $user->refresh();

        return $user;
    }

    public function delete(string $email): bool
    {
        if (!$user = $this->find($email))
            throw new NotFoundException('User not Found');

        return $user->delete();
    }

    public function find(string $email): object|null
    {
        return $this->model->whereEmail($email)->first();
    }
}
