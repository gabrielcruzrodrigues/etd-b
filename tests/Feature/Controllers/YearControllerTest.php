<?php

use App\Contracts\YearServiceContract;
use App\Enums\ResponseStatus;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;

test('show must return a question finded by id', function () {
     $yearData = [
          "id" => 1,
          "year" =>  "2018",
          "created_at" => "2024-11-11T19:01:17.000000Z",
          "updated_at" => "2024-11-11T19:01:17.000000Z"
     ];

     $yearServiceMock = mock(YearServiceContract::class)
          ->shouldReceive('getById')
          ->once()
          ->with(1)
          ->andReturn($yearData)
          ->getMock();

     app()->instance(YearServiceContract::class, $yearServiceMock);

     $this->withoutMiddleware();
     $response = $this->getJson('/api/years/1');

     $response
          ->assertStatus(200)
          ->assertJson(function (AssertableJson $json) {
               $json->where('status', ResponseStatus::SUCCESS->value)
                    ->has('data');
          });
});
