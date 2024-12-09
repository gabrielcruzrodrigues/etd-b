<?php
namespace App\Contracts;

use App\Models\User\UserQuestionAnnotation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserQuestionAnnotationServiceContract 
{
     public function create(array $data) : UserQuestionAnnotation;
     public function update(array $data, int $id) : void;
     public function delete(int $id) : void;
     public function getAll(int $page, int $perPage) : LengthAwarePaginator;
     public function getByUser(int $userId) : Collection;
     public function getByQuestion(int $questionId) : Collection;
     public function getByUserAndQuestionId(int $userId, int $questionId) : Collection;
}