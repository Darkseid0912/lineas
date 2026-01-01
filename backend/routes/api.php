<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatModuleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatUserController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\CatPermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RelRolePermissionController;
use App\Http\Controllers\ModulesPermissionsController;

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
});

Route::middleware('auth:sanctum')->get('/cat-modules', [CatModuleController::class, 'index']);
Route::middleware('auth:sanctum')->post('/cat-modules', [CatModuleController::class, 'store']);
Route::middleware('auth:sanctum')->post('/cat-permissions', [CatPermissionController::class, 'store']);
Route::middleware('auth:sanctum')->get('/cat-permissions', [CatPermissionController::class, 'index']);
Route::middleware('auth:sanctum')->post('/users', [UserController::class, 'store']);
Route::middleware('auth:sanctum')->get('/users', [UserController::class, 'index']);
Route::middleware('auth:sanctum')->get('/users/role-modules-permissions', [UserController::class, 'roleModulesPermissions']);
Route::middleware('auth:sanctum')->get('/rel-role-permissions', [RelRolePermissionController::class, 'index']);
Route::middleware('auth:sanctum')->post('/rel-role-permissions', [RelRolePermissionController::class, 'store']);
Route::middleware('auth:sanctum')->post('/rel-role-permissions/bulk', [RelRolePermissionController::class, 'storeBulk']);
Route::middleware('auth:sanctum')->post('/rel-role-permissions/sync', [RelRolePermissionController::class, 'syncBulk']);
Route::middleware('auth:sanctum')->get('/rel-role-permissions/by-role', [RelRolePermissionController::class, 'listByRole']);
Route::middleware('auth:sanctum')->get('/modules-with-permissions', [ModulesPermissionsController::class, 'index']);

// Auth routes (token-based with Sanctum)
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});


// Catalog: user types
Route::middleware('auth:sanctum')->get('/cat_users', [CatUserController::class, 'index']);
Route::middleware('auth:sanctum')->post('/cat_users', [CatUserController::class, 'store']);


// Atomic registration flow: company + admin user
Route::post('/register/company-admin', [RegistrationController::class, 'companyAdmin'])->middleware('throttle:10,1');
