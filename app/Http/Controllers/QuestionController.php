<?php

namespace App\Http\Controllers;

use App\Contracts\QuestionServiceContract;
use App\Exceptions\QuestionInventoryException;
use App\Http\Requests\Question\QuestionFormRequest;
use App\Http\Requests\Question\QuestionUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Symfony\Component\HttpFoundation\Response;

class QuestionController extends Controller implements HasMiddleware
{
    public function __construct(private readonly QuestionServiceContract $questionService)
    {
    }

    public static function middleware(): array
    {
        return [
            'auth:sanctum',
            new Middleware('ability:admin,suporte', only: ['store, update, destroy'])
        ];
    }

    public function index(Request $request): JsonResponse
    {
        if (!isset($request['page']) || !isset($request['perPage']))
        {
            throw QuestionInventoryException::paginationExeption("the elements for pagination not found");
        }

        $response = $this->questionService->getAll($request['page'], $request['perPage']);

        return response()->json([
            'success' => true,
            'data' => $response,
            'code' => Response::HTTP_OK
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $question = $this->questionService->getById($id);

        return response()->json([
            'success' => true,
            'data' => $question,
            'code' => Response::HTTP_OK
        ], 200);
    }

    public function getByCode(string $code): JsonResponse
    {
        $question = $this->questionService->getByCode($code);

        return response()->json([
            'success' => true,
            'data' => $question,
            'code' => Response::HTTP_OK
        ], 200);
    }

    public function query(Request $request): JsonResponse
    {
        if (!isset($request['page']) || !isset($request['perPage']))
        {
            throw QuestionInventoryException::paginationExeption("the elements for pagination not found");
        }

        $questions = $this->questionService->query($request, $request['page'], $request['perPage']);

        return response()->json([
            'success' => true,
            'data' => $questions,
            'code' => Response::HTTP_OK
        ], 200);
    }

    public function store(QuestionFormRequest $request): JsonResponse
    {
        $response = $this->questionService->create($request->all());

        return response()->json([
            'success' => true,
            'data' => $response,
            'code' => Response::HTTP_OK
        ], 201);
    }

    public function update(QuestionUpdateRequest $request, int $id): JsonResponse
    {
        $this->questionService->update($request->all(), $id);
        return response()->json([], 204);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->questionService->delete($id);
        return response()->json([], 204);
    }

    public function script(Request $request): JsonResponse
    {
        $response = $this->questionService->createQuestionScript($request->all());

        return response()->json([
            'success' => true,
            'data' => $response,
            'code' => Response::HTTP_CREATED
        ], 201);
    }

    public function getAllFilters() : JsonResponse
    {
        $response = $this->questionService->getAllFilters();

        return response()->json([
            'success' => true,
            'data' => $response
        ], 200);
    }
}
