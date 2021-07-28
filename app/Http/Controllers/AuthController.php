<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
class AuthController extends Controller
{
    public function register(Request $request, AuthService $authService)
    {

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $response = $authService->registerUser($request);

        return response()->json(['status' => true, 'message' => 'User registration successful','data' => $response]);
    }

    public function login(Request $request, AuthService $authService)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $loginResponse = $authService->userLogin($request);

        return response()->json(['status' => true, 'message' => 'User login successful','data' => $loginResponse]);
    }

    public function logout()
    {

        auth()->logout();

        return response()->json(['status' => true ,'message' => 'User logged out']);

    }
}
