<?php

namespace App\Http\Controllers;

use App\Contracts\AuthServiceContract;
use App\Http\Requests\sendPasswordResetLinkRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller
{
    public function __construct(private readonly AuthServiceContract $authService)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $this->authService->registerUser($request->all());

        return response()->json(['message' => 'Registration completed successfully.'], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->all());

        return response()->json([
            'message' => 'Login successful.',
            'data' => [
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'],
                'expires_in' => config('sanctum.ac_expiration') * 60,
            ],
        ], Response::HTTP_OK);
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $request->user()->tokens()->where('name', 'access-api')->delete();

        $accessToken = $request->user()->createToken(
            'access-api',
            [$request->user()->role],
            now()->addMinutes(config('sanctum.ac_expiration'))
        )->plainTextToken;

        return response()->json([
            'message' => 'Access token refreshed successfully',
            'access_token' => $accessToken,
            'expires_in' => config('sanctum.ac_expiration') * 60,
        ], Response::HTTP_OK);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Token Revoked'], Response::HTTP_OK);
    }

    public function sendPasswordResetLink(sendPasswordResetLinkRequest $request): JsonResponse
    {
        $this->authService->sendResetLink($request->all());

        return response()->json(['message' => 'Reset link sent to your email.'], Response::HTTP_OK);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
       $this->authService->resetPassword($request->all());

        return response()->json(['message' => 'Password reset successfully.'], Response::HTTP_OK);
    }
}
