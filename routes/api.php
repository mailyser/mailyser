<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\SenderAddressController;
use App\Http\Controllers\EmailTestController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::match(['get', 'post'], '/partner/token', [ApiController::class, 'generatePartnerToken'])->name('api.generate.partner.token');
Route::match(['get', 'post'], '/partner/notification', [ApiController::class, 'partnerNotification'])->name('api.partner.notification');
 
Route::get('/fix-record', [ApiController::class, 'fixRecord'])->name('fixRecord');

Route::get('/view-newsletter/{newsletter}', [ApiController::class, 'viewNewsletter'])->name('api.view.newsletter');

Route::get('/sender-address/list', [SenderAddressController::class, 'list'])->name('api.sender.address.list');
Route::post('/sender-address/create', [SenderAddressController::class, 'create'])->name('api.sender.address.create');

Route::get('/email-test/list', [EmailTestController::class, 'list'])->name('api.email.test.list');
Route::post('/email-test/create', [EmailTestController::class, 'create'])->name('api.email.test.create');
Route::get('/email-test/details', [EmailTestController::class, 'details'])->name('api.email.test.details');
Route::post('/email-test/start-scan', [EmailTestController::class, 'startScan'])->name('api.email.test.start');

