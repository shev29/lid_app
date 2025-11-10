<?php

use Illuminate\Support\Facades\Route;
use Modules\ApplicationManager\Http\Controllers\ApplicationManagerController;
use Modules\ApplicationManager\Http\Controllers\StorageManagerController;
use Modules\ApplicationManager\Models\ApplicationManagerModel;

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

Route::prefix('app_manager')->middleware(['PreventBackHistory', 'guest.redirect'])->group(function(){
    Route::get('/', [ApplicationManagerController::class, 'appManager'])->name('app_man');
    // Route::get('/users_management', [ApplicationManagerController::class, 'usersManagement'])->name('users_management');
    // Route::get('/navigation_management', [ApplicationManagerController::class, 'navigationManagement'])->name('navigation_management');
    Route::get('/storage_manager', [StorageManagerController::class, 'index'])->name('storage_manager.index');
    Route::get('/storage_browse', [StorageManagerController::class, 'browse'])->name('storage_manager.browse');
    Route::post('/storage_upload', [StorageManagerController::class, 'upload'])->name('storage_manager.upload');
    Route::post('/storage_create_dir', [StorageManagerController::class, 'createFolder'])->name('storage_manager.create-folder');
    Route::post('/storage_rename', [StorageManagerController::class, 'rename'])->name('storage_manager.rename');
    Route::delete('/storage_delete', [StorageManagerController::class, 'delete'])->name('storage_manager.delete');
    Route::post('/storage_download', [StorageManagerController::class, 'download'])->name('storage_manager.download');
    Route::post('/storage_download_zip', [StorageManagerController::class, 'downloadZip'])->name('storage_manager.download_zip');
    Route::post('/storage_move', [StorageManagerController::class, 'move'])->name('storage_manager.move');
    Route::post('/storage_duplicate', [StorageManagerController::class, 'duplicate'])->name('storage_manager.duplicate');
    Route::get('/folder_tree', [StorageManagerController::class, 'getFolderTree']);
});
