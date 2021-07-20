<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request, AuthService $authService)
    {

        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $response = $authService->registerUser($fields);

        return response()->json($response, $response['statusCode']);
    }

    public function login(Request $request, AuthService $authService)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $loginResponse = $authService->userLogin($request);

        return response()->json($loginResponse, $loginResponse['statusCode']);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return ['message' => 'User logged out'];
    }
}
