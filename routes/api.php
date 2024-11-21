<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\HomeOwnerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
},);




// مسار لتسجيل مستخدم جديد باستخدام دالة 'register' من AuthController
Route::post('/register',[AuthController::class,'register']);

// مسار لتسجيل الدخول باستخدام دالة 'login' من AuthController
Route::post('/login', [AuthController::class, 'login']);






// مجموعة مسارات خاصة بال (admin) فقط، محمية بواسطة Middleware 'role:admin'
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, '']);
});

// مجموعة مسارات خاصة بال (homeowner) فقط، محمية بواسطة Middleware 'role:homeowner'
Route::middleware(['role:homeowner'])->group(function () {
    Route::get('/homeowner/profile', [HomeOwnerController::class, '']);
});

// مجموعة مسارات خاصة بال (contractor) فقط، محمية بواسطة Middleware 'role:contractor'
Route::middleware(['role:contractor'])->group(function () {
    Route::get('/contractor/profile', [ContractorController::class, '']);
});
