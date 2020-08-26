<?php

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

Route::middleware('auth')->group(function () {

  Route::get('/', 'IndexController@index')->name('index');
  Route::resource('budget', 'BudgetController');


  /* Таблицы vue-table-2 (экспорт) */
  Route::post('/table/export', 'TableController@export')->name('table-export');

});


Auth::routes([
  'register' => false,
  'confirm' => false,
  'email' => false,
  'request' => false,
  'update' => false,
  'reset' => false,
]);

