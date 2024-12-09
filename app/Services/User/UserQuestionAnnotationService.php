<?php

namespace App\Services\User;

use App\Contracts\UserQuestionAnnotationServiceContract;
use App\Exceptions\CustomException;
use App\Exceptions\QuestionInventoryException;
use App\Models\Question\Question;
use App\Models\User\User;
use App\Models\User\UserQuestionAnnotation;
use App\Models\User\UserQuestionComment;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class UserQuestionAnnotationService implements UserQuestionAnnotationServiceContract
{
     /**
      * @throws CustomException
      */
     public function create(array $data): UserQuestionAnnotation
     {
          try 
          {
               User::findOrFail($data['user_id']);
               Question::findOrFail($data['question_id']);
               return UserQuestionAnnotation::create($data);
          } 
          catch (ModelNotFoundException $ex) 
          {
               throw QuestionInventoryException::questionNotFound("User/Question", $data["user_id"] . "/" . $data["question_id"]);
          } 
          catch (Exception $ex) 
          {
               Log::error("UserQuestionAnnotationService->create : UserQuestionAnnotationService - " . $ex->getMessage());
               throw QuestionInventoryException::unexpectedError($ex->getMessage());
          }
     }

     /**
     * @throws CustomException
     */
     public function update(array $data, int $id): void 
     {
          try
          {
               $userQuestionAnnotation = UserQuestionAnnotation::findOrFail($id);

               $userQuestionAnnotation->fill($data);

               if (!$userQuestionAnnotation->isDirty()) {
                    throw CustomException::noChangesDetectedForUpdate();
               }

               $userQuestionAnnotation->save();
          }
          catch (ModelNotFoundException $ex)
          {
               throw QuestionInventoryException::questionNotFound("UserQuestionAnnotation", $id);
          }
          catch (CustomException $ex)
          {
               throw $ex;
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionAnnotationService->update : Unexpected Exception - " . $ex->getMessage());
               throw CustomException::unexpectedError($ex->getMessage());
          }
     }

     /**
     * @throws CustomException
     */
     public function delete(int $id): void 
     {
          try
          {
               $userQuestionComment = UserQuestionAnnotation::findOrFail($id);
               $userQuestionComment->delete();
          }
          catch (ModelNotFoundException $ex)
          {
               throw QuestionInventoryException::questionNotFound();
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionAnnotationService->delete : Unexpected Exception - " . $ex->getMessage());

               throw CustomException::unexpectedError($ex->getMessage());
          }
     }


     public function getAll(int $page, int $perPage = 15): LengthAwarePaginator
     {
          return UserQuestionAnnotation::paginate($perPage, ['*'], 'page', $page);
     }

     /**
     * @throws CustomException
     */
     public function getByUser(int $userId): Collection 
     {
          try
          {
               //verify if user exists
               UserQuestionAnnotation::where('user_id', $userId)->firstOrFail();
               return UserQuestionAnnotation::where('user_id', $userId)->get();
          }
          catch (ModelNotFoundException $ex)
          {
               throw QuestionInventoryException::questionNotFound();
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionAnnotationService->getByUser : Unexpected Exception - " . $ex->getMessage());
               throw CustomException::unexpectedError($ex->getMessage());
          }
     }

     /**
     * @throws CustomException
     */
     public function getByQuestion(int $questionId): Collection 
     {
          try
          {
               //verify if user exists
               UserQuestionAnnotation::where('question_id', $questionId)->firstOrFail();
               return UserQuestionAnnotation::where('question_id', $questionId)->get();
          }
          catch (ModelNotFoundException $ex)
          {
               throw QuestionInventoryException::questionNotFound();
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionAnnotationService->getByQuestion : Unexpected Exception - " . $ex->getMessage());
               throw CustomException::unexpectedError($ex->getMessage());
          }
     }

     /**
     * @throws CustomException
     */
    public function getByUserAndQuestionId(int $userId, int $questionId): Collection
    {
          try
          {
               User::where('id', $userId)->firstOrFail();
               Question::where('id', $questionId)->firstOrFail();

               $annotation = UserQuestionAnnotation::where('user_id', $userId)
                    ->where('question_id', $questionId)
                    ->get();

               return $annotation;
          }
          catch (ModelNotFoundException $ex)
          {
              throw QuestionInventoryException::notFound("User | Question", "{$userId} | {$questionId}");
          }
          catch (Exception $ex)
          {
              Log::error("UserQuestionAnnotationService->getByQuestion : Unexpected Exception - " . $ex->getMessage());
              throw CustomException::unexpectedError($ex->getMessage());
          }
    }
}
