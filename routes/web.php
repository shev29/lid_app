<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\RenderController;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Session;
use Modules\Home\Http\Controllers\HomeController;

Route::get('/test-email', function() {
    // $emailController = new EmailController();
    // return $emailController->sendEmail(
    //     1, // ID dari master_email_account yang akan digunakan
    //     'krisman.silalahi@gmail.com', // Alamat email penerima
    //     'Test Email', // Subject email
    //     '<p>This is a test email.</p>' // Body email dalam format HTML
    // );

    // SendEmailJob::dispatch(
    //     2, // ID dari master_email_account yang akan digunakan
    //     'krisman.silalahi@gmail.com', // Alamat email penerima
    //     'Your subject', // Subject email
    //     '<p>This is the email body</p>' // Body email dalam format HTML
    // );

    // $job = new SendEmailJob(
    //     2, // ID dari master_email_account yang akan digunakan
    //     'krisman.silalahi@gmail.com',
    //     'Your subject',
    //     '<p>This is the email body</p>'
    // );
    // dispatch($job);

    // return response()->json(['message' => 'Form saved and email is being sent in the background'], 200);
});

Route::get('/login', [AuthController::class, 'login'])->name('login')->middleware(['PreventBackHistory', 'auth.redirect']);
// Route::post('/auth_login', [AuthController::class, 'auth_login'])->name('authLogin')->middleware('throttle:5,1');
Route::post('/auth_login', [AuthController::class, 'auth_login'])->name('authLogin')->middleware(['EscapeRequestInput']);
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/change_password', [AuthController::class, 'changePassword']);
Route::post('/updatePassword', [AuthController::class, 'updatePassword'])->name('updatePassword');
Route::post('/resetPassword', [AuthController::class, 'resetPassword'])->name('resetPassword');
Route::get('/alpine', [AuthController::class, 'alpine']);

Route::post('/generatePdf', [RenderController::class, 'generatePdf'])->name('generatePdf');
Route::get('/printPdf', [RenderController::class, 'printPdf'])->name('printPdf');
Route::get('/downloadPdf', [RenderController::class, 'downloadPdf'])->name('downloadPdf');
Route::get('/render', [RenderController::class, 'render'])->name('render');
Route::get('/framePdf', [RenderController::class, 'framePdf']);
Route::get('/renderPdf', [RenderController::class, 'renderPdf'])->name('renderPdf');
Route::get('/renderMsg', [RenderController::class, 'renderMsg'])->name('renderMsg');
Route::get('/validate', [RenderController::class, 'validate']);

Route::middleware(['PreventBackHistory', 'guest.redirect'])->group(function () {
// Route::middleware('auth')->group(function () {
    // Route::get('/', function() {
    //     if (!session()->has('userToken')) {
    //         return redirect()->route('login');
    //     }
    //     return app(HomeController::class)->index();
    // })->name('home');

    Route::get('/', [HomeController::class, 'index'])->name('home');
});

// Route::middleware('web')->group(function () {
//     Route::get('/{any}', function () {
//         Session::put('url.intended', url()->current());
//     })->where('any', '.*');
// });