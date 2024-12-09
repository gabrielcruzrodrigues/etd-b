<?php

use App\Exceptions\CustomException;
use App\Models\Matter\Matter;
use App\Models\Question\Question;
use App\Models\User\User;
use App\Models\User\UserQuestionComment;
use App\Services\User\UserQuestionCommentService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use function PHPUnit\Framework\assertInstanceOf;

# php artisan test tests/Unit/Services/UserQuestionCommentServiceTest.php

describe("UserQuestionComment Service", function () {
     beforeEach(function () {
          $this->service = new UserQuestionCommentService();
     });

     afterEach(function () {
          Mockery::close();
     });

     test('create must save and return a userQuestionComment instance with success', function () {
          $user = User::factory()->create();
          $question = Question::factory()->create();

          $requestData = [
               'user_id' => $user->id,
               'question_id' => $question->id,
               'comment' => 'comentário teste',
          ];

          $response = $this->service->create($requestData);

          assertInstanceOf(UserQuestionComment::class, $response);
          $this->assertDatabaseHas('user_question_comments', [
               'user_id' => $user->id,
               'question_id' => $question->id,
               'comment' => 'comentário teste',
          ]);
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

     test('update must update the userQuestionComment successfuly', function () {
          $userQuestionComment = UserQuestionComment::factory()->create();
          $newData = ['comment' => 'Updated content'];

          $this->service->update($newData, $userQuestionComment->id);

          $this->assertDatabaseHas('user_question_comments', [
               'id' => $userQuestionComment->id,
               'comment' => 'Updated content'
          ]);
     });

     test('update must throw CustomException::notFound() when not found the userQuestionComment', function () {
          $newData = ['comment' => 'Updated content'];

          $this->expectException(CustomException::class);
          $this->expectExceptionMessage("The UserQuestionComments searched with 1 not found!");
          $this->expectExceptionCode(404);

          $this->service->update($newData, 1);
     });

     test('delete must delete a UserQuestionComment successfuly', function () {
          UserQuestionComment::factory()->create([
               'id' => 1
          ]);

          $this->service->delete(1);

          $this->assertDatabaseMissing('user_question_comments', [
               'id' => 1
          ]);
     });

     test('delete must throw CustomException::NotFound() when not found the UserquestionComment', function () {
          $this->expectException(CustomException::class);
          $this->expectExceptionMessage("The UserQuestionComment searched with 1 not found!");
          $this->expectExceptionCode(404);

          $this->service->delete(1);
     });

     test('getAll must return a LengthAwarePaginator with successfuly', function () {
          UserQuestionComment::factory()->count(10)->create();

          $page = 1;
          $perPage = 5;

          $result = $this->service->getAll($page, $perPage);

          $this->assertInstanceOf(LengthAwarePaginator::class, $result);
          $this->assertCount($perPage, $result->items());
          $this->assertEquals(10, $result->total());
          $this->assertEquals($page, $result->currentPage());
          $this->assertEquals(2, $result->lastPage());
     });

     test('teste matter', function () {
          $matter = Matter::factory()->create();

          $this->assertNotNull($matter);
     });

     test('getByUser must return a UserQuestionComment searched by User id', function () {
          $user = User::factory()->create();
          $question = Question::factory()->create();

          UserQuestionComment::factory()->count(3)->create([
               'user_id' => $user->id,
               'question_id' => $question->id
          ]);

          $result = $this->service->getByUser($user->id);

          $this->assertInstanceOf(Collection::class, $result);
          $this->assertCount(3, $result);
          foreach ($result as $comment) {
               $this->assertEquals($user->id, $comment->user_id);
          }
     });

     test('getByUser must throw CustomException::notFound when not found the UserQuestionComment By User id', function () {
          $user = User::factory()->create([
               'id' => 1
          ]);
          $question = Question::factory()->create();

          UserQuestionComment::factory()->count(3)->create([
               'user_id' => $user->id,
               'question_id' => $question->id
          ]);

          $this->expectException(CustomException::class);
          $this->expectExceptionMessage("The UserQuestionComments - user_id searched with 99 not found!");
          $this->expectExceptionCode(404);

          $result = $this->service->getByUser(99);
     });

     test('getByQuestion must return a UserQuestionComment searched by Question id', function () {
          $user = User::factory()->create();
          $question = Question::factory()->create();

          UserQuestionComment::factory()->count(3)->create([
               'user_id' => $user->id,
               'question_id' => $question->id
          ]);

          $result = $this->service->getByQuestion($question->id);

          $this->assertInstanceOf(Collection::class, $result);
          $this->assertCount(3, $result);
          foreach ($result as $comment) {
               $this->assertEquals($user->id, $comment->user_id);
          }
     });

     test('getByQuestion must throw CustomException::notFound when not found the UserQuestionComment By Question id', function () {
          $user = User::factory()->create([
               'id' => 1
          ]);
          $question = Question::factory()->create([
               'id' => 1
          ]);

          UserQuestionComment::factory()->count(3)->create([
               'user_id' => $user->id,
               'question_id' => $question->id
          ]);

          $this->expectException(CustomException::class);
          $this->expectExceptionMessage("The UserQuestionComments - question_id searched with 99 not found!");
          $this->expectExceptionCode(404);

          $result = $this->service->getByQuestion(99);
     });
});
