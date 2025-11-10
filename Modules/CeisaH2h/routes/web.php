<?php

use Illuminate\Support\Facades\Route;
use Modules\CeisaH2h\Http\Controllers\CeisaH2hController;

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

// Route::group([], function () {
//     Route::resource('ceisah2h', CeisaH2hController::class)->names('ceisah2h');
// });

Route::prefix('ceisah2h')->middleware(['PreventBackHistory', 'guest.redirect'])->group(function(){
    Route::prefix('dokumen_pabean')->group(function(){
        Route::get('/', [CeisaH2hController::class, 'dokumen_pabean'])->name('dokumen_pabean');
        Route::get('bc20/{id}', [CeisaH2hController::class, 'bc20']);
        Route::get('ceisa_auth', [CeisaH2hController::class, 'ceisa_auth'])->name('ceisa_auth');
        Route::post('get_data_form', [CeisaH2hController::class, 'get_data_form'])->name('ceisa_get_data_form');
    });
});


