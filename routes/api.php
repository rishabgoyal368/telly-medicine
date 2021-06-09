<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\RecordController;

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


Route::post('/register', [ApiController::class, 'user_registration']);
Route::post('/generate-user-id', [ApiController::class, 'genrate_user_id']);
Route::post('/email-verify', [ApiController::class, 'user_verify']);
Route::post('/otp-verify', [ApiController::class, 'otp_verify']);
Route::post('/login', [ApiController::class, 'user_login']);
Route::post('/forgot-password',[ApiController::class, 'forgot_password']);
// Route::post('/reset-password',[ApiController::class, 'reset_password']);
Route::get('/logout',[ApiController::class, 'logout']); 

Route::post('/addRecords',[RecordController::class, 'addRecord']);
Route::get('/getRecords',[RecordController::class, 'getRecords']);
