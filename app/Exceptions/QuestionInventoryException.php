<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class QuestionInventoryException extends CustomException
{

    public static function questionNotFound(): QuestionInventoryException
    {
        return new self("The question not found", Response::HTTP_NOT_FOUND);
    }

    public static function deleteError(): QuestionInventoryException
    {
        return new self("Un erro occurred when tryning delete a question", Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function queryError(): QuestionInventoryException
    {
        return new self("Un erro occurred when tryning execute a query", Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function invalidId(): QuestionInventoryException
    {
        return new self("The id must not to be less than or equal to zero", Response::HTTP_BAD_REQUEST);
    }

    public static function isNotFile(): QuestionInventoryException
    {
        return new self("The image is not file", Response::HTTP_BAD_REQUEST);
    }

    public static function saveFileError(): QuestionInventoryException
    {
        return new self("Un erro occurred when tryning save file in folder", Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function scriptException(string $message): QuestionInventoryException
    {
        return new self($message, Response::HTTP_BAD_REQUEST);
    }

    public static function downloadException(string $message): QuestionInventoryException
    {
        return new self($message, Response::HTTP_BAD_REQUEST);
    }
}
