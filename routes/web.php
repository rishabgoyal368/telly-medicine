<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\backEnd\ProfileController;
use App\Http\Controllers\backEnd\UserManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::match(['get','post'],'/',[AuthController::class, 'login']);
Route::match(['get','post'],'/admin',[AuthController::class, 'login']);
Route::match(['get','post'],'/login',[AuthController::class, 'login']);
Route::match(['get','post'],'/admin/login',[AuthController::class, 'login']);
Route::match(['get','post'],'/admin/forgot-password',[AuthController::class, 'forgot_password']);
Route::match(['get','post'],'admin/forgot-password',[AuthController::class, 'forgot_password']);
Route::match(['get','post'],'admin/set/password/{user_id}/{security_code}',[AuthController::class, 'set_password']);

Route::get('validate/email',[AuthController::class, 'check_admin_email']);


Route::group(['prefix'=>'admin', 'middleware'=>'CheckAdminAuth'],function(){

	Route::get('/logout',[AuthController::class, 'logout']);
	Route::get('/dashboard',[AuthController::class, 'dashboard']);

	// profile
	Route::match(['get','post'],'/profile',[ProfileController::class, 'index']);
	Route::post('/change-password',[ProfileController::class, 'change_password']);

	//user-management
	Route::match(['get','post'],'/user',[UserManagementController::class, 'index']);
	Route::match(['get','post'],'/user/add',[UserManagementController::class, 'add']);
	Route::match(['get','post'],'/user/edit/{id}',[UserManagementController::class, 'edit']);
	Route::match(['get','post'],'/user/delete/{id}',[UserManagementController::class, 'delete']);



});

// common
define('systemImgPath',asset('/images/system'));
define('backEndCssPath','/backEnd/css');
define('backEndJsPath','/backEnd/js');
define('COMMON_ERROR', 'Some error occured. Please try again later after sometime');

// controller
define('AdminProfileBasePath','public/images/profile/admin');
define('UserProfileBasePath','/images/profile/user');

// views
define('AdminProfileImgPath',asset('/images/profile/admin'));
define('UserProfileImgPath',asset('/images/profile/user'));


//view file static path
define('DefaultImgPath',asset('/images/system/default-image.png'));
