<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\EmployeeOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $pageTitle = "Dashboard";
    return view('dashboard', compact('pageTitle'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Setup Role
    Route::resource('roles', RoleController::class)
        ->middleware('permission:role_index |role_create| role_edit');
    //Setup Permissions
    Route::get('permissions', [PermissionsController::class, 'index'])
        ->name('permissions.index')
        ->middleware('permission:permission_index');
    Route::post('permissions', [PermissionsController::class, 'store'])->name('permissions.store');
    Route::get('permissions/{permission}', [PermissionsController::class, 'show'])
        ->name('permissions.show');
    Route::put('permissions/{permission}', [PermissionsController::class, 'update'])
        ->name('permissions.update');
    Route::delete('permissions/{permission}', [PermissionsController::class, 'destroy'])
        ->name('permissions.destroy');
    //Setup Users
    Route::resource('/users', UsersController::class)
        ->middleware('permission:user_index|user_create|user_edit');

    //Order controller
    Route::resource('categories', CategoriesController::class);
    Route::resource('orders', OrderController::class);
    Route::delete('orders/file/destroy/{order}', [OrderController::class, 'fileDelete'])
        ->name('order.file.destroy');

    //Employee Order Controller
    Route::resource('employee-orders', EmployeeOrderController::class);
    Route::post('employee-orders/{order}/lock-file', [EmployeeOrderController::class, 'lockFile'])
        ->name('employee-orders.lock.file');

    Route::post('employee-orders/claim-batch/{order}', [EmployeeOrderController::class, 'claimBatch'])
        ->name('employee-orders.claim.batch');

});

require __DIR__ . '/auth.php';
