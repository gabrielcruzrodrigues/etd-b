<?php

namespace App\Services\Subtopic; 

use App\Contracts\SubtopicServiceContract;
use App\Models\Matter\Subtopic;
use App\Models\Matter\Topic;
use App\Exceptions\SubtopicExceptions;

class SubtopicService implements SubtopicServiceContract
{   
    public function createSubtopic(array $data)
    {
      if(!Topic::find($data['topic_id'])){
        throw SubtopicExceptions::subtopicNotFound();
      }
      
      try {
          $subtopic = Subtopic::create($data);
      } catch (\Exception $e) {
          throw SubtopicExceptions::createSubtopicError();
      }

      return $subtopic;
    }

    public function findSubtopicById(int $subtopicId)
    {
      $subtopic = Subtopic::with('topic.content')->find($subtopicId);

      if (!$subtopic) {
        throw SubtopicExceptions::topicNotFound();
      }

      return $subtopic;
    }

    public function updateSubtopic(array $data, int $subtopicId)
    {
      $subtopic = Subtopic::find($subtopicId);
      
        if (!$subtopic) {
          throw SubtopicExceptions::subtopicNotFound();
        }

      $subtopic->update($data);

      return $subtopic;
    }

    public function deleteSubtopic(int $subtopicId)
  {
    $subtopic = Subtopic::find($subtopicId);
    
    if (!$subtopic) {
      throw SubtopicExceptions::subtopicNotFound();
    }

    $subtopic->delete();
    return ['msg' => 'Subtopic deleted successfully'];
  }

  public function getAllSubtopics(int $page)
  {
    return Subtopic::paginate(10, ['*'], 'page', $page); 
  }

  public function searchSubtopicByName(string $name)
  {
    $subtopic = Subtopic::where('name', 'LIKE', "%" . $name . "%")->get();

    if ($subtopic->isEmpty()) {
      throw SubtopicExceptions::topicNotFound();
    }

    return $subtopic;
  }
}