<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

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

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::resource('profile', Controllers\ProfileController::class, ['only' => ['index', 'detail', 'store']]);
});

Route::group(['middleware' => ['auth', 'cancerbero']], function () {

    Route::get('/',[Controllers\HomeController::class, 'index'])->name('index.index');
    Route::resource('registros', Controllers\RegistroController::class);

    Route::prefix('catalogs')->name('catalogs.')->group(function () {
        Route::resource('empresas', Controllers\Catalogs\EmpresaController::class);
        Route::resource('users', Controllers\Catalogs\UsersController::class);
        Route::resource('roles', Controllers\Catalogs\RolesController::class);
    });
});

//Auth::routes();
//
//Route::group(['middleware' => ['auth']], function () {
//    Route::resource('profile', 'ProfileController', ['only' => ['index', 'detail', 'store']]);
//});
//
//Route::group(['middleware' => ['auth', 'cancerbero']], function () {
//    Route::get('/', ['as' => 'index.index', 'uses' => 'HomeController@index']);
//
//    Route::namespace ('Catalogs')->prefix('catalogs')->name('catalogs.')->group(function () {
//        Route::resource('users', 'UsersController');
//        Route::resource('roles', 'RolesController');
//    });
//});
