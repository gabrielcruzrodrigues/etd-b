
<?php

use App\Models\User\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('old_password'),
        'role' => 'admin'
    ]);

    $this->token = $this->user->createToken('access-api', ['admin'])->plainTextToken;
});

describe('UserController', function () {

    describe('GET /users', function () {

        it('returns 30 users by default when per_page is not provided', function () {
            User::factory()->count(50)->create();

            $this->assertDatabaseCount('users', 51);

            $response = $this->withHeaders([
                'Authorization' => "Bearer {$this->token}",
            ])->getJson('api/users/');

            $response->assertStatus(Response::HTTP_OK)
                ->assertJson([
                    'status' => 'success',
                    'code' => Response::HTTP_OK,
                ])
                ->assertJsonPath('data.per_page', 30)
                ->assertJsonPath('data.total', 51)
                ->assertJsonCount(30, 'data.data');
        });

        it('returns the specified number of users when per_page is valid', function () {
            User::factory()->count(150)->create();

            $this->assertDatabaseCount('users', 151);

            $perPage = 50;
            $response = $this->withHeaders([
                'Authorization' => "Bearer {$this->token}",
            ])->getJson("api/users?per_page={$perPage}");

            $response->assertStatus(Response::HTTP_OK)
                ->assertJson([
                    'status' => 'success',
                    'code' => Response::HTTP_OK,
                ])
                ->assertJsonPath('data.per_page', 50)
                ->assertJsonPath('data.total', 151)
                ->assertJsonCount($perPage, 'data.data');
        });

        it('adjusts per_page to 1 when per_page is less than or equal to 0', function () {
            User::factory()->count(10)->create();

            $this->assertDatabaseCount('users', 11);

            $perPage = 0;
            $response = $this->withHeaders([
                'Authorization' => "Bearer {$this->token}",
            ])->getJson("api/users?per_page={$perPage}");

            $response->assertStatus(Response::HTTP_OK)
                ->assertJson([
                    'status' => 'success',
                    'code' => Response::HTTP_OK,
                ])
                ->assertJsonPath('data.per_page', 1)
                ->assertJsonPath('data.total', 11)
                ->assertJsonCount(1, 'data.data');
        });

        it('adjusts per_page to 100 when per_page is greater than 100', function () {
            User::factory()->count(150)->create();

            $this->assertDatabaseCount('users', 151);

            $perPage = 150;
            $response = $this->withHeaders([
                'Authorization' => "Bearer {$this->token}",
            ])->getJson("api/users?per_page={$perPage}");

            $response->assertStatus(Response::HTTP_OK)
                ->assertJson([
                    'status' => 'success',
                    'code' => Response::HTTP_OK,
                ])
                ->assertJsonPath('data.per_page', 100)
                ->assertJsonPath('data.total', 151)
                ->assertJsonCount(100, 'data.data');
        });
    });

    describe('GET /users/{email}', function () {

        it('returns user data when the email exists', function () {
            $user = User::factory()->create([
                'email' => 'existing@example.com',
                'name' => 'Existing User',
            ]);

            $response = $this->withHeaders([
                'Authorization' => "Bearer {$this->token}",
            ])->getJson("api/users/{$user->email}");

            $response->assertStatus(Response::HTTP_OK)
                ->assertJson([
                    'status' => 'success',
                    'code' => Response::HTTP_OK,
                    'data' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                    ],
                ]);
        });

        it('returns a 404 error when the user is not found', function () {
            $nonExistentEmail = 'nonexistent@example.com';

            $response = $this->withHeaders([
                'Authorization' => "Bearer {$this->token}",
            ])->getJson("api/users/{$nonExistentEmail}");

            $response->assertStatus(Response::HTTP_NOT_FOUND)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'User not found.',
                    'code' => Response::HTTP_NOT_FOUND,
                ]);
        });
    });
});
