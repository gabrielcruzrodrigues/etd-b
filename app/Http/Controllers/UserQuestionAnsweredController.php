<?php

namespace App\Http\Controllers;

use App\Contracts\UserQuestionAnnotationServiceContract;
use App\Contracts\UserQuestionAnsweredServiceContract;
use App\Contracts\UserQuestionCommentServiceContract;
use App\Enums\ResponseStatus;
use App\Exceptions\CustomException;
use App\Http\Requests\User\UpdateUserQuestionAnnotationRequest;
use App\Http\Requests\User\UpdateUserQuestionAnsweredRequest;
use App\Http\Requests\User\UpdateUserQuestionCommentRequest;
use App\Http\Requests\User\UserQuestionAnnotationRequest;
use App\Http\Requests\User\UserQuestionAnsweredRequest;
use App\Http\Requests\User\UserQuestionCommentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Symfony\Component\HttpFoundation\Response;

class UserQuestionAnsweredController extends Controller implements HasMiddleware
{
    public function __construct(
        private readonly UserQuestionAnsweredServiceContract $userQuestionAnsweredService,
        private readonly UserQuestionCommentServiceContract $userQuestionCommentService,
        private readonly UserQuestionAnnotationServiceContract $userQuestionAnnotationService
    ) {}

    public static function middleware(): array
    {
        return [
            'auth:sanctum',
            new Middleware (
                'ability:admin,suporte',
                only: [
                    'getUserQuestionAnsweredByUserId',
                    'getUserQuestionAnsweredByQuestionId',
                    'destroyUserQuestionAnswered',

                    'getUserQuestionCommentByUserId',
                    'getUserQuestionCommentByQuestionId',
                    'updateUserQuestionComment',
                    'destroyUserQuestionComment'
                ])
        ];
    }

    public function indexUserQuestionAnswereds(Request $request): JsonResponse
    {
        $page = $request->query('page', 10);

        $response = $this->userQuestionAnsweredService->getAll($page);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response,
            'code' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function storeUserQuestionAnswered(UserQuestionAnsweredRequest $request): JsonResponse
    {
        $response = $this->userQuestionAnsweredService->create($request->all());

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response,
            'code' => Response::HTTP_CREATED
        ], 201);
    }

    public function getUserQuestionAnsweredByUserId(int $userId): JsonResponse
    {
        $response = $this->userQuestionAnsweredService->getByUser($userId);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response,
            'code' => Response::HTTP_OK
        ], 200);
    }

    public function getUserQuestionAnsweredByQuestionId(int $questionId): JsonResponse
    {
        $response = $this->userQuestionAnsweredService->getByQuestion($questionId);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response,
            'code' => Response::HTTP_OK
        ], 200);
    }

    public function updateUserQuestionAnswered(UpdateUserQuestionAnsweredRequest $request, int $userId, int $questionId): JsonResponse
    {
        $this->userQuestionAnsweredService->update($request->all(), $userId, $questionId);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'code' => Response::HTTP_NO_CONTENT
        ], Response::HTTP_NO_CONTENT);
    }

    public function destroyUserQuestionAnswered(int $userId, int $questionId): JsonResponse
    {
        $this->userQuestionAnsweredService->delete($userId, $questionId);
        return response()->json([], 204);
    }

    public function indexUserQuestionComments(Request $request): JsonResponse
    {
        if (!isset($request['page']) || !isset($request['perPage']))
        {
            throw CustomException::paginationExeption();
        }

        $response = $this->userQuestionCommentService->getAll($request['page'], $request['perPage']);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response,
            'code' => Response::HTTP_OK
        ], 200);
    }

    public function storeUserQuestionComment(UserQuestionCommentRequest $request): JsonResponse
    {
        $response = $this->userQuestionCommentService->create($request->all());

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response,
            'code' => Response::HTTP_CREATED
        ], 201);
    }

    public function getUserQuestionCommentByUserId(int $userId): JsonResponse
    {
        $response = $this->userQuestionCommentService->getByUser($userId);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response,
            'code' => Response::HTTP_OK
        ], 200);
    }

    public function getUserQuestionCommentByQuestionId(int $questionId): JsonResponse
    {
        $response = $this->userQuestionCommentService->getByQuestion($questionId);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response,
            'code' => Response::HTTP_OK
        ], 200);
    }

    public function updateUserQuestionComment(UpdateUserQuestionCommentRequest $request, int $id): JsonResponse
    {
        $this->userQuestionCommentService->update($request->all(), $id);
        return response()->json([], 204);
    }

    public function destroyUserQuestionComment(int $id): JsonResponse
    {
        $this->userQuestionCommentService->delete($id);
        return response()->json([], 204);
    }

    public function indexUserQuestionAnnotation(Request $request): JsonResponse
    {
        if (!isset($request['page']) || !isset($request['perPage']))
        {
            throw CustomException::paginationExeption();
        }

        $response = $this->userQuestionAnnotationService->getAll($request['page'], $request['perPage']);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response
        ], Response::HTTP_OK);
    }

    public function storeUserQuestionAnnotation(UserQuestionAnnotationRequest $request): JsonResponse
    {
        $response = $this->userQuestionAnnotationService->create($request->all());

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response
        ], Response::HTTP_CREATED);
    }

    public function getUserQuestionAnnotationByUserId(int $userId): JsonResponse
    {
        $response = $this->userQuestionAnnotationService->getByUser($userId);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response
        ], Response::HTTP_OK);
    }

    public function getUserQuestionAnnotationByUserAndQuestionId(int $userId, int $questionId)
    {
        $response = $this->userQuestionAnnotationService->getByUserAndQuestionId($userId, $questionId);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response
        ], Response::HTTP_OK);
    }

    public function getUserQuestionAnnotationByQuestionId(int $questionId): JsonResponse
    {
        $response = $this->userQuestionAnnotationService->getByQuestion($questionId);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response
        ], Response::HTTP_OK);
    }

    public function updateUserQuestionAnnotation(UpdateUserQuestionAnnotationRequest $request, int $id): JsonResponse
    {
        $this->userQuestionAnnotationService->update($request->all(), $id);
        return response()->json([], 204);
    }

    public function destroyUserQuestionAnnotation(int $id): JsonResponse
    {
        $this->userQuestionAnnotationService->delete($id);
        return response()->json([], 204);
    }
}
