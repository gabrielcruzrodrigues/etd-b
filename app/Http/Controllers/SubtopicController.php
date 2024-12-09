<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subtopic\SubtopicRequest;
use App\Contracts\SubtopicServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Subtopic\SubtopicService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SubtopicController extends Controller implements HasMiddleware
{
    public function __construct(private readonly SubtopicServiceContract $subtopicService){}

    public static function middleware(): array
    {
        return [
            'auth:sanctum',
            new Middleware (
                'ability:admin,suporte',
                only: [
                    'store',
                    'update',
                    'destroy'
                ])
        ];
    }

    public function store(SubtopicRequest $request): JsonResponse
    {          
        $subtopic = $this->subtopicService->createSubtopic($request->all());

        return response()->json([
            'success' => true,
            'data'    => $subtopic,
            'code'    => Response::HTTP_CREATED
        ], 201);   
    }

    public function show(int $subtopicId) : JsonResponse
    {
        $subtopic = $this->subtopicService->findSubtopicById($subtopicId);

        return response()->json([
            'success' => true,
            'data'    => $subtopic,
            'code'    => Response::HTTP_OK
        ], 200);
    }

    public function update(Request $request, int $subtopicId): JsonResponse
    {   
        $this->subtopicService->updateSubtopic($request->all(), $subtopicId);

        return response()->json([
            'code' => Response::HTTP_NO_CONTENT
        ], 204);

    }

    public function destroy(int $subtopicId)
    {
        $reponse = $this->subtopicService->deleteSubtopic($subtopicId);

        return response()->json([
            'code' => Response::HTTP_NO_CONTENT
        ], 204);
    }

    public function index(Request $request): JsonResponse
    {
        $page = $request->query('page', 10);
        $subtopics = $this->subtopicService->getAllSubtopics($page);

        return response()->json([
            'success' => true,
            'data'    => $subtopics,
            'code'    => Response::HTTP_OK
        ], 200);
    }

    public function searchByName(Request $request, string $name): JsonResponse
    {   
        $subtopics = $this->subtopicService->searchSubtopicByName($name);
        
        return response()->json([
            'success' => true,
            'data'    => $subtopics,
            'code'    => Response::HTTP_OK
        ], 200);
    }
}
