<?php
namespace App\Exceptions\Matter;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class ContentInventoryException extends CustomException
{

    public static function contentNotFound(): ContentInventoryException
    {
        return new self("The content not found", Response::HTTP_NOT_FOUND);
    }
}
