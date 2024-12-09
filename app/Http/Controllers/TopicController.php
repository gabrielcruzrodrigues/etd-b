<?php

namespace App\Http\Controllers;

use App\Http\Requests\Topic\TopicRequest;
use App\Contracts\TopicServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\Topic\TopicService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TopicController extends Controller implements HasMiddleware
{
    public function __construct(private readonly TopicServiceContract $topicService){}


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

    public function store(TopicRequest $request): JsonResponse
    {          
        $topic = $this->topicService->createTopic($request->all());

        return response()->json([
            'success' => true,
            'data'    => $topic,
            'code'    => Response::HTTP_CREATED
        ], 201);   
    }

    public function show(int $topicId) : JsonResponse
    {
        $topic = $this->topicService->findTopicById($topicId);

        return response()->json([
            'success' => true,
            'data'    => $topic,
            'code'    => Response::HTTP_OK
        ], 200);
    }

    public function update(Request $request, int $topicId): JsonResponse
    {   
        $this->topicService->updateTopic($request->all(), $topicId);

        return response()->json([
            'code' => Response::HTTP_NO_CONTENT
        ], 204);

    }

    public function destroy(int $topicId)
    {
        $reponse = $this->topicService->deleteTopic($topicId);

        return response()->json([
            'code' => Response::HTTP_NO_CONTENT
        ], 204);
    }

    public function index(Request $request): JsonResponse
    {
        $page = $request->query('page', 10);
        $topics = $this->topicService->getAllTopics($page);

        return response()->json([
            'success' => true,
            'data'    => $topics,
            'code'    => Response::HTTP_OK
        ], 200);
    }

    public function searchByName(Request $request, string $name): JsonResponse
    {   
        $topics = $this->topicService->searchTopicByName($name);
        
        return response()->json([
            'success' => true,
            'data'    => $topics,
            'code'    => Response::HTTP_OK
        ], 200);
    }
}
