<?php
namespace App\Services\Matter;

use App\Contracts\MatterServiceContract;
use App\Exceptions\CustomException;
use App\Exceptions\Matter\MatterServiceExceptions;
use App\Models\Matter\Matter;
use Illuminate\Database\Eloquent\Collection;

class MatterService implements MatterServiceContract
{
    /**
     * @throws MatterServiceExceptions
     */
    public function getAll(): Collection
    {
        try {
            return Matter::all();
        } catch (\Exception $e) {
            throw MatterServiceExceptions::serverError($e->getMessage());
        }
    }

    /**
     * @throws CustomException
     */
    public function getById($matterId)
    {
        try {
            return Matter::findOrFail($matterId);
        } catch (\Exception $e) {
            throw MatterServiceExceptions::matterNotFound("Matter $matterId wasn't found");
        }
    }

    /**
     * @throws CustomException
     */
    public function getByName($matterName){
        try {
            return Matter::where("name", $matterName)->firstOrFail();
        } catch (\Exception $e) {
            throw MatterServiceExceptions::matterNotFound("Matter $matterName wasn't found");
        }
    }

    /**
     * @throws MatterServiceExceptions
     */
    public function create(array $request)
    {
        try {
            return Matter::create($request);
        } catch (\Exception $e) {
            throw MatterServiceExceptions::invalidRequest($e->getMessage());
        }
    }

    /**
     * @throws MatterServiceExceptions
     */
    public function update(array $request, int $matterId)
    {
        try {
            return Matter::findOrFail($matterId)->update($request);
        } catch (\Exception $e) {
            throw MatterServiceExceptions::invalidRequest($e->getMessage());
        }
    }

    /**
     * @throws MatterServiceExceptions
     */
    public function delete(int $matterId): int
    {
        try {
            return Matter::destroy($matterId);
        } catch (\Exception $e) {
            throw MatterServiceExceptions::serverError($e->getMessage());
        }
    }
}
