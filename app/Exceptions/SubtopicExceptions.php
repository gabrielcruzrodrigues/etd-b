<?php
namespace App\Exceptions;

use Exception;
use Throwable;

class SubtopicExceptions extends CustomException
{
     

     public static function createSubtopicError(): self
     {
          $message = "Un erro occurred when tryning create a new subtopic";
          return new self(
               message: $message,
               code: 400 
          );
     }

     public static function subtopicNotFound()
     {
          $message = "The subtopic not found";
          return new self(
               message: $message,
               code: 404
          );
     }

     public static function updateSubtopicError(): self
     {
          $message = "Un erro occurred when tryning update a subtopic";
          return new self(
               message: $message,
               code: 400
          );
     }

     public static function databaseError(): self
     {
          $message = "Un erro occurred with database";
          return new self(
               message: $message,
               code: 500
          );
     }

     public static function paginateSubtopicError(): self
     {
          $message = "An erro occurred when trying paginate the subtopics";
          return new self(
               message: $message,
               code: 500
          );
     }

     public static function deleteSubtopicError(): self
     {
          $message = "Un erro occurred when tryning delete a subtopic";
          return new self(
               message: $message,
               code: 500
          );
     }

     public static function invalidSubtopicId(): self
     {
          $message = "The id must not to be less than or equal to zero";
          return new self(
               message: $message,
               code: 400
          );
     }
}