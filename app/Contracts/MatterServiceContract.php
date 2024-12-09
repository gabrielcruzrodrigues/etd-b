<?php 
namespace App\Contracts;

interface MatterServiceContract
{
    public function getAll();
    public function getById(int $matterId);
    public function getByName(string $matterName);
    public function create(array $request);
    public function update(array $request, int $matterid);
    public function delete(int $matterId);

}