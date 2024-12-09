<?php

namespace App\Services\Content;

use App\Contracts\ContentServiceContract;
use App\Exceptions\Content\ContentExceptions;
use App\Models\Content\Content;

class ContentService implements ContentServiceContract
{

    public function getAll()
    {
        return Content::all();
    }

    public function getById(int $contentId)
    {
        return ($result = Content::find($contentId)) ? $result : throw ContentExceptions::notFoundContent();
    }


    public function getByName(string $contentName)
    {
        return ($result = Content::where("name", $contentName)->first()) ? $result : throw ContentExceptions::notFoundContent();
    }

    public function create(array $request)
    {
        $this->checkContent($request['name']);
        return Content::create($request);
    }

    private static function checkContent(string $contentName)
    {
        if (!Content::where('name', $contentName)->first()) {
            return; 
        }
        throw ContentExceptions::alreadyExistsContent();
    }


    public function update(array $request, int $contentId)
    {
        $this->checkContent($request['name']);
        return Content::findOrFail($contentId)->update($request);
    }

    public function delete(int $contentId)
    {
        $this->getById($contentId)->delete();
    }
}
