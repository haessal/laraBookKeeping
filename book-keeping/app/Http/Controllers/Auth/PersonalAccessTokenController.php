<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonalAccessTokenController extends Controller
{
    /**
     * Store a newly created personal access token in storage.
     */
    public function store(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        // delete all existing tokens for the user before creating a new one
        $user->tokens()->delete();

        $token = $user->createToken('personal-access-token');

        return response()->json([
            'token' => $token->plainTextToken,
            'created_at' => $token->accessToken->created_at,
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * Get the creation date of the latest personal access token for the user.
     */
    public function get_creation_date(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $token = $user->tokens()->latest()->first();

        return response()->json([
            'created_at' => $token?->created_at,
        ]);
    }

    /**
     * Revoke the user's personal access token.
     */
    public function destroy(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->tokens()->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
