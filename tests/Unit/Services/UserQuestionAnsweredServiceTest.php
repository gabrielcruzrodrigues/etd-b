<?php

use App\Exceptions\CustomException;
use App\Models\Question\Question;
use App\Models\User\User;
use App\Models\User\UserQuestionAnswered;
use App\Services\User\UserQuestionAnsweredService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

describe("UserQuestionAnswered Service", function () {
     beforeEach(function () {
          $this->service = new UserQuestionAnsweredService();
     });

     afterEach(function () {
          Mockery::close();
     });

     test('create must save and return a userQuestionAnswered instance with success', function () {
          $user = User::factory()->create();
          $question = Question::factory()->create();

          $data = [
               'user_id' => $user->id,
               'question_id' => $question->id,
               'alternative' => 'A',
               'answered_at' => now(),
          ];

          $userQuestionAnswered = $this->service->create($data);

          $this->assertDatabaseHas('user_question_answereds', [
               'user_id' => $data['user_id'],
               'question_id' => $data['question_id'],
               'alternative' => $data['alternative'],
          ]);

          $this->assertInstanceOf(UserQuestionAnswered::class, $userQuestionAnswered);
     });

     test('create must return a CustomException::notFound when not found User/Question', function () {
          $nonExistentUserId = 999;
          $nonExistentQuestionId = 999;

          $user = User::factory()->create(['id' => 1]);

          $this->userQuestionAnnotationRequestData = [
               'user_id' => $nonExistentUserId,
               'question_id' => $nonExistentQuestionId,
          ];

          $this->expectException(CustomException::class);
          $this->expectExceptionMessage("The User/Question searched with 999/999 not found!");
          $this->expectExceptionCode(404);

          $this->service->create($this->userQuestionAnnotationRequestData);
     });

     test('update must update the userQuestionAnswered successfuly', function () {
          $user = User::factory()->create();
          $question = Question::factory()->create();
          $userQuestionAnswered = UserQuestionAnswered::factory()->create([
               'user_id' => $user->id,
               'question_id' => $question->id,
               'alternative' => 'A',
          ]);

          $updateData = ['alternative' => 'B'];

          $this->service->update($updateData, $user->id, $question->id);

          $this->assertDatabaseHas('user_question_answereds', [
               'user_id' => $user->id,
               'question_id' => $question->id,
               'alternative' => 'B',
          ]);
     });

     test('update must throw CustomException::noChangesDetectedForUpdate when the object return false in isDirty method', function () {
          $user = User::factory()->create();
          $question = Question::factory()->create();
          UserQuestionAnswered::factory()->create([
               'user_id' => $user->id,
               'question_id' => $question->id,
               'alternative' => 'A',
          ]);

          $updateData = ['alternative' => 'A'];

          $this->expectException(CustomException::class);
          $this->expectExceptionMessage("changes not detected on entity for update");

          $this->service->update($updateData, $user->id, $question->id);
     });

     test('delete must delete a UserQuestionAnswered successfuly', function () {
          $user = User::factory()->create();
          $question = Question::factory()->create();
          UserQuestionAnswered::factory()->create([
               'user_id' => $user->id,
               'question_id' => $question->id,
               'alternative' => 'A',
          ]);

          $this->service->delete($user->id, $question->id);

          $this->assertDatabaseMissing('user_question_answereds', [
               'user_id' => $user->id,
               'question_id' => $question->id,
          ]);
     });

     test('delete must throw CustomException::NotFound() when not found the UserquestionAnswered', function () {
          $nonExistentUserId = 999;
          $nonExistentQuestionId = 999;

          // Define as expectativas para a exceção
          $this->expectException(CustomException::class);
          $this->expectExceptionMessage("The User/Question searched with 999/999 not found!");

          // Chama o método delete com IDs inexistentes, o que deve lançar a exceção
          $this->service->delete($nonExistentUserId, $nonExistentQuestionId);
     });

     test('getAll must return a LengthAwarePaginator with successfuly', function () {
          UserQuestionAnswered::factory()->count(15)->create();

          $perPage = 10;

          $results = $this->service->getAll($perPage);

          $this->assertInstanceOf(LengthAwarePaginator::class, $results);
          $this->assertCount(10, $results);
          $this->assertEquals(1, $results->currentPage());
          $this->assertEquals(15, $results->total());
          $this->assertEquals(2, $results->lastPage());
     });

     test('getByUser must return a UserQuestionAnsered searched by User id', function () {
          $user = User::factory()->create();
          UserQuestionAnswered::factory()->create(['user_id' => $user->id]);
          UserQuestionAnswered::factory()->create(['user_id' => $user->id]);

          $result = $this->service->getByUser($user->id);

          $this->assertInstanceOf(Collection::class, $result);
          $this->assertCount(2, $result);

          $result->each(function ($userQuestionAnswered) use ($user) {
               $this->assertEquals($user->id, $userQuestionAnswered->user_id);
          });
     });

     test('getByUser must throw CustomException::notFound when not found the UserQuestionAnswered By User id', function () {
          $nonExistentUserId = 999;

          $this->expectException(CustomException::class);
          $this->expectExceptionMessage("The User searched with 999 not found!");

          $this->service->getByUser($nonExistentUserId);
     });

     test('getByQuestion must return a UserQuestionAnsered searched by Question id', function () {
          $question = Question::factory()->create();
          UserQuestionAnswered::factory()->create(['question_id' => $question->id]);
          UserQuestionAnswered::factory()->create(['question_id' => $question->id]);

          $result = $this->service->getByQuestion($question->id);

          $this->assertInstanceOf(Collection::class, $result);
          $this->assertCount(2, $result);

          $result->each(function ($userQuestionAnswered) use ($question) {
               $this->assertEquals($question->id, $userQuestionAnswered->question_id);
          });
     });

     test('getByQuestion must throw CustomException::notFound when not found the UserQuestionAnswered By Question id', function () {
          $nonExistentQuestionId = 999;

          $this->expectException(CustomException::class);
          $this->expectExceptionMessage("The Question searched with 999 not found!");

          $this->service->getByQuestion($nonExistentQuestionId);
     });
});
