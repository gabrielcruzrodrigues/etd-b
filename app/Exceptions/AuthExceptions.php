<?php

namespace App\Exceptions;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class AuthExceptions extends CustomException
{
    public static function InvalidUserCredentials(): AuthExceptions
    {
        return new self('The provided credentials are incorrect.', Response::HTTP_UNAUTHORIZED);
    }

    public static function FailedToSendResetLink(): AuthExceptions
    {
        return new self('Failed to send reset link. Please check the provided email.', Response::HTTP_BAD_REQUEST);
    }
    public static function PasswordResetAttemptFailed(): AuthExceptions
    {
        return new self('An error occurred while resetting your password.', Response::HTTP_BAD_REQUEST);
    }
}
