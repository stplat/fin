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

  Route::get('/involvement/all', 'InvolvementController@all')->name('involvement.all');
  Route::get('/involvement', 'InvolvementController@index')->name('involvement.index');
  Route::put('/involvement', 'InvolvementController@update')->name('involvement.update');

  Route::get('/budget/all', 'BudgetController@all')->name('budget.all');
  Route::resource('budget', 'BudgetController')->only('index');

  Route::get('/shipment/all', 'ShipmentController@all')->name('shipment.all');
  Route::resource('shipment', 'ShipmentController')->only('index');


  /* Таблицы vue-table-2 (экспорт) */
//  Route::post('/table/export', 'TableController@export')->name('table-export');

});


Auth::routes([
  'register' => false,
  'confirm' => false,
  'email' => false,
  'request' => false,
  'update' => false,
  'reset' => false,
]);

