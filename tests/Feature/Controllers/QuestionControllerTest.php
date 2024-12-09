<?php


use App\Contracts\QuestionServiceContract;
use App\Enums\Difficulty;
use App\Exceptions\QuestionInventoryException;
use App\Models\Content\Content;
use App\Models\Institution\Institution;
use App\Models\Matter\Matter;
use App\Models\Matter\Subtopic;
use App\Models\Matter\Topic;
use App\Models\Question\Question;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User\User;
use App\Models\Year\Year;
use Illuminate\Support\Facades\Hash;

# php artisan test tests/Feature/Controllers/QuestionControllerTest.php
describe("Question Controller", function () {
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


     $questionData = [
          'data' => [
               [
                    'id' => 1,
                    'institution' => 'TESTE',
                    'code' => 'q65yB',
                    'text' => 'Algum texto',
                    'image' => 'q65yBe3CaZYxc6ah2OIXJi08L.jpg',
                    'query' => 'pergunta',
                    'alternative_a' => 'alternativa',
                    'alternative_b' => 'alternativa',
                    'alternative_c' => 'alternativa',
                    'alternative_d' => 'alternativa',
                    'alternative_e' => 'alternativa',
                    'answer' => 'b',
                    'matter_id' => 1,
                    'content_id' => 1,
                    'topic_id' => 1,
                    'subtopic_id' => 1,
                    'difficulty' => 'easy',
                    'year' => '2022',
                    'state' => 'active',
                    'created_at' => '2024-10-17T13:05:18.000000Z',
                    'updated_at' => '2024-10-17T13:05:18.000000Z'
               ]
          ]
     ];


     test('index must return a paginated list of questions', function () use ($questionData) {
          $questionServiceMock = mock(QuestionServiceContract::class)
               ->shouldReceive('getAll')
               ->once()
               ->with(1, 5)
               ->andReturn($questionData)
               ->getMock();

          app()->instance(QuestionServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
           ])->getJson("/api/questions?page=1&perPage=5");

          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('success', true)
                         ->has('data', 1)
                         ->where('code', Response::HTTP_OK);
               });
     });


     test('show must return a question finded by id', function () use ($questionData) {
          $questionServiceMock = mock(QuestionServiceContract::class)
               ->shouldReceive('getById')
               ->once()
               ->with(1)
               ->andReturn($questionData)
               ->getMock();

          app()->instance(QuestionServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
           ])->getJson("/api/questions/id/1");

          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('success', true)
                         ->has('data', 1)
                         ->where('code', Response::HTTP_OK);
               });
     });


     test('show must return not found when question does not exist', function () {
          $questionId = 999;

          $questionServiceMock = \Mockery::mock(QuestionServiceContract::class)
               ->shouldReceive('getById')
               ->once()
               ->with($questionId)
               ->andThrow(QuestionInventoryException::questionNotFound())
               ->getMock();

          $this->app->instance(QuestionServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
           ])->getJson("/api/questions/id/{$questionId}");

          $response
               ->assertStatus(404)
               ->assertJson([
                    'message' => 'The question not found',
               ]);
     });


     test('getByCode must return a question finded by code', function () use ($questionData) {
          $questionServiceMock = mock(QuestionServiceContract::class)
               ->shouldReceive('getByCode')
               ->once()
               ->with('jjjjj')
               ->andReturn($questionData)
               ->getMock();

          $this->app->instance(QuestionServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
           ])->getJson("/api/questions/code/jjjjj");

          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('success', true)
                         ->has('data')
                         ->where('code', Response::HTTP_OK);
               });
     });


     test('getByCode must return not found when question does not exist', function () {
          $requestCode = 'jjjjj';

          $questionServiceMock = \Mockery::mock(QuestionServiceContract::class)
               ->shouldReceive('getByCode')
               ->once()
               ->with($requestCode)
               ->andThrow(QuestionInventoryException::questionNotFound())
               ->getMock();

          $this->app->instance(QuestionServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
           ])->getJson("/api/questions/code/{$requestCode}");

          $response
               ->assertStatus(404)
               ->assertJson([
                    'message' => 'The question not found',
               ]);
     });


     test('query must return a list of questions by filters', function () use ($questionData) {
          $requestData = [
               'matter' => '1',
               'content' => '1',
               'topic' => '1',
               'subtopic' => '1',
               'year' => '2022',
               'difficulty' => 'easy',
               'institution' => 'UFBA',
               'state' => 'active',
               'page' => 1,
               'perPage' => 5
          ];

          $queryString = http_build_query($requestData);

          $questionServiceMock = \Mockery::mock(QuestionServiceContract::class);
          $questionServiceMock
               ->shouldReceive('query')
               ->once()
               ->withArgs(function ($request, $page, $perPage) use ($requestData) {
                    return $request instanceof \Illuminate\Http\Request
                         && $request->query() == $requestData
                         && $page === 1
                         && $perPage === 5;
               })
               ->andReturn($questionData);

          $this->app->instance(QuestionServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
           ])->getJson('/api/questions/query?' . $queryString);

          $response
               ->assertStatus(200)
               ->assertJson(function (AssertableJson $json) {
                    $json->where('success', true)
                         ->has('data')
                         ->where('code', Response::HTTP_OK);
               });
     });

     test('query must return queryError exception', function () use ($questionData) {
          $requestData = [
               'matter' => '1',
               'content' => '1',
               'topic' => '1',
               'subtopic' => '1',
               'year' => '2022',
               'difficulty' => 'easy',
               'institution' => 'UFBA',
               'state' => 'active',
               'page' => 1,
               'perPage' => 5
          ];

          $queryString = http_build_query($requestData);

          $questionServiceMock = \Mockery::mock(QuestionServiceContract::class);
          $questionServiceMock
               ->shouldReceive('query')
               ->once()
               ->withArgs(function ($request, $page, $perPage) use ($requestData) {
                    return $request instanceof \Illuminate\Http\Request
                         && $request->query() == $requestData
                         && $page === 1
                         && $perPage === 5;
               })
               ->andThrow(QuestionInventoryException::queryError());


          $this->app->instance(QuestionServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
           ])->getJson('/api/questions/query?' . $queryString);

          $response
               ->assertStatus(500)
               ->assertJson([
                    'message' => 'Un erro occurred when tryning execute a query',
               ]);
     });

     test('store must return a question instance and status code 201', function () use ($questionData) {

          $requestData = [
               'institution' => 'TESTE',
               'text' => 'Algum texto',
               'query' => 'pergunta',
               'alternative_a' => 'alternativa',
               'alternative_b' => 'alternativa',
               'alternative_c' => 'alternativa',
               'alternative_d' => 'alternativa',
               'alternative_e' => 'alternativa',
               'answer' => 'b',
               'matter_id' => '1',
               'content_id' => '1',
               'topic_id' => '1',
               'subtopic_id' => '1',
               'difficulty' => 'easy',
               'year' => '2022',
               'state' => 'active'
          ];

          $questionServiceMock = \Mockery::mock(QuestionServiceContract::class)
               ->shouldReceive('create')
               ->once()
               ->with($requestData)
               ->andReturn($questionData)
               ->getMock();

          $this->app->instance(QuestionServiceContract::class, $questionServiceMock);


          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
           ])->postJson('/api/questions', $requestData);

          $response
               ->assertStatus(201)
               ->assertJson([
                    'success' => true,
                    'data' => $questionData,
                    'code' => Response::HTTP_OK
               ]);
     });

     test('script must create and return a question instance and status code 201', function () use ($questionData) {

          $requestData = [
               'institution' => 'TESTE',
               'text' => 'Algum texto',
               'query' => 'pergunta',
               'alternative_a' => 'alternativa',
               'alternative_b' => 'alternativa',
               'alternative_c' => 'alternativa',
               'alternative_d' => 'alternativa',
               'alternative_e' => 'alternativa',
               'answer' => 'b',
               'matter_id' => '1',
               'content_id' => '1',
               'topic_id' => '1',
               'subtopic_id' => '1',
               'difficulty' => 'easy',
               'year' => '2022',
               'state' => 'active'
          ];

          $questionServiceMock = \Mockery::mock(QuestionServiceContract::class)
               ->shouldReceive('createQuestionScript')
               ->once()
               ->with($requestData)
               ->andReturn($questionData)
               ->getMock();

          $this->app->instance(QuestionServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
           ])->postJson('/api/questions/create/script', $requestData);
          

          $response
               ->assertStatus(201)
               ->assertJson([
                    'success' => true,
                    'data' => $questionData,
                    'code' => Response::HTTP_CREATED
               ]);
     });


     test('update must update question successfully', function () {
          $requestData = [
               "institution_id" => 1,
               "query" => "Qual é a capital da França?",
               "alternative_a" => "Londres",
               "alternative_b" => "Berlim",
               "alternative_c" => "Paris",
               "alternative_d" => "Madri",
               "alternative_e" => "Roma",
               "answer" => "C",
               "matter_id" => 1,
               "content_id" => 1,
               "topic_id" => 1,
               "subtopic_id" => 1,
               "difficulty" => "easy",
               "year_id" => 1
          ];

          Matter::factory()->create(['id' => 1]);
          Content::factory()->create(['id' => 1]);
          Topic::factory()->create(['id' => 1]);
          Subtopic::factory()->create(['id' => 1]);
          Institution::factory()->create(['id' => 1]);
          Year::factory()->create(['id' => 1]);

          Question::factory()->create([
               'id' => 1,
               'matter_id' => 1,
               "content_id" => 1,
               "topic_id" => 1,
               "subtopic_id" => 1,
               "year_id" => 1,
               "institution_id" => 1
          ]);
          
          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
           ])->putJson('/api/questions/1', $requestData);

          $response->assertStatus(204);
     });

     test('destroy must delete question successfully', function () {
          $questionId = '1';

          $questionServiceMock = \Mockery::mock(QuestionServiceContract::class)
               ->shouldReceive('delete')
               ->once()
               ->with($questionId)
               ->andReturn(true)
               ->getMock();

          $this->app->instance(QuestionServiceContract::class, $questionServiceMock);

          $response = $this->withHeaders([
               'Authorization' => 'Bearer ' . $this->token,
           ])->deleteJson("/api/questions/{$questionId}");

          $response
               ->assertStatus(204)
               ->assertNoContent();
     });
});
