<?php 
namespace App\Contracts;

use App\Http\Requests\SubtopicRequest;

interface SubtopicServiceContract
{
    public function getAllSubtopics(int $page);
    public function deleteSubtopic(int $subtopicId);
    public function findSubtopicById(int $subtopicId);
    public function createSubtopic(array $request);
    public function searchSubtopicByName(string $subtopicName);
    public function updateSubtopic(array $request, int $subtopicid);

}