<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request; 
use App\Models\User; 
use App\Models\Newsletter;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use App\Models\Sender;

class SenderAddressController extends ApiController
{
  
  public function list(Request $request) {
    $this->validateApiKey($request);
    
    $senders = Sender::where("user_id", $this->currentUser->id)->where("enabled", 1)->get();
    
    $resp = [];
    $resp['status'] = 1;
    $resp['sender_address'] = [];
    
    foreach($senders as $sender) {
      $resp['sender_address'][] = ['id' => $sender->id, 'email_address' => $sender->email_address];
    }
     
    return response()->json($resp, 200);
  }
   
  public function create(Request $request) {
    $this->validateApiKey($request);
    
    $resp = [];
    $resp['status'] = 0;
    
    if($request->has('email_address') && $request->email_address) {
    
      Filament::auth()->loginUsingId($this->currentUser->id, true);
      
      
      $sender = new Sender();
      $sender->user_id = $this->currentUser->id;
      $sender->email_address = $request->email_address;
      $sender->enabled = 1;
      $sender->save();
      
      $resp['status'] = 1;
      $resp['id'] = $sender->id;
            
    } else {
      $resp['message'] = "Missing 'Email Address'";
      
    }
    
    return response()->json($resp, 200);
    
  
  }
  
}
