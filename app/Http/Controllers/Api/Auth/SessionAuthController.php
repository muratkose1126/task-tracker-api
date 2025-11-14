<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SessionAuthController extends Controller
{
    /**
     * Register with session-based authentication
     * Sets session cookie instead of returning token
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Authenticate the user (session-based)
        Auth::login($user);

        return response()->json([
            'user' => new UserResource($user),
            'message' => 'Registered and logged in successfully',
        ], 201);
    }

    /**
     * Login with session-based authentication
     * Sets session cookie instead of returning token
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        // Authenticate the user (session-based)
        Auth::login($user);

        return response()->json([
            'user' => new UserResource($user),
            'message' => 'Logged in successfully',
        ]);
    }

    /**
     * Logout and destroy session
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate session only if session store is available
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request)
    {
        return new UserResource($request->user());
    }
}
