<?php

namespace Tests\Unit;

use App\Services\Subtopic\SubtopicService;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Contracts\SubtopicServiceContract;
use App\Exceptions\SubtopicExceptions;
use App\Http\Controllers\SubtopicController;
use App\Models\Matter\Subtopic;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use App\Models\User\User;

// php artisan test tests/Feature/Controllers/SubtopicControllerTest.php

describe("Subtopic Service", function () {
  beforeEach(function(){
    
    $this->modelMock = mock(Subtopic::class)->makePartial();
    $this->service = new SubtopicService(); 

    $this->subtopicData = collect ([
			'data' => [
				'id'         => 1,
				'name'       => 'Teste subtopic 1',
				'topic_id' 	 => 1,
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



	test('store must create a new subtopic', function (){
		
		$requestData = [
			'name'     => 'Teste subtopic 1',
			'topic_id' => 1
		];

		$subtopicServiceMock = mock(SubtopicServiceContract::class)
			->shouldReceive('createSubtopic')
			->once()
			->with($requestData)
			->andReturn($this->subtopicData)
			->getMock();
		
		app()->instance(SubtopicServiceContract::class, $subtopicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->PostJson("/api/subtopics/", $requestData);
		
		$response
			->assertStatus(Response::HTTP_CREATED)
			->assertJson(function (AssertableJson $json) {
				$json->where('success', true)
						 ->where('code', Response::HTTP_CREATED)
						 ->has('data');
			});
	});

	test('index must return a paginated list of subtopics', function (){
		$subtopicServiceMock = mock(SubtopicServiceContract::class)
			->shouldReceive('getAllSubtopics')
			->once()
			->andReturn($this->subtopicData)
			->getMock();

		app()->instance(SubtopicServiceContract::class, $subtopicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->getJson("/api/subtopics");

		$response
			->assertStatus(Response::HTTP_OK)
			->assertJson(function (AssertableJson $json) {
				$json->where('success', true)
						 ->where('code', Response::HTTP_OK)
						 ->has('data');
			});
	});

	test('show must return a subtopic finded by id', function (){
		$subtopicId = 1;

		$subtopicServiceMock = mock(SubtopicServiceContract::class)
			->shouldReceive('findSubtopicById')
			->once()
			->with(1)
			->andReturn($this->subtopicData)
			->getMock();

		app()->instance(SubtopicServiceContract::class, $subtopicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->getJson("/api/subtopics/{$subtopicId}");

		$response
			->assertStatus(Response::HTTP_OK)
			->assertJson(function (AssertableJson $json) {
				$json->where('success', true)
						 ->where('code', Response::HTTP_OK)
						 ->has('data');
			});
	});

	test('show must return not found when subtopic does not exist', function () {
		$subtopicId = 999;

		$subtopicServiceMock = \Mockery::mock(SubtopicServiceContract::class)
			->shouldReceive('findSubtopicById')
			->once()
			->with($subtopicId)
			->andThrow(SubtopicExceptions::subtopicNotFound())
			->getMock();

		$this->app->instance(SubtopicServiceContract::class, $subtopicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->getJson("/api/subtopics/{$subtopicId}");

		$response
			->assertStatus(Response::HTTP_NOT_FOUND)
			->assertJson([
					'message' => 'The subtopic not found',
			]);
	});

	test('searchSubtopicByName must return a subtopic finded by name', function (){
		$subtopicName = 'subName';

		$subtopicServiceMock = mock(SubtopicServiceContract::class)
			->shouldReceive('searchSubtopicByName')
			->once()
			->with($subtopicName)
			->andReturn($this->subtopicData)
			->getMock();

		$this->app->instance(SubtopicServiceContract::class, $subtopicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->getJson("/api/subtopics/search/{$subtopicName}");

		$response
			->assertStatus(Response::HTTP_OK)
			->assertJson(function (AssertableJson $json) {
				$json->where('success', true)
						 ->where('code', Response::HTTP_OK)
						 ->has('data');
			});
	});

	test('searchSubtopicByName must return not found when subtopic does not exist', function () {
		$subtopicName = 'jjjjj';

		$subtopicServiceMock = \Mockery::mock(SubtopicServiceContract::class)
			->shouldReceive('searchSubtopicByName')
			->once()
			->with($subtopicName)
			->andThrow(SubtopicExceptions::subtopicNotFound())
			->getMock();

		$this->app->instance(SubtopicServiceContract::class, $subtopicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->getJson("/api/subtopics/search/{$subtopicName}");

		$response
			->assertStatus(Response::HTTP_NOT_FOUND)
			->assertJson([
					'message' => 'The subtopic not found',
			]);
	});

	test('update must update subtopic successfully', function () {

		$subtopicId = 1;

		$updatedSubtopicData = [
			'name'     => 'Test subtopico 2',
			'topic_id' => 1
		];

		$subtopicServiceMock = \Mockery::mock(SubtopicServiceContract::class)
			->shouldReceive('updateSubtopic')
			->once()
			->with($updatedSubtopicData, $subtopicId)
			->andReturn()
			->getMock();

		$this->app->instance(SubtopicServiceContract::class, $subtopicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
			'Accept' => 'application/json'
		])->putJson("/api/subtopics/{$subtopicId}", $updatedSubtopicData);

		$response->assertStatus(Response::HTTP_NO_CONTENT);
	});

	test('destroy must delete subtopic successfully', function () {
		$subtopicId = '1';

		$subtopicServiceMock = \Mockery::mock(SubtopicServiceContract::class)
			->shouldReceive('deleteSubtopic')
			->once()
			->with($subtopicId)
			->andReturn(true)
			->getMock();

		$this->app->instance(SubtopicServiceContract::class, $subtopicServiceMock);

		$response = $this->withHeaders([
			'Authorization' => 'Bearer ' . $this->token,
		])->deleteJson("/api/subtopics/{$subtopicId}");

		$response
			->assertStatus(Response::HTTP_NO_CONTENT)
			->assertNoContent();
	});
});