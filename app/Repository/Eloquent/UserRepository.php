<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\Contracts\PaginationInterface;
use App\Repository\Contracts\UserRepositoryInterface;
use App\Repository\Exceptions\NotFoundException;
use App\Repository\Presenters\PaginationEloquentPresenter;

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

    public function paginate(int $page = 1): PaginationInterface
    {
        return new PaginationEloquentPresenter($this->model->paginate());
    }


    public function create(array $data): object
    {
        $data['password'] = bcrypt($data['password']);

        return $this->model->create($data);
    }

    public function update(string $email, array $data): object
    {
        if (isset($data['password']))
            $data['password'] = bcrypt($data['password']);

        $user = $this->find($email);
        $user->update($data);

        $user->refresh();

        return $user;
    }

    public function delete(string $email): bool
    {
        $user = $this->find($email);

        return $user->delete();
    }

    public function find(string $email): object
    {
        if (!$user = $this->model->whereEmail($email)->first())
            throw new NotFoundException('User not Found');

        return $user;
    }
}
