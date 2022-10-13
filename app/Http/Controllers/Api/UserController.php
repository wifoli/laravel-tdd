<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Repository\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {
    }

    public function index()
    {
        // $users = collect($this->repository->findAll());
        $pagination = $this->repository->paginate();

        return UserResource::collection(collect($pagination->items()))
            ->additional([
                'meta' => [
                    'total' => $pagination->total(),
                    'current_page' => $pagination->currentPage(),
                    'first_page' => $pagination->firstPage(),
                    'last_page' => $pagination->lastPage(),
                    'per_page' => $pagination->perPage(),
                ]
            ]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->repository->create($request->validated());

        return new UserResource($user);
    }

    public function show(string $email)
    {
        $user = $this->repository->find($email);

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, string $email)
    {
        $user = $this->repository->update($email, $request->validated());

        return new UserResource($user);
    }

    public function destroy(string $email)
    {
        $this->repository->delete($email);

        return response()->noContent();
    }
}
