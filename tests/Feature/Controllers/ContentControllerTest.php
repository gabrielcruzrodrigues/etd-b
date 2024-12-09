<?php

use App\Models\User\User;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Services\Content\ContentService;
use Illuminate\Support\Facades\Hash;

# php artisan test tests/Feature/Controllers/ContentControllerTest.php

describe("Content Controller", function () {
    beforeEach(function () {
        $this->modelMock = mock(ContentService::class);
        $this->contentData = collect([
            'data' => [
                [
                    'id' => 1,
                    'name' => 'quimica',
                    "matter_id" => 1,
                    'created_at' => '2024-10-17T13:05:18.000000Z',
                    'updated_at' => '2024-10-17T13:05:18.000000Z'
                ]
            ]
        ]);

        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('old_password'),
            'role' => 'admin'
        ]);

        $this->token = $this->user->createToken('access-api', ['admin'])->plainTextToken;
    });

    afterEach(function () {
        Mockery::close();
    });

    test("getAll must return all contents", function () {
        $this->modelMock
            ->shouldReceive('getAll')
            ->andReturn($this->contentData);

        app()->instance(ContentService::class, $this->modelMock);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/content");

        $this->withoutMiddleware();
        $response = $this->getJson("/api/content");

        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->where('status', "success")
                    ->has('data', 1)
                    ->etc();
            });
    });

    test("getById must return content by id", function () {
        $contentId = 1;
        $this->modelMock->shouldReceive('getById')
            ->with($contentId)
            ->once()
            ->andReturn($this->contentData);

        app()->instance(ContentService::class, $this->modelMock);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/content/{$contentId}");

        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->where('status', "success")
                    ->has('data')
                    ->etc();
            });
    });

    test("getByName must return a content by name", function () {
        $requestData = [
            'name' => 'quimica'
        ];

        $this->modelMock->shouldReceive('getByName')
            ->with($requestData['name'])
            ->once()
            ->andReturn($this->contentData);

        app()->instance(ContentService::class, $this->modelMock);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/content/name", $requestData);

        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->where('status', "success")
                    ->has('data')
                    ->etc();
            });
    });

    test("create must create a new content", function () {
        $requestData = [
            'name' => 'quimica',
            'matter_id' => 1
        ];

        $this->modelMock->shouldReceive('create')
            ->with($requestData)
            ->once()
            ->andReturn($this->contentData);

        app()->instance(ContentService::class, $this->modelMock);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->PostJson("/api/content/", $requestData);


        $response
            ->assertStatus(201)
            ->assertJson(function (AssertableJson $json) {
                $json->where('status', "success")
                    ->where('message', "Content created")
                    ->etc();
            });
    });

    test("update must update a content by ID", function () {
        $contentId = 1;
        $updateData = [
            'name' => 'updated-quimica'
        ];

        $this->modelMock->shouldReceive('update')
            ->with($updateData, $contentId)
            ->once()
            ->andReturn(true);

        app()->instance(ContentService::class, $this->modelMock);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/content/{$contentId}", $updateData);

        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->where('message', "Content updated")
                    ->where('status', "success")
                    ->etc();
            });
    });

    test("destroy must delete a content by ID", function () {
        $contentId = 1;

        $this->modelMock->shouldReceive('delete')
            ->with($contentId)
            ->once()
            ->andReturn(true);

        app()->instance(ContentService::class, $this->modelMock);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/content/{$contentId}");

        $response->assertStatus(204);
    });
});
