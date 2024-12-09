<?php

namespace App\Http\Controllers;

use App\Contracts\YearServiceContract;
use App\Enums\ResponseStatus;
use App\Services\Question\YearService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class YearController extends Controller
{
    public function __construct(protected YearServiceContract $yearService){}

    public static function middleware(): array
    {
        return [
            'auth:sanctum'
        ];
    }

    public function getById(int $yearId) : JsonResponse
    {
        $response = $this->yearService->getById($yearId);

        return response()->json([
            'status' => ResponseStatus::SUCCESS->value,
            'data' => $response
        ], 200);
    }
}
