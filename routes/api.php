<?php

use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


//Unprotected Routes
Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

//Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::post('/account', [AccountController::class, 'store']);

    Route::get('/account', [AccountController::class, 'show']);

    Route::get('/transactions', [TransactionsController::class, 'show']);

    Route::get('/transactions/search/{transaction_amount}', [TransactionsController::class, 'search']);

    Route::get('/banks', [TransferController::class, 'getBanks']);

    Route::post('/transfer', [TransferController::class, 'initiate']);

    Route::post('/logout', [AuthController::class, 'logout']);
});