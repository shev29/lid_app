<?php

use Illuminate\Support\Facades\Route;
use Modules\ProcurementPurchasing\Http\Controllers\ProcurementPurchasingController;

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

Route::prefix('proc_pur')->middleware(['PreventBackHistory', 'guest.redirect', 'DecodeModSecurityPlaceholders'])->group(function(){
    Route::get('/dashboard', [ProcurementPurchasingController::class, 'dashboard'])->name('proc_pur_dashboard');
    Route::get('/request', [ProcurementPurchasingController::class, 'request'])->name('proc_pur_request');
    Route::get('/archive', [ProcurementPurchasingController::class, 'archive'])->name('proc_pur_archive');
    Route::get('/settings', [ProcurementPurchasingController::class, 'settings'])->name('proc_pur_settings');
    Route::get('/orderFormRequest', [ProcurementPurchasingController::class, 'orderFormRequest']);
    Route::get('/applicationFormRequest', [ProcurementPurchasingController::class, 'applicationFormRequest']);
    Route::get('/applicationCompleted', [ProcurementPurchasingController::class, 'applicationCompleted']);
    Route::get('/viewForm', [ProcurementPurchasingController::class, 'viewForm']);
    Route::get('/newForm', [ProcurementPurchasingController::class, 'newForm']);
    Route::get('/getVendor', [ProcurementPurchasingController::class, 'getVendor']);
    Route::get('/getVendorDetails', [ProcurementPurchasingController::class, 'getVendorDetails']);
    Route::get('/getDelivery', [ProcurementPurchasingController::class, 'getDelivery']);
    Route::post('/saveDelivery', [ProcurementPurchasingController::class, 'saveDelivery']);
    Route::post('/saveForm', [ProcurementPurchasingController::class, 'saveForm']);
    Route::get('/itemUnprocessed', [ProcurementPurchasingController::class, 'itemUnprocessed']);
    Route::put('/updateOrderItem', [ProcurementPurchasingController::class, 'updateOrderItem']);
    Route::put('/updatePoFormHistory', [ProcurementPurchasingController::class, 'updatePoFormHistory']);
    Route::post('/purchasingHistory', [ProcurementPurchasingController::class, 'purchasingHistory'])->name('purchasingHistory');
    Route::post('/purchasingVendorReference', [ProcurementPurchasingController::class, 'purchasingVendorReference'])->name('purchasingVendorReference');
    Route::get('/getApprovalMatrix', [ProcurementPurchasingController::class, 'getApprovalMatrix']);
    Route::get('/getPurchaseTypeGroup', [ProcurementPurchasingController::class, 'getPurchaseTypeGroup']);
    Route::get('/getPurchaseTypeComponents', [ProcurementPurchasingController::class, 'getPurchaseTypeComponents']);
    Route::post('/orderFormApplicantTable', [ProcurementPurchasingController::class, 'orderFormApplicantTable'])->name('orderFormApplicantTable');
    Route::post('/inspectorTable', [ProcurementPurchasingController::class, 'inspectorTable'])->name('inspectorTable');
    Route::post('/poApprovalRuleTable', [ProcurementPurchasingController::class, 'poApprovalRuleTable'])->name('poApprovalRuleTable');
    Route::post('/saveOrderFormApplicant', [ProcurementPurchasingController::class, 'addOrderFormApplicant']);
    Route::put('/saveOrderFormApplicant', [ProcurementPurchasingController::class, 'updateOrderFormApplicant']);
    Route::post('/savePoApprovalRule', [ProcurementPurchasingController::class, 'addPoApprovalRule']);
    Route::put('/savePoApprovalRule', [ProcurementPurchasingController::class, 'updatePoApprovalRule']);
    Route::post('/saveInspector', [ProcurementPurchasingController::class, 'addInspector']);
    Route::put('/saveInspector', [ProcurementPurchasingController::class, 'updateInspector']);
    Route::post('/saveVendor', [ProcurementPurchasingController::class, 'addVendor']);
    Route::put('/saveVendor', [ProcurementPurchasingController::class, 'updateVendor']);
    Route::get('/getRuleFlow', [ProcurementPurchasingController::class, 'getRuleFlow']);
    Route::get('/userSelection', [ProcurementPurchasingController::class, 'userSelection']);
    Route::post('/exportData', [ProcurementPurchasingController::class, 'exportData']);
    Route::post('/addPurchaseTypeComponents', [ProcurementPurchasingController::class, 'addPurchaseTypeComponents']);
    Route::get('/poInspection', [ProcurementPurchasingController::class, 'poInspection']);
    Route::get('/poDetails', [ProcurementPurchasingController::class, 'poDetails']);
});

Route::get('/proc_pur/getComparison', [ProcurementPurchasingController::class, 'getComparison']);
