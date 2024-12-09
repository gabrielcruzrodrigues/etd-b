<?php
namespace App\Contracts;

use App\Models\User\User;

interface AuthServiceContract
{
  /**
   * Register a new user.
   *
   * @param array $data
   * @return void
   */
  public function registerUser(array $data): void;

  /**
   * Handle user login and generate access and refresh tokens.
   *
   * @param array $data
   * @return array
   */
  public function login(array $data): array;

  /**
   * Send a reset password link to the user.
   *
   * @param array $data
   * @return void
   */
  public function sendResetLink(array $data): void;

  /**
   * Reset the user password.
   *
   * @param array $data
   * @return mixed
   */
  public function resetPassword(array $data): mixed;
}
