<?php

namespace App\Services\Topic;

use App\Contracts\TopicServiceContract;
use App\Exceptions\TopicExceptions;
use App\Models\Content\Content;
use App\Models\Matter\Topic;

class TopicService implements TopicServiceContract
{
  public function createTopic(array $data)
  {
    $content = Content::find($data['content_id']);

    if (!$content) {
      throw TopicExceptions::topicNotFound(); 
    }

    $topic = $content->topics()->create($data);

    if (!$topic) {
      throw TopicExceptions::createTopicError(); 
    }

    return $topic;
  }

  public function findTopicById(int $topicId)
  {
    $topic = Topic::with('content.matter')->find($topicId);

    if (!$topic) {
      throw TopicExceptions::topicNotFound();
    }

    return $topic;
  }

  public function updateTopic(array $data, int $topicId)
  {
    $topic = Topic::find($topicId);

    if (!$topic) {
      throw TopicExceptions::topicNotFound();
    }

    $topic->update($data);

    return $topic;
  }

  public function deleteTopic(int $topicId)
  {
    $topic = Topic::find($topicId);

    if (!$topic) {
      throw TopicExceptions::topicNotFound();
    }

    $topic->delete();
    return ['msg' => 'Topic deleted successfully'];
  }

  public function getAllTopics(int $page)
  {
    return Topic::paginate(10, ['*'], 'page', $page);
  }

  public function searchTopicByName(string $name)
  {
    $topics = Topic::where('name', 'LIKE', "%" . $name . "%")->get();

    if ($topics->isEmpty()) {
      throw TopicExceptions::topicNotFound();
    }

    return $topics;
  }
}
