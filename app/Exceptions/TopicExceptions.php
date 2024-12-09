<?php
namespace App\Exceptions;

use Exception;
use Throwable;

class TopicExceptions extends CustomException
{
     

     public static function createTopicError(): self
     {
          $message = "Un erro occurred when tryning create a new topic";
          return new self(
               message: $message,
               code: 400 
          );
     }

     public static function topicNotFound()
     {
          $message = "The topic not found";
          return new self(
               message: $message,
               code: 404
          );
     }

     public static function updateTopicError(): self
     {
          $message = "Un erro occurred when tryning update a topic";
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

     public static function paginateTopicError(): self
     {
          $message = "An erro occurred when trying paginate the topics";
          return new self(
               message: $message,
               code: 500
          );
     }

     public static function deleteTopicError(): self
     {
          $message = "Un erro occurred when tryning delete a topic";
          return new self(
               message: $message,
               code: 500
          );
     }

     public static function invalidTopicId(): self
     {
          $message = "The id must not to be less than or equal to zero";
          return new self(
               message: $message,
               code: 400
          );
     }
}