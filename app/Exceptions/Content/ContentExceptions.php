<?php
namespace App\Exceptions\Content;

use App\Exceptions\CustomException;
use Symfony\Component\HttpFoundation\Response;

class ContentExceptions  extends CustomException
{
    public static function notFoundContent(){
        return new self(
             "Content not found", Response::HTTP_NOT_FOUND
        );
    }

    public static function alreadyExistsContent(){
        return new self(
            'Content with this name already exists.', Response::HTTP_OK
        );
    }
}