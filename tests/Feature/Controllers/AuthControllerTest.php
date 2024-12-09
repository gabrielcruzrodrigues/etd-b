<?php


use App\Models\User\User;
use App\Notifications\PasswordResetConfirmationNotification;
use App\Notifications\RegistrationConfirmationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('old_password'),
    ]);
});

describe('Auth controller', function () {
    describe('Route: api/register', function () {

        it('registers a new user and sends a confirmation notification', function () {
            Notification::fake();

      $userData = [
        'name' => 'Test User',
        'email' => 'registertest@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone'=>'99 9999-9999'
      ];

            $response = $this->postJson('api/register', $userData);

            $response->assertStatus(Response::HTTP_CREATED);

            $response->assertJson([
                'message' => 'Registration completed successfully.',
            ]);

            $this->assertDatabaseHas('users', [
                'email' => 'registertest@example.com',
            ]);

            $user = User::where('email', 'registertest@example.com')->first();

            Notification::assertSentTo($user, RegistrationConfirmationNotification::class);
        });
        it('fails when required fields are missing', function () {
            Notification::fake();
            $userData = [
                'email' => 'test@example.com',
            ];

            $response = $this->postJson('api/register', $userData);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            $response->assertJsonValidationErrors(['name', 'password']);
            Notification::assertNothingSent();
        });
        it('fails when email is invalid', function () {
            Notification::fake();

            $userData = [
                'name' => 'Test User',
                'email' => 'invalid-email',
                'password' => 'password',
                'password_confirmation' => 'password',
                'date_of_birth' => '1990-01-01',
            ];

            $response = $this->postJson('api/register', $userData);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            $response->assertJsonValidationErrors(['email']);

            Notification::assertNothingSent();
        });
        it('fails when password confirmation does not match', function () {
            Notification::fake();

            $userData = [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'different_password'
            ];

            $response = $this->postJson('api/register', $userData);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            $response->assertJsonValidationErrors(['password']);

            Notification::assertNothingSent();
        });
    });

    describe('Route: api/login', function () {
        it('logs in successfully with valid credentials', function () {
            Config::set('sanctum.ac_expiration', 60);
            Config::set('sanctum.rt_expiration', 10080);

            $userData = [
                'email' => 'test@example.com',
                'password' => 'password123',
            ];

            Auth::shouldReceive('attempt')->once()->with([
                'email' => 'test@example.com',
                'password' => 'password123',
            ])->andReturnTrue();

            Auth::shouldReceive('user')->andReturn($this->user);

            $response = $this->postJson('/api/login', $userData);

            $response->assertStatus(200);
            $response->assertJsonStructure([
                'message',
                'data' => [
                    'access_token',
                    'refresh_token',
                    'expires_in',
                ],
            ]);
        });
        it('fails when credentials are invalid', function () {
            Auth::shouldReceive('attempt')->once()->with([
                'email' => 'test@example.com',
                'password' => 'wrong_password',
            ])->andReturnFalse();

            $userData = [
                'email' => 'test@example.com',
                'password' => 'wrong_password',
            ];

            $response = $this->postJson('/api/login', $userData);

            $response->assertStatus(Response::HTTP_UNAUTHORIZED);

            $response->assertJson([
                'message' => 'The provided credentials are incorrect.',
            ]);
        });
        it('fails when no credentials are provided', function () {
            $response = $this->postJson('/api/login', []);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            $response->assertJsonValidationErrors(['email', 'password']);
        });
        it('fails when email is not provided', function () {
            $userData = [
                'password' => 'some_password',
            ];

            $response = $this->postJson('/api/login', $userData);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

            $response->assertJsonValidationErrors(['email']);
        });
    });

    describe('Route: api/refresh-token', function () {

        it('can refresh the access token successfully', function () {

            Config::set('sanctum.ac_expiration', 60);

            $refreshToken = $this->user->createToken('access_token', ['refresh_token'])->plainTextToken;

            Sanctum::actingAs($this->user, ['refresh_token']);

            $response = $this->getJson('/api/refresh-token', [
                'Authorization' => 'Bearer ' . $refreshToken
            ]);

            $response->assertStatus(Response::HTTP_OK);
            $response->assertJsonStructure([
                'message',
                'access_token',
                'expires_in',
            ]);

            expect($response->json('access_token'))->not->toBeNull();
        });

        it('fails when no token is provided in the request', function () {

            Config::set('sanctum.ac_expiration', 60);

            $response = $this->getJson('/api/refresh-token');

            $response->assertStatus(Response::HTTP_UNAUTHORIZED);
            $response->assertJson([
                'message' => 'Unauthenticated.',
            ]);
        });

        it('fails when the token does not have the required refresh_token ability', function () {
            $accessToken = $this->user->createToken('access_token', ['access-api'])->plainTextToken;

            Sanctum::actingAs($this->user, ['access-api']);

            $response = $this->getJson('/api/refresh-token', [
                'Authorization' => 'Bearer ' . $accessToken
            ]);

            $response->assertStatus(Response::HTTP_FORBIDDEN);
            $response->assertJson([
                'message' => 'This action is unauthorized.',
            ]);
        });

        it('invalidates the old access token after refresh', function () {
            Config::set('sanctum.ac_expiration', 60);

            $oldAccessToken = $this->user->createToken('access_token')->plainTextToken;
            $refreshToken = $this->user->createToken('refresh-token', ['refresh_token'])->plainTextToken;

            Sanctum::actingAs($this->user, ['refresh_token']);

            $response = $this->getJson('/api/refresh-token', [
                'Authorization' => 'Bearer ' . $refreshToken
            ]);

            $response->assertStatus(Response::HTTP_OK);

            $this->assertFalse(
                $this->user->tokens()->where('token', hash('sha256', $oldAccessToken))->exists()
            );
        });
    });

    describe('Route: api/logout', function () {
        it('revokes the current access token successfully on logout', function () {
            $accessToken = $this->user->createToken('access_token')->plainTextToken;

            Sanctum::actingAs($this->user);

            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->getJson('api/logout');

            $response->assertStatus(200);
            $response->assertJson([
                'message' => 'Token Revoked',
            ]);
        });

        it('fails when trying to logout without a valid token', function () {
            $response = $this->getJson('api/logout');

            $response->assertStatus(Response::HTTP_UNAUTHORIZED);
            $response->assertJson([
                'message' => 'Unauthenticated.',
            ]);
        });
    });

    describe('Route: api/password/forgot', function () {
        it('sends the password reset link successfully', function () {
            $userData = [
                'email' => 'test@example.com',
            ];

            Password::shouldReceive('sendResetLink')
                ->once()
                ->with($userData)
                ->andReturn(Password::RESET_LINK_SENT);

            $response = $this->postJson('api/password/forgot', $userData);

            $response->assertStatus(200);
            $response->assertJson([
                'message' => 'Reset link sent to your email.',
            ]);
        });

        it('fails to send the password reset link', function () {
            $userData = [
                'email' => 'test@example.com',
            ];

            Password::shouldReceive('sendResetLink')
                ->once()
                ->with($userData)
                ->andReturn(Password::INVALID_USER);

            $response = $this->postJson('api/password/forgot', $userData);

            $response->assertStatus(Response::HTTP_BAD_REQUEST);
            $response->assertJson([
                'message' => 'Failed to send reset link. Please check the provided email.',
            ]);
        });

        it('fails when email is not provided', function () {
            $response = $this->postJson('api/password/forgot', []);

            $response->assertStatus(422);

            $response->assertJsonValidationErrors(['email']);
        });

        it('fails when an invalid email is provided', function () {
            $userData = [
                'email' => 'invalid-email-format',
            ];

            $response = $this->postJson('api/password/forgot', $userData);

            $response->assertStatus(422);

            $response->assertJsonValidationErrors(['email']);
        });

    });

    describe('Route: api/password/reset', function () {
        it('resets the password successfully with valid token and credentials', function () {
            Notification::fake();

            $token = Password::createToken($this->user);

            $resetData = [
                'email' => $this->user->email,
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
                'token' => $token
            ];

            $response = $this->postJson('api/password/reset', $resetData);

            $response->assertStatus(Response::HTTP_OK)
                ->assertJson([
                    'message' => 'Password reset successfully.'
                ]);

            $this->user->refresh();

            expect(Hash::check('new-password123', $this->user->password))->toBeTrue();

            Notification::assertSentTo(
                $this->user,
                PasswordResetConfirmationNotification::class
            );
        });
        it('ensures old password no longer works after reset', function () {
            Notification::fake();

            $token = Password::createToken($this->user);

            $resetData = [
                'email' => $this->user->email,
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
                'token' => $token
            ];

            $response = $this->postJson('api/password/reset', $resetData);

            $response->assertStatus(Response::HTTP_OK);

            $this->user->refresh();

            expect(Hash::check('old-password', $this->user->password))->toBeFalse();
        });
        it('fails when token is invalid', function () {
            $response = $this->postJson('/api/password/reset', [
                'token' => 'invalid_token',
                'email' => $this->user->email,
                'password' => 'new_password123',
                'password_confirmation' => 'new_password123'
            ]);

            $response->assertStatus(Response::HTTP_BAD_REQUEST);
        });
        it('fails when email does not exist', function () {
            $token = Password::createToken($this->user);

            $response = $this->postJson('/api/password/reset', [
                'token' => $token,
                'email' => 'nonexistent@example.com',
                'password' => 'new_password123',
                'password_confirmation' => 'new_password123'
            ]);

            $response->assertStatus(Response::HTTP_BAD_REQUEST);
        });
        it('validates required fields', function () {
            $response = $this->postJson('/api/password/reset', []);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJsonValidationErrors(['token', 'email', 'password']);
        });
        it('validates password confirmation', function () {
            $token = Password::createToken($this->user);

            $response = $this->postJson('/api/password/reset', [
                'token' => $token,
                'email' => $this->user->email,
                'password' => 'new_password123',
                'password_confirmation' => 'different_password'
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJsonValidationErrors(['password']);
        });
        it('validates minimum password length', function () {
            $token = Password::createToken($this->user);

            $response = $this->postJson('/api/password/reset', [
                'token' => $token,
                'email' => $this->user->email,
                'password' => '123',
                'password_confirmation' => '123'
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJsonValidationErrors(['password']);
        });
        it('validates email format', function () {
            $token = Password::createToken($this->user);

            $response = $this->postJson('/api/password/reset', [
                'token' => $token,
                'email' => 'invalid-email',
                'password' => 'new_password123',
                'password_confirmation' => 'new_password123'
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJsonValidationErrors(['email']);
        });
    });
});
