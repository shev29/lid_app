<?php

use Illuminate\Support\Facades\Route;
use Modules\DocumentApproval\Http\Controllers\ApiExternalController;
use Modules\DocumentApproval\Http\Controllers\DocumentApprovalController;
use Modules\DocumentApproval\Http\Controllers\MigrationBmlLogController;

Route::prefix('doc_approval')->middleware(['PreventBackHistory', 'guest.redirect', 'DecodeModSecurityPlaceholders'])->group(function(){
    Route::get('/dashboard', [DocumentApprovalController::class, 'dashboard'])->name('doc_approval_dashboard');
    Route::get('/approval', [DocumentApprovalController::class, 'approval'])->name('doc_approval_approval');
    Route::get('/request', [DocumentApprovalController::class, 'request'])->name('doc_approval_request');
    // Route::get('/history', [DocumentApprovalController::class, 'history']);
    Route::get('/newRequest', [DocumentApprovalController::class, 'newRequest']);
    Route::get('/getUnit', [DocumentApprovalController::class, 'getUnit']);
    Route::get('/getCompany', [DocumentApprovalController::class, 'getCompany']);
    Route::get('/getDepartment', [DocumentApprovalController::class, 'getDepartment']);
    Route::get('/getCurrency', [DocumentApprovalController::class, 'getCurrency']);
    Route::get('/getDocumentPurpose', [DocumentApprovalController::class, 'getDocumentPurpose']);
    Route::get('/getEmployee', [DocumentApprovalController::class, 'getEmployee']);
    Route::get('/getApproval', [DocumentApprovalController::class, 'getApproval']);
    Route::get('/getDocumentType', [DocumentApprovalController::class, 'getDocumentType']);
    Route::get('/getDocumentTypeRequest', [DocumentApprovalController::class, 'getDocumentTypeRequest']);
    // Route::get('/getDocumentHeader', [DocumentApprovalController::class, 'getDocumentHeader']);
    Route::get('/getDocumentNumber', [DocumentApprovalController::class, 'getDocumentNumber']);
    Route::get('/getCostCenter', [DocumentApprovalController::class, 'getCostCenter']);
    Route::get('/getReason', [DocumentApprovalController::class, 'getReason']);
    Route::post('/saveForm', [DocumentApprovalController::class, 'saveForm']);
    Route::post('/ongoing', [DocumentApprovalController::class, 'ongoing']);
    Route::get('/viewToPdf', [DocumentApprovalController::class, 'viewToPdf']);
    Route::get('/generatePdf', [DocumentApprovalController::class, 'generatePdf']);
    Route::get('/getInspection', [DocumentApprovalController::class, 'getInspection']);
    Route::get('/getLocation', [DocumentApprovalController::class, 'getLocation']);
    Route::post('/updateSigner', [DocumentApprovalController::class, 'updateSigner']);
    Route::post('/documentHistory', [DocumentApprovalController::class, 'documentHistory']);
    Route::post('/generateOrderForm', [ApiExternalController::class, 'generateOrderForm']);

    Route::get('/suggestion', [DocumentApprovalController::class, 'suggestion']);
    Route::get('/migration', [MigrationBmlLogController::class, 'migration']);
    Route::get('/migration_vendor', [MigrationBmlLogController::class, 'migration_vendor']);
});

Route::get('/doc_approval/view', [DocumentApprovalController::class, 'view']);
Route::get('/doc_approval/viewForm', [DocumentApprovalController::class, 'viewForm']);
Route::get('/doc_approval/getSendBackOptions', [DocumentApprovalController::class, 'getSendBackOptions']);
Route::post('/doc_approval/updateAction', [DocumentApprovalController::class, 'updateAction'])->middleware(['EscapeRequestInput', 'DecodeModSecurityPlaceholders']);
Route::get('/doc_approval/testpdf', [DocumentApprovalController::class, 'testpdf']);
Route::get('/doc_approval/getAttachment', [DocumentApprovalController::class, 'getAttachment']);
Route::get('/doc_approval/getProgress', [DocumentApprovalController::class, 'getProgress']);
Route::get('/doc_approval/reminder', [DocumentApprovalController::class, 'reminder']);
Route::post('/doc_approval/ongoingApproval', [DocumentApprovalController::class, 'ongoingApproval'])->middleware(['EscapeRequestInput', 'DecodeModSecurityPlaceholders']);
Route::get('/doc_approval/getDocumentList', [DocumentApprovalController::class, 'getDocumentList']);



