<?php 
namespace App\Contracts;

use App\Models\User\UserQuestionAnswered;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserQuestionAnsweredServiceContract 
{
     public function create(array $data) : UserQuestionAnswered;
     public function update(array $data, int $userId, int $questionId) : void;
     public function delete(int $userId, int $questionId) : void;
     public function getAll(int $page) : LengthAwarePaginator;
     public function getByUser(int $userId) : Collection;
     public function getByQuestion(int $questionId) : Collection;
}