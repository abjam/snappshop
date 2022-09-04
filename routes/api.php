<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/api/make_transaction', [UserController::class, 'makeTransaction']);
Route::get('/api/three_users_last_ten', [AccountController::class, 'threeUsersLastTen']);
Route::get('/api/last_ten_transactions', [AccountController::class, 'last_ten']);
