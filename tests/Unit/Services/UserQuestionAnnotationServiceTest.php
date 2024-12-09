<?php

use App\Exceptions\CustomException;
use App\Exceptions\QuestionInventoryException;
use App\Models\Question\Question;
use App\Models\User\User;
use App\Models\User\UserQuestionAnnotation;
use App\Services\User\UserQuestionAnnotationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

use function PHPUnit\Framework\assertTrue;

# php artisan test tests/Unit/Services/UserQuestionAnnotationServiceTest.php

describe("UserQuestionAnnotation Service", function () {
     beforeEach(function () {
          $this->service = new UserQuestionAnnotationService();
     });

     afterEach(function () {
          Mockery::close();
     });

     test('create must save and return a userQuestionAnnotation instance with success', function () {
          $user = User::factory()->create();
          $question = Question::factory()->create();

          $data = [
               'user_id' => $user->id,
               'question_id' => $question->id,
               'annotation' => 'teste'
          ];

          $userQuestionAnswered = $this->service->create($data);

          $this->assertDatabaseHas('user_question_annotations', [
               'user_id' => $data['user_id'],
               'question_id' => $data['question_id'],
               'annotation' => $data['annotation'],
          ]);

          $this->assertInstanceOf(UserQuestionAnnotation::class, $userQuestionAnswered);
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
          $this->expectExceptionMessage("The question not found");
          $this->expectExceptionCode(404);

          $this->service->create($this->userQuestionAnnotationRequestData);
     });

     test('update must update the userQuestionAnnotation successfuly', function () {
          $user = User::factory()->create();
          $question = Question::factory()->create();

          UserQuestionAnnotation::factory()->create([
               'user_id' => $user->id,
               'question_id' => $question->id,
               'annotation' => 'teste',
          ]);

          $updateData = ['annotation' => 'teste_2'];

          $this->service->update($updateData, $user->id, $question->id);

          $this->assertDatabaseHas('user_question_annotations', [
               'user_id' => $user->id,
               'question_id' => $question->id,
               'annotation' => 'teste_2',
          ]);
     });

     test('update must throw CustomException::notFound() when not found the userQuestionAnnotation', function () {
          $nonExistentUserId = 999;
          $nonExistentQuestionId = 999;

          $updateData = ['notes' => 'New notes'];

          $this->expectException(QuestionInventoryException::class);
          $this->expectExceptionMessage("The question not found");

          $this->service->update($updateData, $nonExistentUserId, $nonExistentQuestionId);
     });

     test('update must throw CustomException::noChangesDetectedForUpdate when the object return false in isDirty method', function () {
          $user = User::factory()->create();
          $question = Question::factory()->create();
          UserQuestionAnnotation::factory()->create([
               'user_id' => $user->id,
               'question_id' => $question->id,
               'annotation' => 'Initial notes',
          ]);

          $updateData = ['annotation' => 'Initial notes'];

          $this->expectException(CustomException::class);
          $this->expectExceptionMessage("changes not detected on entity for update");

          $this->service->update($updateData, $user->id, $question->id);
     });

     test('delete must delete a UserQuestionAnnotation successfuly', function () {
          UserQuestionAnnotation::factory()->create([
               'id' => 1
          ]);

          $this->service->delete(1);

          $this->assertDatabaseMissing('user_question_annotations', [
               'id' => 1
          ]);
     });

     test('delete must throw CustomException::NotFound() when not found the UserquestionAnnotation', function () {
          $nonExistentUserId = 999;
          $nonExistentQuestionId = 999;

          $this->expectException(QuestionInventoryException::class);
          $this->expectExceptionMessage("The question not found");

          $this->service->delete($nonExistentUserId, $nonExistentQuestionId);
     });

     test('getAll must return a LengthAwarePaginator with successfuly', function () {
          UserQuestionAnnotation::factory()->count(10)->create();

          $page = 1;
          $perPage = 5;

          $result = $this->service->getAll($page, $perPage);

          $this->assertInstanceOf(LengthAwarePaginator::class, $result);
          $this->assertCount($perPage, $result->items());
          $this->assertEquals(10, $result->total());
          $this->assertEquals($page, $result->currentPage());
          $this->assertEquals(2, $result->lastPage());
     });

     test('getByUser must return a UserQuestionAnnotation searched by User id', function () {
          $user = User::factory()->create();
          UserQuestionAnnotation::factory()->count(3)->create(['user_id' => $user->id]);

          $result = $this->service->getByUser($user->id);

          $this->assertInstanceOf(Collection::class, $result);
          $this->assertCount(3, $result);

          $result->each(function ($annotation) use ($user) {
               $this->assertEquals($user->id, $annotation->user_id);
          });
     });

     test('getByUser must throw CustomException::notFound when not found the UserQuestionAnnotation By User id', function () {
          $nonExistentUserId = 999;

          $this->expectException(QuestionInventoryException::class);
          $this->expectExceptionMessage("The question not found");

          $this->service->getByUser($nonExistentUserId);
     });

     test('getByQuestion must return a UserQuestionAnnotation searched by Question id', function () {
          $question = Question::factory()->create();
          UserQuestionAnnotation::factory()->count(3)->create(['question_id' => $question->id]);

          $result = $this->service->getByQuestion($question->id);

          $this->assertInstanceOf(Collection::class, $result);
          $this->assertCount(3, $result);

          $result->each(function ($annotation) use ($question) {
               $this->assertEquals($question->id, $annotation->question_id);
          });
     });

     test('getByQuestion must throw CustomException::notFound when not found the UserQuestionAnnotation By Question id', function () {
          $nonExistentQuestionId = 999;

          $this->expectException(QuestionInventoryException::class);
          $this->expectExceptionMessage("The question not found");

          $this->service->getByQuestion($nonExistentQuestionId);
     });

     test('getByUserAndQuestionId must return a UserQuestionAnnotation searched by User and Question id', function () {
          $question = Question::factory()->create();
          $user = User::factory()->create();
          UserQuestionAnnotation::factory()->count(3)->create([
               'question_id' => $question->id, 'user_id' => $user->id
          ]);

          $result = $this->service->getByUserAndQuestionId($question->id, $user->id);

          $this->assertInstanceOf(Collection::class, $result);
          $this->assertCount(3, $result);

          $result->each(function ($annotation) use ($question, $user) {
               $this->assertEquals($question->id, $annotation->question_id, $user->id);
          });
     });

     test('getByUserAndQuestionId must throw CustomException::notFound when not found the UserQuestionAnnotation By User and Question id', function () {
          $nonExistentQuestionId = 999;
          $user = User::factory()->create(['id' => 1]);

          $this->expectException(CustomException::class);
          $this->expectExceptionMessage("The User | Question searched with 999 | 1 not found!");

          $this->service->getByUserAndQuestionId($nonExistentQuestionId, $user->id);
     });
});
