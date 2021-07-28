<?php


namespace App\Services;

use JWTAuth;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use UnexpectedValueException;

class AuthService
{

    public function registerUser($fields)
    {
        $userExists = User::where('email', $fields['email'])
                            ->where('name', $fields['name'])->first();

        if($userExists){

            throw new UnexpectedValueException('user already exists');

        }

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);
    
        $token = JWTAuth::attempt($fields->only('email', 'password'));


        return ['user' => $user, 'token' => $token, 'token_type' => 'Bearer'];

    }

    public function userLogin($credentials)
    {
        if (! $token = JWTAuth::attempt($credentials->only('email', 'password'))) {

            throw new AuthenticationException('Invalid login details');

        }

        $user = User::where('email', $credentials['email'])->firstOrFail();

        return ['user' => $user, 'token' => $token, 'token_type' => 'Bearer'];
    }

}
