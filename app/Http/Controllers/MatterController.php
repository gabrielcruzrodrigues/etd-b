<?php

namespace App\Http\Controllers;

use App\Contracts\MatterServiceContract;
use App\Enums\ResponseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Matter\MatterValidatedRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MatterController extends Controller implements HasMiddleware
{

    public function __construct(protected MatterServiceContract $matterService)
    {
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

    public function index(): JsonResponse
    {
        $matters = $this->matterService->GetAll();
        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'code' => Response::HTTP_OK,
            'data' => $matters
        ], Response::HTTP_OK);
    }

    public function store(MatterValidatedRequest $request): JsonResponse
    {
        $matter = $this->matterService->create($request->all());
        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'code' => Response::HTTP_CREATED,
            'data' => $matter
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show($id_matter): JsonResponse
    {
        $matter = $this->matterService->GetById($id_matter);
        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'code' => Response::HTTP_OK,
            'data' => $matter
        ], Response::HTTP_OK);
    }

    public function getByName(MatterValidatedRequest $request): JsonResponse
    {
        $matter = $this->matterService->GetByName($request->name);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'code' => Response::HTTP_OK,
            'data' => $matter
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource.
     */

    public function update(MatterValidatedRequest $request, int $matterId): JsonResponse
    {
        $this->matterService->Update($request->all(), $matterId);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'code' => Response::HTTP_OK,
            'message' => "Matter of id: $matterId updated, name changed to $request->name"
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($matterId): JsonResponse
    {
        $this->matterService->Delete($matterId);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'code' => Response::HTTP_NO_CONTENT,
            'message' => "Matter of id: $matterId deleted"
        ], Response::HTTP_OK);
    }
}
