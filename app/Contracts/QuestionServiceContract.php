<?php 
namespace App\Contracts;

use App\Http\Requests\Question\QuestionFormRequest;

interface QuestionServiceContract 
{
     public function create(array $data);
     public function update(array $data, int $questionId);
     public function delete(int $questionId);
     public function getAll(int $page, int $perPage = 15);
     public function getById(int $questionId);
     public function getByCode(string $code);
     public function query(object $fields, int $page, int $perPage);
     public function createQuestionScript(array $data);
     public function getAllFilters();
}