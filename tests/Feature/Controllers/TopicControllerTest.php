<?php

namespace Tests\Unit;

use App\Services\Topic\TopicService;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Contracts\TopicServiceContract;
use App\Exceptions\TopicExceptions;
use App\Http\Controllers\TopicController;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User\User;

// php artisan test tests/Feature/Controllers/TopicControllerTest.php

describe("Topic Service", function () {
  beforeEach(function(){
    $this->service = new TopicService(); 

    $this->topicData = collect ([
			'data' => [
				'id'         => 1,
				'name'       => 'Topico 1',
				'content_id' => 1,
				'created_at' => '2024-10-17T13:05:18.000000Z',
				'updated_at' => '2024-10-17T13:05:18.000000Z'
			]
    ]);

		$this->user = User::factory()->create([
			'email' => 'test@example.com',
			'password' => Hash::make('old_password'),
			'role' => 'admin'
		]);

		$this->token = $this->user->createToken('access-api', ['admin'])->plainTextToken;
  });



	test('store must create a new topic', function (){
		
		$requestData = [
			'name'       => 'Test topico 1',
			'content_id' => 1
		];

		$topicServiceMock = mock(TopicServiceContract::class)
			->shouldReceive('createTopic')
			->once()
			->with($requestData)
			->andReturn($this->topicData)
			->getMock();
		
		app()->instance(TopicServiceContract::class, $topicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->PostJson("/api/topics/", $requestData);

		
		$response
			->assertStatus(Response::HTTP_CREATED)
			->assertJson(function (AssertableJson $json) {
				$json->where('success', true)
						 ->where('code', Response::HTTP_CREATED)
						 ->has('data');
			});
	});

	test('index must return a paginated list of topics', function (){
		$topicServiceMock = mock(TopicServiceContract::class)
			->shouldReceive('getAllTopics')
			->once()
			->andReturn($this->topicData)
			->getMock();

		app()->instance(TopicServiceContract::class, $topicServiceMock);

		
		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->getJson("/api/topics");

		$response
			->assertStatus(Response::HTTP_OK)
			->assertJson(function (AssertableJson $json) {
				$json->where('success', true)
						 ->where('code', Response::HTTP_OK)
						 ->has('data');
			});
	});

	test('show must return a topic finded by id', function (){
		$topicId = 1;

		$topicServiceMock = mock(TopicServiceContract::class)
			->shouldReceive('findTopicById')
			->once()
			->with(1)
			->andReturn($this->topicData)
			->getMock();

		app()->instance(TopicServiceContract::class, $topicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->getJson("/api/topics/{$topicId}");

		$response
			->assertStatus(Response::HTTP_OK)
			->assertJson(function (AssertableJson $json) {
				$json->where('success', true)
						 ->where('code', Response::HTTP_OK)
						 ->has('data');
			});
	});

	test('show must return not found when topic does not exist', function () {
		$topicId = 999;

		$topicServiceMock = \Mockery::mock(TopicServiceContract::class)
			->shouldReceive('findTopicById')
			->once()
			->with($topicId)
			->andThrow(TopicExceptions::topicNotFound())
			->getMock();

		$this->app->instance(TopicServiceContract::class, $topicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->getJson("/api/topics/{$topicId}");

		$response
			->assertStatus(Response::HTTP_NOT_FOUND)
			->assertJson([
				'message' => 'The topic not found',
			]);
	});

	test('searchTopicByName must return a topic finded by name', function (){
		$topicName = 'topicName';

		$topicServiceMock = mock(TopicServiceContract::class)
			->shouldReceive('searchTopicByName')
			->once()
			->with($topicName)
			->andReturn($this->topicData)
			->getMock();

		$this->app->instance(TopicServiceContract::class, $topicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->getJson("/api/topics/search/{$topicName}");

		$response
			->assertStatus(Response::HTTP_OK)
			->assertJson(function (AssertableJson $json) {
				$json->where('success', true)
						 ->where('code', Response::HTTP_OK)
						 ->has('data');
			});
	});

	test('searchTopicByName must return not found when topic does not exist', function () {
		$topicName = 'topicName';

		$topicServiceMock = \Mockery::mock(TopicServiceContract::class)
			->shouldReceive('searchTopicByName')
			->once()
			->with($topicName)
			->andThrow(TopicExceptions::topicNotFound())
			->getMock();

		$this->app->instance(TopicServiceContract::class, $topicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->getJson("/api/topics/search/{$topicName}");

		$response
			->assertStatus(Response::HTTP_NOT_FOUND)
			->assertJson([
				'message' => 'The topic not found',
			]);
	});

	test('update must update topic successfully', function () {

		$topicId = 1;

		$updatedTopicData = [
			'name'       => 'Test topico 2',
			'content_id' => 1
		];

		$topicServiceMock = \Mockery::mock(TopicServiceContract::class)
			->shouldReceive('updateTopic')
			->once()
			->with($updatedTopicData, $topicId)
			->andReturn()
			->getMock();

		$this->app->instance(TopicServiceContract::class, $topicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
			'Accept' => 'application/json'
		])->putJson("/api/topics/{$topicId}", $updatedTopicData);

		$response->assertStatus(Response::HTTP_NO_CONTENT);
	});

	test('destroy must delete topic successfully', function () {
		$topicId = '1';

		$topicServiceMock = \Mockery::mock(TopicServiceContract::class)
			->shouldReceive('deleteTopic')
			->once()
			->with($topicId)
			->andReturn(true)
			->getMock();

		$this->app->instance(TopicServiceContract::class, $topicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->deleteJson("/api/topics/{$topicId}");
		$response
			->assertStatus(Response::HTTP_NO_CONTENT)
			->assertNoContent();
	});
});