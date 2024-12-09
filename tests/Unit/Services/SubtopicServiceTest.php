<?php

use App\Models\Content\Content;
use App\Models\Matter\Matter;
use App\Models\Matter\Subtopic;
use App\Services\Subtopic\SubtopicService;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Matter\Topic;
use Illuminate\Database\Eloquent\Collection;

use function PHPUnit\Framework\assertNotNull;

// php artisan test tests/Unit/Services/SubtopicServiceTest.php

describe("Subtopic Service", function () {
  beforeEach(function () {
    $this->service = new SubtopicService();
  });

  afterEach(function () {
    Mockery::close();
  });

  test('create must save and return a subtopic instance with success', function () {

    $topic = Topic::factory()->create();

    $data = [
      'name' => 'Sample Topic',
      'topic_id' => $topic->id,
    ];

    $topic = $this->service->createSubtopic($data);

    $this->assertDatabaseHas('subtopics', [
      'name' => 'Sample Topic',
      'topic_id' => $topic->id,
    ]);

    $this->assertInstanceOf(Subtopic::class, $topic);
  });

  test('update must update the subtopic successfully', function () {
    $topic = Topic::factory()->create();
    $subtopic = Subtopic::factory()->create([
      'name' => 'Original Topic Name',
      'topic_id' => $topic->id,
    ]);

    $updateData = [
      'name' => 'Updated subtopic Name',
    ];

    $updatedTopic = $this->service->updateSubtopic($updateData, $subtopic->id);

    $this->assertDatabaseHas('subtopics', [
      'id' => $subtopic->id,
      'name' => 'Updated subtopic Name',
    ]);

    $this->assertInstanceOf(Subtopic::class, $updatedTopic);
    $this->assertEquals('Updated subtopic Name', $updatedTopic->name);
  });

  test('delete must delete a subtopic with success', function () {
    $topic = Topic::factory()->create();
    $subtopic = Subtopic::factory()->create([
      'name' => 'Sample subtopic',
      'topic_id' => $topic->id,
    ]);

    $response = $this->service->deleteSubtopic($subtopic->id);

    $this->assertDatabaseMissing('subtopics', [
      'id' => $subtopic->id,
    ]);

    $this->assertEquals(['msg' => 'Subtopic deleted successfully'], $response);
  });

  test('getAll must return a list of subtopics with success', function () {
    Subtopic::factory()->count(15)->create();
    $page = 1;

    $subtopics = $this->service->getAllSubtopics($page);

    $this->assertInstanceOf(LengthAwarePaginator::class, $subtopics);
    $this->assertCount(10, $subtopics);
    $this->assertEquals(1, $subtopics->currentPage());
    $this->assertEquals(15, $subtopics->total());
    $this->assertEquals(2, $subtopics->lastPage());
  });

  test('getById must return a Subtopic by id with success', function () {
    $matter = Matter::factory()->create();
    $content = Content::factory()->create(['matter_id' => $matter->id]);
    $topic = Topic::factory()->create(['content_id' => $content->id]);
    $subtopic = Subtopic::factory()->create(['topic_id' => $topic->id]);

    $foundTopic = $this->service->findSubtopicById($subtopic->id);

    $this->assertInstanceOf(Subtopic::class, $foundTopic);
    $this->assertEquals($subtopic->id, $foundTopic->id);
  });

  test('searchSubtopicByName must return a Subtopic by name with success', function () {
    Subtopic::factory()->create(['name' => 'Physics Basics']);
    Subtopic::factory()->create(['name' => 'Advanced Physics']);
    Subtopic::factory()->create(['name' => 'Mathematics']);

    $result = $this->service->searchSubtopicByName('Physics');

    $this->assertInstanceOf(Collection::class, $result);
    $this->assertCount(2, $result);

    $result->each(function ($subtopic) {
      $this->assertStringContainsString('Physics', $subtopic->name);
    });
  });
});
