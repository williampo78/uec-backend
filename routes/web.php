<?php

use App\Http\Controllers\AdminControllers;
use App\Http\Controllers\ItemControllers;
use App\Http\Controllers\LoginAuthController;
use App\Http\Controllers\DepartmentControllers;
use App\Http\Controllers\SupplierTypeControllers ;
use Illuminate\Support\Facades\Route;

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
Route::group(['prefix' => 'backend', 'middleware' => ['admin']], function () {
    Route::get('/', [AdminControllers::class, 'index']);
    Route::get('registration', [LoginAuthController::class, 'registration'])->name('register-user');
    Route::get('/signOut', [AdminControllers::class, 'signOut'])->name('signout');
    Route::resource('/admin', AdminControllers::class);
    Route::resource('/item', ItemControllers::class, ['names' => ['index' => 'item']]);
    Route::resource('/warehouse',\App\Http\Controllers\WarehouseController::class);
    Route::resource('/department', DepartmentControllers::class, ['names' => ['index' => 'department']]);
    Route::resource('/supplier_type', SupplierTypeControllers::class, ['names' => ['index' => 'supplier_type']]);
});

Route::get('login', [LoginAuthController::class, 'index'])->name('login');
Route::post('custom-login', [LoginAuthController::class, 'customLogin'])->name('login.custom');
Route::post('custom-registration', [LoginAuthController::class, 'customRegistration'])->name('register.custom');
