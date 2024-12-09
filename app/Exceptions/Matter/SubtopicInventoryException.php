<?php
namespace App\Exceptions\Matter;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class SubtopicInventoryException extends CustomException
{

    public static function subtopicNotFound(): SubtopicInventoryException
    {
        return new self("The subtopic not found", Response::HTTP_NOT_FOUND);
    }
}
