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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register',[AuthController::class,'register']);
Route::get('email_verify',[AuthController::class,'emailVerify']);
Route::post('login',[AuthController::class,'login']);
Route::post('view_profile',[AuthController::class,'viewProfile']);
Route::post('forgot_password',[AuthController::class,'forgotPassword']);
Route::post('verify_pin',[AuthController::class,'verifyPin']);
Route::post('reset_password',[AuthController::class,'resetPassword']);
Route::post('update_profile',[AuthController::class,'updateProfile']);
Route::post('logout',[AuthController::class,'logout']);

Route::post('upload_image',[ImageController::class,'uploadImage']);
Route::post('list_image',[ImageController::class,'listImage']);
Route::post('delete_image',[ImageController::class,'deleteImage']);
Route::post('upload_image',[ImageController::class,'uploadImage']);
