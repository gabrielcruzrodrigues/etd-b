<?php

namespace App\Services\User;

use App\Contracts\UserQuestionCommentServiceContract;
use App\Exceptions\CustomException;
use App\Exceptions\QuestionInventoryException;
use App\Exceptions\UserException;
use App\Models\Question\Question;
use App\Models\User\User;
use App\Models\User\UserQuestionComment;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class UserQuestionCommentService implements UserQuestionCommentServiceContract
{
     /**
      * @throws CustomException
      */
     public function create(array $data): UserQuestionComment
     {
          try
          {
               User::findOrFail($data['user_id']);
               Question::findOrFail($data['question_id']);
               return UserQuestionComment::create($data);
          }
          catch (ModelNotFoundException $ex)
          {
               throw QuestionInventoryException::questionNotFound("User/Question", $data["user_id"] . "/" . $data["question_id"]);
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionCommentService->create : UserQuestionCommentService - " . $ex->getMessage());
               throw CustomException::unexpectedError($ex->getMessage());
          }
     }

     /**
     * @throws CustomException
     */
     public function update(array $data, int $id): void
     {
          try
          {
               $userQuestionComment = UserQuestionComment::findOrFail($id);

               $userQuestionComment->fill($data);

               if (!$userQuestionComment->isDirty()) {
                    throw CustomException::noChangesDetectedForUpdate();
               }

               $userQuestionComment->save();
          }
          catch (ModelNotFoundException $ex)
          {
               throw QuestionInventoryException::notFound("UserQuestionComments", "{$id}");
          }
          catch (CustomException $ex)
          {
               throw $ex;
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionCommentService->update : Unexpected Exception - " . $ex->getMessage());
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
               $userQuestionComment = UserQuestionComment::findOrFail($id);
               $userQuestionComment->delete();
          }
          catch (ModelNotFoundException $ex)
          {
               throw UserException::notFound("UserQuestionComment", $id);
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionCommentService->delete : Unexpected Exception - " . $ex->getMessage());

               throw CustomException::unexpectedError($ex->getMessage());
          }
     }


     public function getAll(int $page, int $perPage = 15): LengthAwarePaginator
     {
          return UserQuestionComment::paginate($perPage, ['*'], 'page', $page);
     }

     /**
     * @throws CustomException
     */
     public function getByUser(int $userId): Collection
     {
          try
          {
               //verify if user exists
               UserQuestionComment::where('user_id', $userId)->firstOrFail();
               return UserQuestionComment::where('user_id', $userId)->get();
          }
          catch (ModelNotFoundException $ex)
          {
               throw UserException::notFound("UserQuestionComments - user_id", $userId);
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionCommentService->getByUser : Unexpected Exception - " . $ex->getMessage());
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
               UserQuestionComment::where('question_id', $questionId)->firstOrFail();
               return UserQuestionComment::where('question_id', $questionId)->get();
          }
          catch (ModelNotFoundException $ex)
          {
               throw UserException::notFound("UserQuestionComments - question_id", $questionId);
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionCommentService->getByQuestion : Unexpected Exception - " . $ex->getMessage());
               throw CustomException::unexpectedError($ex->getMessage());
          }
     }
}
