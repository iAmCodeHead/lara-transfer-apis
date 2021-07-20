<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{

    private function tokenGenerator($user)
    {
        return $user->createToken(env('TOKEN_KEY'))->plainTextToken;
    }

    public function registerUser($fields)
    {
        $userExists = User::where('email', $fields['email'])
                            ->where('name', $fields['name'])->first();

        if(!$userExists){
            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password'])
            ]);
    
            $token = $this->tokenGenerator($user);
    
            return [
                'status' => true,
                'statusCode' => 200,
                'message' => 'User registration successful',
                'data' => [$user, 'token' => $token, 'token_type' => 'Bearer']
            ];
        }

        return [
            'status' => false,
            'statusCode' => 400,
            'message' => 'User already exists'
        ];
    }

    public function userLogin($credentials)
    {
        if (!Auth::attempt($credentials->only('email', 'password'))) {
            return [
                'status' => false,
                'statusCode' => 400,
                'message' => 'Invalid login details'
            ];
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();

        $token = $this->tokenGenerator($user);

        return [
            'status' => true,
            'statusCode' => 200,
            'message' => 'User logged in',
            'data' => ['user' => $user, 'token' => $token, 'token_type' => 'Bearer']
        ];
    }
}
