<?php
namespace App\Exceptions\Matter;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class MatterServiceExceptions extends CustomException
{

    public static function invalidRequest($message = "Invalid request"): MatterServiceExceptions

    {
        return new self($message, Response::HTTP_BAD_REQUEST);
    }

    public static function serverError($message = "Server error"): MatterServiceExceptions
    {
        return new self($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function matterNotFound($message = "This matter wasn't found"): MatterServiceExceptions
    {
        return new self($message, Response::HTTP_NOT_FOUND);
    }
}
