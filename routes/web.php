<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DashboardController;
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

Route::get('/dashboard', [DashboardController::class,'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Setup Role
    Route::resource('roles', RoleController::class)
        ->middleware('permission:role_index |role_create| role_edit|role_delete');
    //Setup Permissions
    Route::get('permissions', [PermissionsController::class, 'index'])
        ->name('permissions.index')
        ->middleware('permission:permission_index');

    Route::post('permissions', [PermissionsController::class, 'store'])
        ->name('permissions.store')
        ->middleware('permission:permission_create');

    Route::get('permissions/{permission}', [PermissionsController::class, 'show'])
        ->name('permissions.show')
        ->middleware("permission:permission_show");

    Route::put('permissions/{permission}', [PermissionsController::class, 'update'])
        ->name('permissions.update')
        ->middleware('permission:permission_edit');

    Route::delete('permissions/{permission}', [PermissionsController::class, 'destroy'])
        ->name('permissions.destroy')
        ->middleware('permission:permission_delete');
    //Setup Users
    Route::resource('/users', UsersController::class)
        ->middleware('permission:user_index|user_create|user_edit|user_delete');

    //Order controller
    Route::resource('categories', CategoriesController::class)
        ->middleware('permission:category_index|category_create|category_edit');

    Route::resource('orders', OrderController::class)
        ->middleware('permission:order_index|order_create|order_edit|order_delete');

    Route::delete('orders/file/destroy/{order}', [OrderController::class, 'fileDelete'])
        ->name('order.file.destroy')->middleware('permission:order_delete');

    //Employee Order Controller
    Route::resource('employee-orders', EmployeeOrderController::class)
        ->middleware('permission:employee_order_index|employee_order_edit');

    Route::post('employee-orders/{order}/lock-file', [EmployeeOrderController::class, 'lockFile'])
        ->name('employee-orders.lock.file')
        ->middleware('permission:employee_order_lock_file');

    Route::post('employee-orders/claim-batch/{order}', [EmployeeOrderController::class, 'claimBatch'])
        ->name('employee-orders.claim.batch')
        ->middleware('permission:employee_order_claim_batch');

    Route::get('employee-orders/my-batch/{order}', [EmployeeOrderController::class, 'myBatchIndex'])
        ->name('employee-orders.my.batch')
        ->middleware('permission:employee_my_batch_index');

});

require __DIR__ . '/auth.php';
