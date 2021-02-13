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
  Route::post('/involvement/upload', 'InvolvementController@upload')->name('involvement.upload');

  Route::get('/budget', 'BudgetController@index')->name('budget.index');
  Route::put('/budget', 'BudgetController@update')->name('budget.update');
  Route::get('/budget/all', 'BudgetController@all')->name('budget.all');
  Route::post('/budget/upload', 'BudgetController@upload')->name('budget.upload');

  Route::get('/shipment/all', 'ShipmentController@all')->name('shipment.all');
  Route::resource('shipment', 'ShipmentController')->only('index');
  Route::post('/shipment/upload', 'ShipmentController@upload')->name('shipment.upload');

  Route::get('/finance/all', 'FinanceController@all')->name('finance.all');
  Route::resource('finance', 'FinanceController')->only('index');
  Route::post('/finance/upload', 'FinanceController@upload')->name('finance.upload');

  Route::get('/application/all', 'ApplicationController@all')->name('application.all');
  Route::get('/application', 'ApplicationController@index')->name('application.index');
  Route::put('/application', 'ApplicationController@update')->name('application.update');
  Route::post('/application/upload', 'ApplicationController@upload')->name('application.upload');
  Route::post('/application/consolidate', 'ApplicationController@consolidate')->name('application.consolidate');
  Route::post('/application/export', 'ApplicationController@export')->name('application.export');

  Route::get('/warehouse', 'WarehouseController@index')->name('warehouse.index');

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

