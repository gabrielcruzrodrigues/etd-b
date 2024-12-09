<?php

namespace App\Http\Controllers;

use App\Contracts\ContentServiceContract;
use App\Enums\ResponseStatus;
use App\Http\Requests\Content\ContentValidatedRequest;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ContentController extends Controller implements HasMiddleware
{
    public function __construct(protected ContentServiceContract $contentService) {

    }
    public static function middleware(): array
    {
        return [
            'auth:sanctum',
            new Middleware (
                'ability:admin,suporte',
                only: [
                    'index',
                    'store',
                    'getByName',
                    'update',
                    'destroy'
                ])
        ];
    }

    public function index()
    {
        $contents = $this->contentService->getAll();
        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,  
            'data' => $contents,
            'code' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function show($matterId)
    {
        $contents = $this->contentService->getById($matterId);
        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,  
            'data' => $contents,
            'code' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function getByName(ContentValidatedRequest $request)
    {
         $contents =$this->contentService->getByName($request->name);
         return response()->json([
            'status' => ResponseStatus::SUCCESS->value,  
            'data' => $contents,
            'code' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function store(ContentValidatedRequest $request){
        
        $this->contentService->create($request->all());
        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,  
            'message' => "Content created",
            'code' => Response::HTTP_CREATED
        ], Response::HTTP_CREATED);
    }

    public function update(ContentValidatedRequest $request, $matterId)
    {
        $this->contentService->update( $request->all(), $matterId);
        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,  
            'message' => "Content updated",
            'code' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function destroy($contentId)
    {
        $this->contentService->delete( $contentId);
        return response()->json([ 
            'status' => ResponseStatus::SUCCESS->value,  
            'message' => "Content of id: $contentId deleted", 
            'code' => Response::HTTP_NO_CONTENT
        ], Response::HTTP_NO_CONTENT);
    }
}
