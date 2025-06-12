<?php

use App\Http\Controllers\API\Admin\ArticleAdminController;
use App\Http\Controllers\API\Admin\CategoryController;
use App\Http\Controllers\API\Admin\PermissionController;
use App\Http\Controllers\API\Admin\RoleController;
use App\Http\Controllers\API\Admin\TagsController;
use App\Http\Controllers\API\Admin\UserController;
use App\Http\Controllers\API\Admin\InjuryHistoryAdminController;
use App\Http\Controllers\API\User\InjuryHistoryController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Exceptions\RoleAlreadyExists;


require __DIR__.'/auth.php';
Route::middleware(['auth:sanctum','verified'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/verification/{id}', function($id) {
    $user = User::findOrFail($id);
    $user->email_verified_at = now();
    $user->save();
});

Route::middleware(['auth:sanctum', 'role:user'])->prefix('user')->group(function () {
    Route::apiResource('/injury-history', InjuryHistoryController::class);
    Route::prefix('Articles')->group(function () {
        Route::get('/', [ArticleAdminController::class, 'index']);
        Route::get('/{id}', [ArticleAdminController::class, 'show']);
    });
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/injury-history', InjuryHistoryController::class);
    Route::apiResource('/injury-history-admin', InjuryHistoryAdminController::class);
    Route::apiResource('/Articles', ArticleAdminController::class);
    Route::apiResource('/Category', CategoryController::class);
    Route::apiResource('/Tags', TagsController::class);
});

Route::middleware(['auth:sanctum', 'role:superadmin'])->prefix('superadmin')->group(function () {
    Route::apiResource('/roles', RoleController::class);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/injury-history-admin', InjuryHistoryAdminController::class);
    Route::apiResource('/injury-history', InjuryHistoryController::class);
    Route::apiResource('/permissions', PermissionController::class);
    Route::apiResource('/articles', ArticleAdminController::class);
    Route::apiResource('/category', CategoryController::class);
    Route::apiResource('/tags', TagsController::class);

});



