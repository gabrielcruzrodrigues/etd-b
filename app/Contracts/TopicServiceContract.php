<?php 
namespace App\Contracts;

use App\Http\Requests\TopicRequest;

interface TopicServiceContract
{
    public function getAllTopics(int $page);
    public function deleteTopic(int $topicId);
    public function findTopicById(int $topicId);
    public function createTopic(array $request);
    public function searchTopicByName(string $topicName);
    public function updateTopic(array $request, int $topicid);

}