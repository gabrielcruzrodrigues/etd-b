<?php 
namespace App\Contracts;

interface ContentServiceContract
{
    public function getAll();
    public function getById(int $contentId);
    public function getByName(string $contentName);
    public function create(array $request);
    public function update(array $request, int $contentid);
    public function delete(int $contentId);

}