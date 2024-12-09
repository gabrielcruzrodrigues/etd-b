<?php

namespace App\Exceptions;

use App\Enums\ResponseStatus;
use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CustomException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'status' => ResponseStatus::ERROR->value,
            'message' => $this->getMessage(),
            'code' => $this->getCode()],
            $this->getCode());
    }

    public static function notFound(string $entity, string $paramForSearch): CustomException
    {
        return new self(
            "The {$entity} searched with {$paramForSearch} not found!",
            Response::HTTP_NOT_FOUND
        );
    }

    public static function unexpectedError(string $errorMessage): CustomException
    {
        return new self(
            $errorMessage,
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    public static function noChangesDetectedForUpdate(): CustomException
    {
        return new self(
            "changes not detected on entity for update",
            Response::HTTP_BAD_REQUEST
        );
    }

    public static function paginationExeption(): CustomException
    {
        return new self(
            "Elements for pagination not found!",
            Response::HTTP_BAD_REQUEST
        );
    }

    
}
