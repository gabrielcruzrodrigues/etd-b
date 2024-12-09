<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UserException extends CustomException
{
    public static function UserNotFound(): UserException
    {
        return new self('User not found.', Response::HTTP_NOT_FOUND);
    }
}
