<?php

use App\Models\Matter\Matter;
use App\Services\Matter\MatterService;
use App\Exceptions\Matter\MatterServiceExceptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use function PHPUnit\Framework\assertNotNull;

describe("Matter Service", function () {
     beforeEach(function () {
          $this->service = new MatterService();
     });

     afterEach(function () {
          Mockery::close();
     });

     test("getAll must return a collection of matter", function () {
          Matter::factory()->count(3)->create();

          $result = $this->service->getAll();

          $this->assertInstanceOf(Collection::class, $result);
          $this->assertCount(3, $result);
     });

     test("getByName must return matter by name", function () {
          $matterName = 'Physics';
          $matter = Matter::factory()->create(['name' => $matterName]);

          $result = $this->service->getByName($matterName);

          $this->assertEquals($matter->id, $result->id);
          $this->assertEquals($matterName, $result->name);
     });

     test("GetById must return matter by id", function () {
          $matter = Matter::factory()->create();

          $result = $this->service->getById($matter->id);

          $this->assertEquals($matter->id, $result->id);
          $this->assertEquals($matter->name, $result->name);
     });

     test("create must create a new matter", function () {
          $data = ['name' => 'Physics'];

          $result = $this->service->create($data);

          $this->assertDatabaseHas('matters', [
               'name' => 'Physics',
          ]);
          $this->assertEquals('Physics', $result->name);
     });

     test("update method must update a matter", function () {
          $matter = Matter::factory()->create(['name' => 'Physics']);

          $updateData = ['name' => 'Advanced Physics'];

          $result = $this->service->update($updateData, $matter->id);

          $this->assertTrue($result);
          $this->assertDatabaseHas('matters', [
               'id' => $matter->id,
               'name' => 'Advanced Physics',
          ]);
     });

     test('delete must delete a matter by its id', function () {
          $matter = Matter::factory()->create();

          $result = $this->service->delete($matter->id);

          $this->assertEquals(1, $result);
          $this->assertDatabaseMissing('matters', [
               'id' => $matter->id,
          ]);
     });
});

describe("Matter Service Exceptions", function () {
     beforeEach(function () {
          $this->service = new MatterService();
     });

     afterEach(function () {
          Mockery::close();
     });

     test("getById must throw notFound exception if matter does not exist", function () {
          $nonExistentMatterId = 999;

          $this->expectException(MatterServiceExceptions::class);
          $this->expectExceptionMessage("Matter $nonExistentMatterId wasn't found");

          $this->service->getById($nonExistentMatterId);
     });

     test("getByName must throw notFound exception if matter does not exist", function () {
          $nonExistentMatterName = "teste";

          $this->expectException(MatterServiceExceptions::class);
          $this->expectExceptionMessage("Matter $nonExistentMatterName wasn't found");

          $this->service->getByName($nonExistentMatterName);
     });
});
