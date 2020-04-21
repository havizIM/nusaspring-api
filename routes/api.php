<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'AuthController@login');

Route::get('products/file/{file_name}', 'ProductController@picture');
Route::get('adjustments/file/{file_name}', 'AdjustmentController@picture');
Route::get('purchases/file/{file_name}', 'PurchaseController@picture');
Route::get('sellings/file/{file_name}', 'SellingController@picture');
Route::get('purchase_returns/file/{file_name}', 'PurchaseReturnController@picture');
Route::get('selling_returns/file/{file_name}', 'SellingReturnController@picture');
Route::get('purchase_payments/file/{file_name}', 'PurchasePaymentController@picture');
Route::get('selling_payments/file/{file_name}', 'SellingPaymentController@picture');

Route::group([
    'middleware' => ['auth:api'],
], function() {
    Route::get('logout', 'AuthController@logout');

    Route::apiResource('categories', 'CategoryController')->except([
        'show'
    ]);

    Route::apiResource('units', 'UnitController')->except([
        'show'
    ]);

    Route::apiResource('tasks', 'TaskController');

    Route::apiResource('reminders', 'ReminderController');

    Route::apiResource('suppliers', 'SupplierController');

    Route::apiResource('customers', 'CustomerController');

    Route::apiResource('products', 'ProductController');
    
    Route::apiResource('sellings', 'SellingController');

    Route::apiResource('purchases', 'PurchaseController');

    Route::apiResource('adjustments', 'AdjustmentController');

    Route::apiResource('purchase_payments', 'PurchasePaymentController');

    Route::apiResource('selling_payments', 'SellingPaymentController');

    Route::apiResource('purchase_returns', 'PurchaseReturnController');

    Route::apiResource('selling_returns', 'SellingReturnController');

    Route::get('export/database', 'ExportController@database');

    Route::get('setting/profile', 'SettingController@profile');

    Route::post('setting/change_password', 'SettingController@change_password');

    Route::get('setting/logs', 'SettingController@logs');

    Route::get('analytics/bussiness/{year}', 'AnalyticController@bussiness');

    Route::get('analytics/top_ten/{order_by}', 'AnalyticController@top_ten');

});
