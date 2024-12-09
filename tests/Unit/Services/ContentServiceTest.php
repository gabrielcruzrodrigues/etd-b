<?php

use App\Models\Content\Content;
use App\Services\Content\ContentService;
use App\Exceptions\Content\ContentExceptions;
use App\Models\Matter\Matter;
use Illuminate\Database\Eloquent\Collection;

use function PHPUnit\Framework\assertNotNull;

describe("Content Service", function () {
    beforeEach(function () {
        $this->service = new ContentService();
    });

    afterEach(function () {
        Mockery::close();
    });

    test("getAll must return collection of content", function () {
        Content::factory()->count(3)->create();

        $result = $this->service->getAll();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    });

    test("getById must return content by id", function () {
        $content = Content::factory()->create();

        $result = $this->service->getById($content->id);

        $this->assertEquals($content->id, $result->id);
        $this->assertEquals($content->name, $result->name);
    });

    test("getByName must return content by name", function () {
        $contentName = 'Physics';
        $content = Content::factory()->create(['name' => $contentName]);

        $result = $this->service->getByName($contentName);

        $this->assertEquals($content->id, $result->id);
        $this->assertEquals($contentName, $result->name);
    });

    test('create must create a new content', function () {
        $matter = Matter::factory()->create();
        $data = ['name' => 'Physics', 'matter_id' => $matter->id];

        $result = $this->service->create($data);

        $this->assertDatabaseHas('contents', [
            'name' => 'Physics',
        ]);
        $this->assertEquals('Physics', $result->name);
    });

    test("update method must update a content", function () {
        $content = Content::factory()->create(['name' => 'Physics']);

        $updateData = ['name' => 'Advanced Physics'];

        $result = $this->service->update($updateData, $content->id);

        $this->assertTrue($result);
        $this->assertDatabaseHas('contents', [
            'id' => $content->id,
            'name' => 'Advanced Physics',
        ]);
    });

    test("delete method must delete a content by id", function () {
        $content = Content::factory()->create();

        $result = $this->service->delete($content->id);

        $this->assertDatabaseMissing('contents', [
            'id' => $content->id,
        ]);
    });
});

describe("Content Service Exceptions", function () {
    beforeEach(function () {
        $this->service = new ContentService();
    });

    afterEach(function () {
        Mockery::close();
    });

    test('getById must throw exception if content is not found', function () {
        $nonExistentMatterId = 999;

        $this->expectException(ContentExceptions::class);
        $this->expectExceptionMessage("Content not found");

        $this->service->getById($nonExistentMatterId);
    });

    test('getByName must throw exception if content is not found', function () {
        $nonExistentMatterName = "teste";

        $this->expectException(ContentExceptions::class);
        $this->expectExceptionMessage("Content not found");

        $this->service->getByName($nonExistentMatterName);
    });

    test('create must throw exception if content already exists', function () {
        $matter = Matter::factory()->create();

        Content::factory()->create([
            'name' => 'quimica',
            'matter_id' => $matter->id,
        ]);

        $this->expectException(ContentExceptions::class);
        $this->expectExceptionMessage('Content with this name already exists.');

        $this->service->create(['name' => 'quimica', 'matter_id' => $matter->id]);
    });

    test("update method must throw exception if content with same name already exists", function () {
        $matter = Matter::factory()->create();

        Content::factory()->create([
            'name' => 'quimica',
            'matter_id' => $matter->id,
        ]);

        $contentToUpdate = Content::factory()->create([
            'name' => 'biologia',
            'matter_id' => $matter->id,
        ]);

        $this->expectException(ContentExceptions::class);
        $this->expectExceptionMessage('Content with this name already exists.');

        $this->service->update(['name' => 'quimica'], $contentToUpdate->id);
    });


    test("delete method must throw exception if content not found", function () {
        $nonExistentContentId = 999;

        $this->expectException(ContentExceptions::class);
        $this->expectExceptionMessage('Content not found');

        $this->service->delete($nonExistentContentId);
    });
});
