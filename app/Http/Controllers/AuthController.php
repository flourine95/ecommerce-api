<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseController
{
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->errorResponse('Invalid email or password', 401);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return $this->successResponse([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserResource($user),
            ], 'Login successful');

        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return $this->serverErrorResponse('An error occurred during login');
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->successResponse(null, 'Logout successful');

        } catch (Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return $this->serverErrorResponse('An error occurred during logout');
        }
    }

    public function user(Request $request)
    {
        try {
            return $this->successResponse(
                new UserResource($request->user()),
                'User information retrieved successfully'
            );

        } catch (Exception $e) {
            Log::error('Get user error: ' . $e->getMessage());
            return $this->serverErrorResponse('An error occurred while retrieving user information');
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('user');

            return $this->successResponse(
                new UserResource($user),
                'Registration successful',
                201
            );

        } catch (Exception $e) {
            Log::error('Register error: ' . $e->getMessage());
            return $this->serverErrorResponse('An error occurred during registration');
        }
    }

    public function refresh(Request $request)
    {
        try {
            $user = $request->user();
            $user->currentAccessToken()->delete();

            $token = $user->createToken('api-token')->plainTextToken;

            return $this->successResponse([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 'Token refreshed successfully');

        } catch (Exception $e) {
            Log::error('Refresh token error: ' . $e->getMessage());
            return $this->serverErrorResponse('An error occurred while refreshing the token');
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return $this->errorResponse('Current password is incorrect');
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return $this->successResponse(null, 'Password changed successfully');

        } catch (Exception $e) {
            Log::error('Change password error: ' . $e->getMessage());
            return $this->serverErrorResponse('An error occurred while changing the password');
        }
    }
}
