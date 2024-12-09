<?php

namespace App\Services\User;

use App\Contracts\AuthServiceContract;
use App\Exceptions\AuthExceptions;
use App\Models\User\User;
use App\Notifications\PasswordResetConfirmationNotification;
use App\Notifications\RegistrationConfirmationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;


class AuthService implements AuthServiceContract
{
    public function registerUser(array $data): void
    {
        $user = User::create($data);

        $user->notify(new RegistrationConfirmationNotification());
    }

    /**
     * @throws AuthExceptions
     */
    public function login(array $data): array
    {
        if (!Auth::attempt($data)) {
            throw AuthExceptions::InvalidUserCredentials();
        }

        $user = Auth::user();

        $accessToken = $user->createToken(
            'access-api',
            [$user->role],
            now()->addMinutes(config('sanctum.ac_expiration'))
        )->plainTextToken;

        $refreshToken = $user->createToken(
            'refresh-token',
            ['refresh_token'],
            now()->addMinutes(config('sanctum.rt_expiration'))
        )->plainTextToken;

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }
    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }


    /**
     * @throws AuthExceptions
     */
    public function sendResetLink(array $data): void
    {
        $status = Password::sendResetLink($data);

        if ($status !== Password::RESET_LINK_SENT) {
            throw AuthExceptions::FailedToSendResetLink();
        }
    }

    /**
     * @throws AuthExceptions
     */
    public function resetPassword(array $data): mixed
    {
        $status = Password::reset($data, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();

            $user->notify(new PasswordResetConfirmationNotification());
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw AuthExceptions::PasswordResetAttemptFailed();
        }

        return $status;
    }

}
