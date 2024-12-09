<?php

use App\Models\Content\Content;
use App\Models\Matter\Matter;
use App\Models\Matter\Topic;
use App\Services\Topic\TopicService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use function PHPUnit\Framework\assertNotNull;

// php artisan test tests/Unit/Services/TopicServiceTest.php

describe("Topic Service", function () {
  beforeEach(function () {
    $this->service = new TopicService();
  });

  afterEach(function () {
    Mockery::close();
  });

  test('create must save and return a topic instance with success', function () {
    $content = Content::factory()->create();

    $data = [
      'name' => 'Sample Topic',
      'content_id' => $content->id,
    ];

    $topic = $this->service->createTopic($data);

    $this->assertDatabaseHas('topics', [
      'name' => 'Sample Topic',
      'content_id' => $content->id,
    ]);

    $this->assertInstanceOf(Topic::class, $topic);
  });

  test('update must update the topic successfully', function () {
    $content = Content::factory()->create();
    $topic = Topic::factory()->create([
      'name' => 'Original Topic Name',
      'content_id' => $content->id,
    ]);

    $updateData = [
      'name' => 'Updated Topic Name',
    ];

    $updatedTopic = $this->service->updateTopic($updateData, $topic->id);

    $this->assertDatabaseHas('topics', [
      'id' => $topic->id,
      'name' => 'Updated Topic Name',
    ]);

    $this->assertInstanceOf(Topic::class, $updatedTopic);
    $this->assertEquals('Updated Topic Name', $updatedTopic->name);
  });


  test('delete must delete a topic with success', function () {
    $content = Content::factory()->create();
    $topic = Topic::factory()->create([
      'name' => 'Sample Topic',
      'content_id' => $content->id,
    ]);

    $response = $this->service->deleteTopic($topic->id);

    $this->assertDatabaseMissing('topics', [
      'id' => $topic->id,
    ]);

    $this->assertEquals(['msg' => 'Topic deleted successfully'], $response);
  });

  test('getAll must return a list of topics with success', function () {
    Topic::factory()->count(15)->create();
    $page = 1;

    $topics = $this->service->getAllTopics($page);

    $this->assertInstanceOf(LengthAwarePaginator::class, $topics);
    $this->assertCount(10, $topics);
    $this->assertEquals(1, $topics->currentPage());
    $this->assertEquals(15, $topics->total());
    $this->assertEquals(2, $topics->lastPage());
  });

  test('getById must return a Topic by id with success', function () {
    $matter = Matter::factory()->create();
    $content = Content::factory()->create(['matter_id' => $matter->id]);
    $topic = Topic::factory()->create(['content_id' => $content->id]);

    $foundTopic = $this->service->findTopicById($topic->id);

    $this->assertInstanceOf(Topic::class, $foundTopic);
    $this->assertTrue($foundTopic->relationLoaded('content'));
    $this->assertTrue($foundTopic->content->relationLoaded('matter'));
    $this->assertEquals($topic->id, $foundTopic->id);
    $this->assertEquals($content->id, $foundTopic->content->id);
    $this->assertEquals($matter->id, $foundTopic->content->matter->id);
  });

  test('searchTopicByName must return a Topic by name with success', function () {
    Topic::factory()->create(['name' => 'Physics Basics']);
    Topic::factory()->create(['name' => 'Advanced Physics']);
    Topic::factory()->create(['name' => 'Mathematics']);

    $result = $this->service->searchTopicByName('Physics');

    $this->assertInstanceOf(Collection::class, $result);
    $this->assertCount(2, $result);

    $result->each(function ($topic) {
      $this->assertStringContainsString('Physics', $topic->name);
    });
  });
});
