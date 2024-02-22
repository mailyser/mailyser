<?php

use Illuminate\Support\Facades\Route;
use App\Http\Requests\EmailVerifyRequest;
use Illuminate\Routing\RouteRegistrar;
use App\Models\User;
use App\Notifications\UserApprovalNotification;
use App\Http\Controllers\FrontController;
 

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

Route::get('/email/verify/{id}/{hash}', function (EmailVerifyRequest $request) {
  
  $request->fulfill();
  
  return redirect('/setup-account');
  
})->middleware([])->name('verification.verify');

// Route::get('setup-account-thankyou',App\Filament\Pages\SetupAccountThankyou::class);

Route::get('subuser-approval/{user}/{status}',function ($userid, $status) {
  
  $user = User::findOrFail($userid);
  $user->parent_approved = $status;
  $user->save();
  
  $company = User::findOrFail($user->parent_id);
  
  $user->notify(new UserApprovalNotification($user,$company));
  
  return redirect('/');
})->name('sub-user-approval-link');


Route::get('/view-newsletter/{newsletter}', [FrontController::class, 'viewNewsletter'])->name('api.view.newsletter');