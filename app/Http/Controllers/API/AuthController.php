<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Handle user login and return user details with a plaintext token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create a token for the user.
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], Response::HTTP_OK);
    }

    /**
     * Return the authenticated user's details.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user(), Response::HTTP_OK);
    }

    /**
     * Logout the user (revoke the current token).
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke the token that was used to authenticate the current request.
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out'], Response::HTTP_OK);
    }
}
