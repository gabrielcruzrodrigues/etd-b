<?php
namespace App\Exceptions\Matter;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class TopicInventoryException extends CustomException
{
    public static function topicNotFound(): TopicInventoryException
    {
        return new self("The topic not found", Response::HTTP_NOT_FOUND);
    }
}
