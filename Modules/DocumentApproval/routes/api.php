<?php

use Illuminate\Support\Facades\Route;
use Modules\DocumentApproval\Http\Controllers\ApiExternalController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
//     Route::apiResource('documentapproval', DocumentApprovalController::class)->names('documentapproval');
// });

Route::prefix('purchasingCarton')->middleware(['api', 'api.key'])->group(function () {
    Route::post('/getPo', [ApiExternalController::class, 'getPoCarton']);
    Route::post('/sendPrePo', [ApiExternalController::class, 'storePoCarton']);
});