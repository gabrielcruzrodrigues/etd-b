<?php

namespace App\Services\User;

use App\Contracts\UserQuestionAnsweredServiceContract;
use App\Exceptions\CustomException;
use App\Models\Question\Question;
use App\Models\User\User;
use App\Models\User\UserQuestionAnswered;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class UserQuestionAnsweredService implements UserQuestionAnsweredServiceContract
{
    /**
     * @throws CustomException
     */
    public function create(array $data): UserQuestionAnswered
     {
          try
          {
               User::findOrFail($data['user_id']);
               Question::findOrFail($data['question_id']);
               return UserQuestionAnswered::create($data);
          }
          catch (ModelNotFoundException $ex)
          {
               throw CustomException::notFound("User/Question", $data["user_id"] . "/" . $data["question_id"]);
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionAnsweredService->create : UserQuestionAnsweredService - " . $ex->getMessage());
               throw CustomException::unexpectedError($ex->getMessage());
          }
     }

    /**
     * @throws CustomException
     */
    public function update(array $data, int $userId, int $questionId): void
     {
          try
          {
               $userQuestionAnswered = UserQuestionAnswered::where('user_id', $userId)
                    ->where('question_id', $questionId)
                    ->firstOrFail();

               $userQuestionAnswered->fill($data);

               if (!$userQuestionAnswered->isDirty()) {
                    throw CustomException::noChangesDetectedForUpdate();
               }

               UserQuestionAnswered::where('user_id', $userId)
                    ->where('question_id', $questionId)
                    ->update($data);
          }
          catch (ModelNotFoundException $ex)
          {
               throw CustomException::notFound("User/Question", "{$userId}/{$questionId}");
          }
          catch (CustomException $ex)
          {
               throw $ex;
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionAnsweredService->update : Unexpected Exception - " . $ex->getMessage());
               throw CustomException::unexpectedError($ex->getMessage());
          }
     }

    /**
     * @throws CustomException
     */
    public function delete(int $userId, int $questionId): void
     {
          try
          {
               UserQuestionAnswered::where('user_id', $userId)
                    ->where('question_id', $questionId)
                    ->firstOrFail();

               UserQuestionAnswered::where('user_id', $userId)
                    ->where('question_id', $questionId)
                    ->delete();
          }
          catch (ModelNotFoundException $ex)
          {
               throw CustomException::notFound("User/Question", "{$userId}/{$questionId}");
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionAnsweredService->delete : Unexpected Exception - " . $ex->getMessage());

               throw CustomException::unexpectedError($ex->getMessage());
          }
     }

     public function getAll(int $page): LengthAwarePaginator
     {
          return UserQuestionAnswered::paginate($page);
     }

    /**
     * @throws CustomException
     */
    public function getByUser(int $userId): Collection
     {
          try
          {
               //verify if user exists
               UserQuestionAnswered::where('user_id', $userId)->firstOrFail();
               return UserQuestionAnswered::where('user_id', $userId)->get();
          }
          catch (ModelNotFoundException $ex)
          {
               throw CustomException::notFound("User", "{$userId}");
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionAnsweredService->getByUser : Unexpected Exception - " . $ex->getMessage());
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
               UserQuestionAnswered::where('question_id', $questionId)->firstOrFail();

               return UserQuestionAnswered::where('question_id', $questionId)->get();
          }
          catch (ModelNotFoundException $ex)
          {
               throw CustomException::notFound("Question", "{$questionId}");
          }
          catch (Exception $ex)
          {
               Log::error("UserQuestionAnsweredService->getByQuestion : Unexpected Exception - " . $ex->getMessage());
               throw CustomException::unexpectedError($ex->getMessage());
          }
     }
}
