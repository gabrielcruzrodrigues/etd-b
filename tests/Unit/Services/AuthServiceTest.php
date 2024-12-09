<?php

use App\Services\User\AuthService;
use App\Models\User\User;
use App\Exceptions\AuthExceptions;
use App\Notifications\RegistrationConfirmationNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;


describe('AuthService', function () {
  beforeEach(function () {
    $this->authService = new AuthService();
  });

  describe('registerUser', function () {
    it('should create a new user and send registration confirmation', function () {
      Notification::fake();

      $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'phone'=>'99 9999-9999'
      ];

      $this->authService->registerUser($userData);

      $this->assertDatabaseHas('users', [
        'email' => $userData['email'],
        'name' => $userData['name']
      ]);

      $user = User::where('email', $userData['email'])->first();

      Notification::assertSentTo($user, RegistrationConfirmationNotification::class);
    });
  });

  describe('login', function () {
    it('should generate access and refresh tokens for valid credentials', function () {
      $user = User::factory()->create([
        'password' => Hash::make('password123')
      ]);

      $credentials = [
        'email' => $user->email,
        'password' => 'password123'
      ];

      $result = $this->authService->login($credentials);

      expect($result)->toHaveKeys(['access_token', 'refresh_token'])
        ->and($result['access_token'])->toBeString()
        ->and($result['refresh_token'])->toBeString();
    });

    it('should throw exception for invalid credentials', function () {
      $credentials = [
        'email' => 'wrong@example.com',
        'password' => 'wrongpassword'
      ];

      $this->expectException(AuthExceptions::class);
      $this->expectExceptionMessage('The provided credentials are incorrect.');
      $this->expectExceptionCode(Response::HTTP_UNAUTHORIZED);

      $this->authService->login($credentials);
    });
  });

  describe('sendResetLink', function () {
    it('should send reset link successfully', function () {
      $user = User::factory()->create();

      Password::shouldReceive('sendResetLink')
        ->once()
        ->andReturn(Password::RESET_LINK_SENT);

      $this->authService->sendResetLink(['email' => $user->email]);

      Password::shouldHaveReceived('sendResetLink')
        ->once()
        ->with(['email' => $user->email]);
    });

    it('should throw exception when reset link fails', function () {
      Password::shouldReceive('sendResetLink')
        ->once()
        ->andReturn(Password::INVALID_USER);

      $this->expectException(AuthExceptions::class);
      $this->expectExceptionMessage('Failed to send reset link. Please check the provided email.');
      $this->expectExceptionCode(Response::HTTP_BAD_REQUEST);

      $this->authService->sendResetLink(['email' => 'nonexistent@example.com']);
    });
  });

  describe('resetPassword', function () {
    it('should reset password successfully', function () {
      Notification::fake();

      $user = User::factory()->create();

      $data = [
        'email' => $user->email,
        'password' => 'newpassword123',
        'token' => 'valid-token'
      ];

      Password::shouldReceive('reset')
        ->once()
        ->andReturn(Password::PASSWORD_RESET);

      $status = $this->authService->resetPassword($data);

      expect($status)->toBe(Password::PASSWORD_RESET);

      Password::shouldHaveReceived('reset')->once();
    });
  });
});
