<?php

use App\Contracts\UserQuestionAnnotationServiceContract;
use App\Contracts\UserQuestionAnsweredServiceContract;
use App\Contracts\UserQuestionCommentServiceContract;
use App\Enums\ResponseStatus;
use App\Models\Question\Question;
use App\Models\User\UserQuestionAnnotation;
use App\Models\User\UserQuestionAnswered;
use App\Models\User\UserQuestionComment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;

# php artisan test tests/Feature/Controllers/UserQuestionAnsweredControllerTest.php
describe("UserQuestionAnsweredControllerTest", function () {
     beforeEach(function () {
          $this->user = User::factory()->create([
               'email' => 'test@example.com',
               'password' => Hash::make('old_password'),
               'role' => 'admin'
          ]);

          $this->token = $this->user->createToken('access-api', ['admin'])->plainTextToken;

          $this->matterData = [
               'data' => [
                    [
                         'id' => 1,
                         'name' => 'quimica',
                         'created_at' => '2024-10-17T13:05:18.000000Z',
                         'updated_at' => '2024-10-17T13:05:18.000000Z'
                    ]
               ]
          ];
     });

     afterEach(function () {
          Mockery::close();
     });

     $userQuestionAnsweredData = [
          "data" => [
               [
                    "user_id" => 1,
                    "question_id" => 1,
                    "alternative" => "A",
                    "error_notebook" => "content",
                    "created_at" => "2024-10-23T14:52:55.000000Z",
                    "updated_at" => "2024-10-23T15:36:38.000000Z"
               ]
          ]
     ];

     $userQuestionAnsweredRequestData = [
          "user_id" => 1,
          "question_id" => 1,
          "alternative" => "A",
          "error_notebook" => "content"
     ];

     dataset('userQuestionAnsweredRequest', function () {
          return [
               [
                    [
                         "user_id" => 1,
                         "question_id" => 1,
                         "alternative" => "A",
                         "error_notebook" => "content"
                    ]
               ]
          ];
     });

     $userQuestionCommentData = [
          "data" => [
               [
                    "user_id" => 1,
                    "question_id" => 1,
                    "comment" => "comentário teste",
                    "created_at" => "2024-10-23T14:52:55.000000Z",
                    "updated_at" => "2024-10-23T15:36:38.000000Z"
               ]
          ]
     ];

     $userQuestionCommentRequestData = [
          "user_id" => 1,
          "question_id" => 1,
          "comment" => "comentário teste",
     ];

     dataset('userQuestionCommentRequest', function () {
          return [
               [
                    [
                         "user_id" => 1,
                         "question_id" => 1,
                         "comment" => "comentário teste",
                    ]
               ]
          ];
     });

     $userQuestionAnnotationData = [
          "data" => [
               [
                    "user_id" => 1,
                    "question_id" => 1,
                    "annotation" => "comentário teste",
                    "created_at" => "2024-10-23T14:52:55.000000Z",
                    "updated_at" => "2024-10-23T15:36:38.000000Z"
               ]
          ]
     ];

     $userQuestionAnnotationRequestData = [
          "user_id" => 1,
          "question_id" => 1,
          "annotation" => "comentário teste",
     ];

     dataset('userQuestionAnnotationRequest', function () {
          return [
               [
                    [
                         "user_id" => 1,
                         "question_id" => 1,
                         "annotation" => "comentário teste",
                    ]
               ]
          ];
     });

     test('IndexUserQuestionAnswereds must return a paginated list of userQuestionAnswered', function () use ($userQuestionAnsweredData) {
          $total = count($userQuestionAnsweredData);
          $perPage = 15;
          $currentPage = 1;
          $options = ['path' => url('/path-to-method')];
          $paginator = new LengthAwarePaginator($userQuestionAnsweredData, $total, $perPage, $currentPage, $options);

          $questionServiceMock = mock(UserQuestionAnsweredServiceContract::class)
               ->shouldReceive('getAll')
               ->once()
               ->with(1)
               ->andReturn($paginator)
               ->getMock();

          app()->instance(UserQuestionAnsweredServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->getJson("/api/users/answers?page=1");

          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('status', ResponseStatus::SUCCESS->value)
                         ->has('data', 13)
                         ->where('code', Response::HTTP_OK);
               });
     });

     test('storeUserQuestionAnswered must return a question instance and status code 201', function () use ($userQuestionAnsweredData, $userQuestionAnsweredRequestData) {
          $userQuestionAnswered = new UserQuestionAnswered([
               'user_id' => 1,
               'question_id' => 1,
               'alternative' => 'A',
               'error_notebook' => 'content',
               'created_at' => '2024-10-23T14:52:55.000000Z',
               'updated_at' => '2024-10-23T15:36:38.000000Z'
          ]);

          $questionServiceMock = \Mockery::mock(UserQuestionAnsweredServiceContract::class)
               ->shouldReceive('create')
               ->once()
               ->with($userQuestionAnsweredRequestData)
               ->andReturn($userQuestionAnswered)
               ->getMock();

          $this->app->instance(UserQuestionAnsweredServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->postJson('/api/users/answers', $userQuestionAnsweredRequestData);

          $response
               ->assertStatus(201)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('status', ResponseStatus::SUCCESS->value)
                         ->has('data')
                         ->where('code', Response::HTTP_CREATED);
               });
     });

     test('getUserQuestionAnsweredByUserId must return a userQuestionAnswered list finded by userId', function () use ($userQuestionAnsweredData) {
          $questionServiceMock = mock(UserQuestionAnsweredServiceContract::class)
               ->shouldReceive('getByUser')
               ->once()
               ->with(1)
               ->andReturn(new Collection([
                    $userQuestionAnsweredData
               ]))
               ->getMock();

          $this->app->instance(UserQuestionAnsweredServiceContract::class, $questionServiceMock);


          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->getJson("/api/users/user/1/answers");

          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('status', ResponseStatus::SUCCESS->value)
                         ->has('data', 1)
                         ->where('code', Response::HTTP_OK);
               });
     });

     test('getUserQuestionAnsweredByQuestionId must return a userQuestionAnswered list finded by questionId', function () use ($userQuestionAnsweredData) {
          $questionServiceMock = mock(UserQuestionAnsweredServiceContract::class)
               ->shouldReceive('getByQuestion')
               ->once()
               ->with(1)
               ->andReturn(new Collection([
                    $userQuestionAnsweredData
               ]))
               ->getMock();

          $this->app->instance(UserQuestionAnsweredServiceContract::class, $questionServiceMock);


          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->getJson("/api/users/question/1/answers");

          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('status', ResponseStatus::SUCCESS->value)
                         ->has('data', 1)
                         ->where('code', Response::HTTP_OK);
               });
     });

     test('updateUserQuestionAnswered must update userQuestionAnswered successfully', function ($userQuestionAnsweredRequest) {
          $userId = '1';
          $questionId = '1';

          $userQuestionAnsweredUpdatedData = [
               "user_id" => 1,
               "question_id" => 1,
               "alternative" => "C",
               "error_notebook" => "distraction"
          ];

          $questionServiceMock = \Mockery::mock(UserQuestionAnsweredServiceContract::class)
               ->shouldReceive('update')
               ->once()
               ->with($userQuestionAnsweredRequest, $userId, $questionId)
               ->andReturn($userQuestionAnsweredUpdatedData)
               ->getMock();

          $this->app->instance(UserQuestionAnsweredServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->putJson("/api/users/{$userId}/answers/{$questionId}", $userQuestionAnsweredRequest);

          $response->assertStatus(204);
     })->with('userQuestionAnsweredRequest');

     test('destroyUserQuestionAnswered must delete userQuestionAnswered successfully', function () {
          $questionId = '1';
          $userId = '1';

          $questionServiceMock = \Mockery::mock(UserQuestionAnsweredServiceContract::class)
               ->shouldReceive('delete')
               ->once()
               ->with($userId, $questionId)
               ->andReturn(true)
               ->getMock();

          $this->app->instance(UserQuestionAnsweredServiceContract::class, $questionServiceMock);


          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->deleteJson("/api/users/{$userId}/answers/{$questionId}");

          $response
               ->assertStatus(204)
               ->assertNoContent();
     });

     test('IndexUserQuestionComments must return a paginated list of userQuestionAnnotation', function () use ($userQuestionCommentData) {
          $total = count($userQuestionCommentData);
          $perPage = 15;
          $currentPage = 1;
          $options = ['path' => url('/path-to-method')];
          $paginator = new LengthAwarePaginator($userQuestionCommentData, $total, $perPage, $currentPage, $options);

          $questionServiceMock = mock(UserQuestionCommentServiceContract::class)
               ->shouldReceive('getAll')
               ->once()
               ->with(1, 5)
               ->andReturn($paginator)
               ->getMock();

          app()->instance(UserQuestionCommentServiceContract::class, $questionServiceMock);


          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->getJson('/api/users/comments?page=1&perPage=5');

          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('status', ResponseStatus::SUCCESS->value)
                         ->has('data', 13)
                         ->where('code', Response::HTTP_OK);
               });
     });

     test('storeUserQuestionComment must return a question instance and status code 201', function () use ($userQuestionCommentData, $userQuestionCommentRequestData) {
          $userQuestionComment = new UserQuestionComment([
               'user_id' => 1,
               'question_id' => 1,
               'alternative' => 'A',
               'error_notebook' => 'content',
               'created_at' => '2024-10-23T14:52:55.000000Z',
               'updated_at' => '2024-10-23T15:36:38.000000Z'
          ]);

          $questionServiceMock = \Mockery::mock(UserQuestionCommentServiceContract::class)
               ->shouldReceive('create')
               ->once()
               ->with($userQuestionCommentRequestData)
               ->andReturn($userQuestionComment)
               ->getMock();

          $this->app->instance(UserQuestionCommentServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->postJson('/api/users/comments', $userQuestionCommentRequestData);

          $response
               ->assertStatus(201)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('status', ResponseStatus::SUCCESS->value)
                         ->has('data')
                         ->where('code', Response::HTTP_CREATED);
               });
     });

     test('getUserQuestionCommentByUserId must return a userQuestionComment list finded by userId', function () use ($userQuestionCommentData) {
          $questionServiceMock = mock(UserQuestionCommentServiceContract::class)
               ->shouldReceive('getByUser')
               ->once()
               ->with(1)
               ->andReturn(new Collection([
                    $userQuestionCommentData
               ]))
               ->getMock();

          $this->app->instance(UserQuestionCommentServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->getJson('/api/users/user/1/comments');

          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('status', ResponseStatus::SUCCESS->value)
                         ->has('data', 1)
                         ->where('code', Response::HTTP_OK);
               });
     });

     test('getUserQuestionCommentByQuestionId must return a userQuestionComment list finded by questionId', function () use ($userQuestionCommentData) {
          $questionServiceMock = mock(UserQuestionCommentServiceContract::class)
               ->shouldReceive('getByQuestion')
               ->once()
               ->with(1)
               ->andReturn(new Collection([
                    $userQuestionCommentData
               ]))
               ->getMock();

          $this->app->instance(UserQuestionCommentServiceContract::class, $questionServiceMock);


          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->getJson('/api/users/question/1/comments');

          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('status', ResponseStatus::SUCCESS->value)
                         ->has('data', 1)
                         ->where('code', Response::HTTP_OK);
               });
     });

     test('updateUserQuestionComment must update userQuestionComment successfully', function ($userQuestionCommentRequest) {
          $userQuestionCommentUpdatedData = [
               "user_id" => 1,
               "question_id" => 1,
               "alternative" => "C",
               "error_notebook" => "distraction"
          ];

          $questionServiceMock = \Mockery::mock(UserQuestionCommentServiceContract::class)
               ->shouldReceive('update')
               ->once()
               ->with($userQuestionCommentRequest, 1)
               ->andReturn($userQuestionCommentUpdatedData)
               ->getMock();

          $this->app->instance(UserQuestionCommentServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->putJson("/api/users/comments/1", $userQuestionCommentRequest);

          $response->assertStatus(204);
     })->with('userQuestionCommentRequest');

     test('destroyUserQuestionComment must delete userQuestionComment successfully', function () {
          $questionServiceMock = \Mockery::mock(UserQuestionCommentServiceContract::class)
               ->shouldReceive('delete')
               ->once()
               ->with(1)
               ->andReturn(true)
               ->getMock();

          $this->app->instance(UserQuestionCommentServiceContract::class, $questionServiceMock);

          $this->withoutMiddleware();
          $response = $this->deleteJson("/api/users/comments/1");

          $response
               ->assertStatus(204)
               ->assertNoContent();
     });

     test('IndexUserQuestionAnnotation must return a paginated list of userQuestionAnnotation', function () use ($userQuestionAnnotationData) {
          $total = count($userQuestionAnnotationData);
          $perPage = 15;
          $currentPage = 1;
          $options = ['path' => url('/path-to-method')];
          $paginator = new LengthAwarePaginator($userQuestionAnnotationData, $total, $perPage, $currentPage, $options);

          $questionServiceMock = mock(UserQuestionAnnotationServiceContract::class)
               ->shouldReceive('getAll')
               ->once()
               ->with(1, 5)
               ->andReturn($paginator)
               ->getMock();

          app()->instance(UserQuestionAnnotationServiceContract::class, $questionServiceMock);


          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->getJson('/api/users/annotations?page=1&perPage=5');

          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('status', ResponseStatus::SUCCESS->value)
                         ->has('data', 13);
               });
     });

     test('storeUserQuestionAnnotation must return a question instance and status code 201', function () use ($userQuestionAnnotationRequestData) {
          $userQuestionAnnotation = new UserQuestionAnnotation([
               'user_id' => 1,
               'question_id' => 1,
               'alternative' => 'A',
               'error_notebook' => 'content',
               'created_at' => '2024-10-23T14:52:55.000000Z',
               'updated_at' => '2024-10-23T15:36:38.000000Z'
          ]);

          $questionServiceMock = \Mockery::mock(UserQuestionAnnotationServiceContract::class)
               ->shouldReceive('create')
               ->once()
               ->with($userQuestionAnnotationRequestData)
               ->andReturn($userQuestionAnnotation)
               ->getMock();

          $this->app->instance(UserQuestionAnnotationServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->postJson('/api/users/annotations', $userQuestionAnnotationRequestData);

          $response
               ->assertStatus(201)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('status', ResponseStatus::SUCCESS->value)
                         ->has('data');
               });
     });

     test('getUserQuestionAnnotationByUserId must return a userQuestionAnnotation list finded by userId', function () use ($userQuestionAnnotationData) {
          $userQuestionAnnotationServiceMock = mock(UserQuestionAnnotationServiceContract::class)
               ->shouldReceive('getByUser')
               ->once()
               ->with(1)
               ->andReturn(new Collection([
                    $userQuestionAnnotationData
               ]))
               ->getMock();

          $this->app->instance(UserQuestionAnnotationServiceContract::class, $userQuestionAnnotationServiceMock);


          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->getJson('/api/users/user/1/annotations');
          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('status', ResponseStatus::SUCCESS->value)
                         ->has('data', 1);
               });
     });

     test('getUserQuestionAnnotationByQuestionId must return a userQuestionAnnotation list finded by questionId', function () use ($userQuestionAnnotationData) {
          $userQuestionAnnotationServiceMock = mock(UserQuestionAnnotationServiceContract::class)
               ->shouldReceive('getByQuestion')
               ->once()
               ->with(1)
               ->andReturn(new Collection([
                    $userQuestionAnnotationData
               ]))
               ->getMock();

          $this->app->instance(UserQuestionAnnotationServiceContract::class, $userQuestionAnnotationServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->getJson('/api/users/question/1/annotations');

          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('status', ResponseStatus::SUCCESS->value)
                         ->has('data', 1);
               });
     });

     test('getUserQuestionAnnotationByUserAndQuestionId must return a userQuestionAnnotation list finded by user and question id', function () use ($userQuestionAnnotationData) {
          $question = Question::factory()->create();
          $user = User::factory()->create();
          UserQuestionAnnotation::factory()->count(5)->create([
               'user_id' => $user->id, 'question_id' => $question->id
          ]);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->getJson("/api/users/$user->id/$question->id/annotations");

          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('status', ResponseStatus::SUCCESS->value)
                         ->has('data', 5);
               });
     });

     test('updateUserQuestionAnnotation must update userQuestionAnnotation successfully', function () {
          UserQuestionAnnotation::factory()->create([
               "annotation" => "teste",
               "id" => 1
          ]);

          $userQuestionAnnotationUpdatedData = [
               "annotation" => "teste 2"
          ];

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
          ])->putJson("/api/users/annotations/1", $userQuestionAnnotationUpdatedData);

          $response->assertStatus(204);
     });

     test('destroyUserQuestionAnnotation must delete userQuestionAnnotation successfully', function () {
          $questionServiceMock = \Mockery::mock(UserQuestionAnnotationServiceContract::class)
               ->shouldReceive('delete')
               ->once()
               ->with(1)
               ->andReturn(true)
               ->getMock();

          $this->app->instance(UserQuestionAnnotationServiceContract::class, $questionServiceMock);

          $this->withoutMiddleware();
          $response = $this->deleteJson("/api/users/annotations/1");

          $response
               ->assertStatus(204)
               ->assertNoContent();
     });
});
