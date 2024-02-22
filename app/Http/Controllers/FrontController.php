<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Models\User; 
use App\Models\Newsletter;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use JeffGreco13\FilamentBreezy\Events\LoginSuccess;
use App\Filament\Resources\NewsletterResource\Widgets\NewsletterSpamReportChart;
use App\Filament\Resources\NewsletterResource\Widgets\NewsletterDeliveryReportChart;

class FrontController extends Controller
{
  public function viewNewsletter(Request $request, Newsletter $newsletter) {
    var_dump($newsletter) ;
    
    $user = User::where("id", $newsletter->user_id)->first();
    
    var_dump(  Filament::auth()->user());
    //Auth::loginUsingId($newsletter->user_id, true);
    Filament::auth()->loginUsingId($newsletter->user_id, true);
    //     Filament::auth()->login($user, true);
    //auth()->setUser($user);
    
    event(new LoginSuccess(Filament::auth()->user()));
     
    $widgets = [];
//     $widgets = array_merge($widgets, [
//       NewsletterResource\Widgets\NewsletterSpamReportChart::class,
//       NewsletterResource\Widgets\NewsletterDeliveryReportChart::class,
//     ]);
    
    
    $widget1 = new NewsletterSpamReportChart();
    $widget1->record = $newsletter;
    
    $widget2 = new NewsletterDeliveryReportChart();
    $widget2->record = $newsletter;
    
    $widgets[] = $widget1;
    $widgets[] = $widget2;
    
  
    
    return view('front.view')->with('widget1', $widget1)->with('widget2', $widget2)
    ->with('record', $newsletter)->with('widgets', $widgets);
  }
   
  
}
