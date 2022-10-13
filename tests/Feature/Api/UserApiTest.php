<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    protected string $endpoint = '/api/users';

    /**
     * @dataProvider dataProviderPagination
     */
    public function test_get_paginate(
        int $total,
        int $page = 1,
        int $totalPage = 15
    ) {
        User::factory()->count($total)->create();

        $response = $this->getJson("{$this->endpoint}?page={$page}");

        $response->assertOk();
        $response->assertJsonCount($totalPage, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                ]
            ],
            'meta' => [
                'total',
                'current_page',
                'first_page',
                'last_page',
                'per_page',
            ]
        ]);
        $response->assertJsonFragment([
            'total' => $total,
            'current_page' => $page
        ]);
    }

    public function dataProviderPagination(): array
    {
        return [
            'test total paginate empty'     => ['total' => 0, 'page' => 1, 'totalPage' => 0],
            'test total 40 users page one'  => ['total' => 40, 'page' => 1, 'totalPage' => 15],
            'test total 20 users page two'  => ['total' => 20, 'page' => 2, 'totalPage' => 5],
            'test total 10 users page two'  => ['total' => 10, 'page' => 2, 'totalPage' => 0],
            'test total 10 users page two'  => ['total' => 10, 'page' => 2, 'totalPage' => 0],
        ];
    }

    /**
     * @dataProvider dataProviderCreateUser
     */
    public function test_create(
        array $payload,
        int $statusCode,
        array $structureResponse
    ) {
        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus($statusCode);
        $response->assertJsonStructure($structureResponse);
    }

    public function dataProviderCreateUser(): array
    {
        return [
            'test created' => [
                'payload' => [
                    'name'      => 'Leandro',
                    'email'     => 'leandro@email.com',
                    'password'  => '123456789',
                ],
                'statusCode' => Response::HTTP_CREATED,
                'structureResponse' => [
                    'data' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ],
            'test validation' => [
                'payload' => [],
                'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'structureResponse' => [],
            ],
        ];
    }

    public function test_find()
    {
        $user = User::factory()->create();

        $response = $this->getJson("{$this->endpoint}/{$user->email}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
            ],
        ]);
    }

    public function test_find_not_found()
    {

        $response = $this->getJson("{$this->endpoint}/fake_email@email.com");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @dataProvider providerUserUpdate
     */
    public function test_update(
        array $payload,
        int $statusCode,
    ) {
        $user = User::factory()->create();

        $response = $this->putJson("{$this->endpoint}/{$user->email}", $payload);

        $response->assertStatus($statusCode);
    }

    public function providerUserUpdate(): array
    {
        return [
            'test update ok' => [
                'payload' => [
                    'name' => 'Name Update',
                    'password' => '123456789',
                ],
                'statusCode' => Response::HTTP_OK,
            ],
            'test update without password' => [
                'payload' => [
                    'name' => 'Name Update',
                ],
                'statusCode' => Response::HTTP_OK,
            ],
            'test update without name' => [
                'payload' => [
                    'password' => '123456789',
                ],
                'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
            'test update empty payload' => [
                'payload' => [],
                'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ],
        ];
    }

    public function test_update_not_found()
    {
        $response = $this->putJson("{$this->endpoint}/email_fake@email.com", [
            'name' => 'Leandro Willian',
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_delete()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("{$this->endpoint}/{$user->email}");

        $response->assertNoContent();
    }

    public function test_delete_not_found()
    {
        $response = $this->deleteJson("{$this->endpoint}/email_fake@email.com");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
