<?php

use App\Contracts\MatterServiceContract;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\User\User;
use Illuminate\Support\Facades\Hash;

describe("Matter Controller", function () {
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

    test('index must return a list of matters', function () {
        $matterServiceMock = mock(MatterServiceContract::class)
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($this->matterData)
            ->getMock();

        app()->instance(MatterServiceContract::class, $matterServiceMock);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/matter");

        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->where('status', 'success')
                    ->where('code', 200)
                    ->has('data', 1)
                    ->etc();
            });
    });

    test('store must create a new matter', function () {
        $matterData = [
            'id' => 1,
            'name' => 'quimica',
            'created_at' => '2024-10-17T13:05:18.000000Z',
            'updated_at' => '2024-10-17T13:05:18.000000Z'
        ];

        $requestData = [
            'name' => 'quimica'
        ];

        $matterServiceMock = mock(MatterServiceContract::class)
            ->shouldReceive('create')
            ->with($requestData)
            ->once()
            ->andReturn($matterData)
            ->getMock();

        app()->instance(MatterServiceContract::class, $matterServiceMock);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/matter/", $requestData);

        $response
            ->assertStatus(201)
            ->assertJson(function (AssertableJson $json) {
                $json->where('status', 'success')
                    ->where('code', 201)
                    ->has('data')
                    ->etc();
            });
    });

    test('show must return a matter by ID', function () {
        $matterId = 1;
        $matterData = [
            'id' => $matterId,
            'name' => 'quimica',
            'created_at' => '2024-10-17T13:05:18.000000Z',
            'updated_at' => '2024-10-17T13:05:18.000000Z'
        ];

        $matterServiceMock = mock(MatterServiceContract::class)
            ->shouldReceive('getById')
            ->with($matterId)
            ->once()
            ->andReturn($matterData)
            ->getMock();

        app()->instance(MatterServiceContract::class, $matterServiceMock);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/matter/{$matterId}");

        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->where('status', 'success')
                    ->where('code', 200)
                    ->has('data')
                    ->etc();
            });
    });

    test('getByName must return a matter by name', function () {
        $matterName = 'quimica';

        $matterServiceMock = mock(MatterServiceContract::class)
            ->shouldReceive('getByName')
            ->with($matterName)
            ->once()
            ->andReturn($this->matterData['data'][0])
            ->getMock();

        app()->instance(MatterServiceContract::class, $matterServiceMock);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/matter/name", ['name' => $matterName]);

        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($matterName) {
                $json->where('status', 'success')
                    ->where('data.name', $matterName)
                    ->where('code', 200)
                    ->etc();
            });
    });

    test('update must update a matter by ID', function () {
        $matterId = 1;
        $updateData = [
            'name' => 'updated-quimica'
        ];

        $matterServiceMock = mock(MatterServiceContract::class)
            ->shouldReceive('update')
            ->with($updateData, $matterId)
            ->once()
            ->andReturn(true)
            ->getMock();

        app()->instance(MatterServiceContract::class, $matterServiceMock);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/matter/{$matterId}", $updateData);

        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($updateData, $matterId) {
                $json->where('message', "Matter of id: $matterId updated, name changed to {$updateData['name']}")
                    ->where('code', 200)
                    ->etc();
            });
    });

    test('destroy must delete a matter by ID', function () {
        $matterId = 1;

        $matterServiceMock = mock(MatterServiceContract::class)
            ->shouldReceive('delete')
            ->with($matterId)
            ->once()
            ->andReturn(true)
            ->getMock();

        app()->instance(MatterServiceContract::class, $matterServiceMock);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/matter/{$matterId}");

        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($matterId) {
                $json->where('message', "Matter of id: $matterId deleted")
                    ->where('status', 'success')
                    ->where('code', 204)
                    ->etc();
            });
    });
});
