<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\AppSumoActivation;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Newsletter;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
  public $currentUser = false;
  
  public function viewNewsletter(Request $request, Newsletter $newsletter) {
    var_dump($newsletter) ;
    
    $user = User::where("id", $newsletter->user_id)->first();
    
    var_dump(  auth()->user());
    Auth::loginUsingId($newsletter->user_id, true);
    Filament::auth()->loginUsingId($newsletter->user_id, true);
//     Filament::auth()->login($user, true);
    auth()->setUser($user);
    var_dump(  auth()->user());
    die;
  }
  
  public function getInputParameters(){
    $data = [];
    
    parse_str(file_get_contents( 'php://input' ), $data);
    
    $data = array_merge($data, $_POST, $_REQUEST);
    return $data;
  }
  
  public function validateApiKey(Request $request) {
         
    $apiKey = $request->api_key;
     
    $user = User::where('api_key', $apiKey)->first();
    if($user) {
      $this->currentUser = $user;
    } else {
      echo json_encode(['status' => 0, 'message' => 'Invalid API Key']);
      die;
    } 
  }
  
    public function generatePartnerToken(Request $request): JsonResponse
    {
        $status = 0;
        $error = "Invalid Credentials";
        
        $username =  isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
        $secret =  isset($_REQUEST['password']) ? $_REQUEST['password'] : '';
        
        if(config('app.app_sumo_user') == $username
            && config('app.app_sumo_key') == $secret) {
                
                $status = 1;
                $error = "";
                
                $key = config('app.jwt_key');
                
                $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
                
                // Create token payload as a JSON string
                $payload = json_encode(['time' => strtotime('+2 hours')]);
                
                // Encode Header to Base64Url String
                $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
                
                // Encode Payload to Base64Url String
                $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
                
                // Create Signature Hash
                $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $key, true);
                
                // Encode Signature to Base64Url String
                $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
                
                // Create JWT
                $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
                
                
                return response()->json(['access' => $jwt], 200);
                
            }
        
        
        return response()->json(['status' => $status, 'error' => $error], 401);
    }
    
    private function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = getallheaders();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    /**
     * get access token from header
     * */
    private function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
    
    public function partnerNotification(Request $request): JsonResponse
    {
        $status = 0;
        $error = "Invalid";
        
        $headers = getallheaders();
        if (!array_key_exists('Authorization', $headers)) {
             
            return response()->json(['status' => $status, 'error' => $error], 401);
            
        }
        $jwt = $this->getBearerToken();
        
        if ($jwt === null) {
             
            return response()->json(['status' => $status, 'error' => "Token is missing"], 401);
            
        }
        
         
        
        if($jwt) {
            
            $payLoad = (json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $jwt)[1]))), true) );
            
            if(isset($payLoad['time']) && $payLoad['time'] > strtotime('now')) {
                $key = config('app.jwt_key');
                
                $payLoad1 = json_encode($payLoad);
                
                
                $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payLoad1));
                
                
                $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
                 
                // Encode Header to Base64Url String
                $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
                
                
                $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $key, true);
                
                $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
                
                // Create JWT
                $jwtNew = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
                 
                
                if($jwt == $jwtNew) {
                    
                    $status = 1;
                    $error = "";
                    
                    $action = $_REQUEST['action'];
                    $plan_id = $_REQUEST['plan_id'];
                    $uuid = $_REQUEST['uuid'];
                    $activation_email = $_REQUEST['activation_email'];
                    $invoice_item_uuid = isset($_REQUEST['invoice_item_uuid']) ? $_REQUEST['invoice_item_uuid'] : '';
                    
                    if($action == 'activate') {
                        
                        $appSumoActivation = AppSumoActivation::where("email", $activation_email)->first();
                        
                        if(!$appSumoActivation) {
                            
                            $appSumoActivation = new AppSumoActivation();
                            $appSumoActivation->email = $activation_email;
                            $appSumoActivation->plan_id= $plan_id;
                            $appSumoActivation->uuid = $uuid;
                            $appSumoActivation->invoice_item_uuid = $invoice_item_uuid;
                            $appSumoActivation->save();
                            
                            
                            $redirect_url = "https://app.mailyser.io/register?a=".md5($appSumoActivation->id)."&aa=".base64_encode($appSumoActivation->id)."&source=appsumo";
                            
                            return response()->json(['message' => 'product activated', 'redirect_url' => $redirect_url], 201);
                        }else{
                            return response()->json(['message' => 'duplicate email', 'redirect_url' => ''], 401);
                            
                        }
                        
                    } else if($action == 'enhance_tier') {
                        //change to higher plan
                        
                        $appSumoActivation = AppSumoActivation::where('email', $activation_email)->first();
                        if($appSumoActivation) {
                            
                            $user = User::where("app_sumo_activation_id", $appSumoActivation->id)->first();
                            if($user) {
                                $this->addAppSumoSubscription($user, $plan_id);
                                $appSumoActivation->plan_id= $plan_id;
                                $appSumoActivation->save();
                                return response()->json(['message' => 'product enhanced'], 200);
                            }
                        }
                        
                        
                    } else if($action == 'reduce_tier') {
                        //change to lower plan
                        
                        $appSumoActivation = AppSumoActivation::where('email', $activation_email)->first();
                        if($appSumoActivation) {
                            
                            $user = User::where("app_sumo_activation_id", $appSumoActivation->id)->first();
                            if($user) {
                                $this->addAppSumoSubscription($user, $plan_id);
                                $appSumoActivation->plan_id= $plan_id;
                                $appSumoActivation->save();
                                return response()->json(['message' => 'product reduced'], 200);
                            }
                        }
                        
                    } else if($action == 'refund') {
                        //end the subscription
                        
                       
                        
                        $appSumoActivation = AppSumoActivation::where('email', $activation_email)->first();
                        if($appSumoActivation) {
                            
                            $user = User::where("app_sumo_activation_id", $appSumoActivation->id)->first();
                            if($user) {
                                $subscription = Subscription::where("user_id", $user->id)->first();
                                
                                if($subscription) {
                                    $subscription->delete();
                                }
                                return response()->json(['message' => 'product refunded'], 200);
                            }
                        }
                        
                        
                    }
                    
                    return response()->json(['status' => $status, 'error' => $error], 200);
                    
                }
                
            }
        }
        
        return response()->json(['status' => $status, 'error' => $error], 401);
        
    }
    
    public function fixRecord() {
      $user = User::find(142);
      $this->addAppSumoSubscription($user, 'mailyser_tier1');
    }
    
    
    private function addAppSumoSubscription($user, $appSumoTierId){
        
        $subscription = Subscription::where("user_id", $user->id)->first();
         
        if($subscription) {
            foreach($subscription->items() as $item) {
                if($item->subscription_id == $subscription->id)
                    $item->delete();
            }
            
            $subscription->delete();
        }
        
        $subscriptionPlan = SubscriptionPlan::where('app_sumo_tier_id', $appSumoTierId)->first();
        
        $subscription = $user->subscriptions()->create([
            'name' => 'default',
            'stripe_id' => $subscriptionPlan->app_sumo_tier_id.'.'.strtotime('now'),
            'stripe_status' => 'active',
            'stripe_price' => 'price_'.$subscriptionPlan->app_sumo_tier_id,
            'quantity' => $subscriptionPlan->no_of_email_domains, //$isSinglePrice ? ($firstItem->quantity ?? null) : null,
            'trial_ends_at' =>  null,
            'ends_at' => null,
            'no_of_email_tests' => $subscriptionPlan->no_of_email_tests,
            'no_of_email_domains' => $subscriptionPlan->no_of_email_domains,
        ]);
        $subscription->items()->create([
            'stripe_id' => $subscriptionPlan->app_sumo_tier_id.'.'.strtotime('now'),
            'stripe_product' => $subscriptionPlan->app_sumo_tier_id,
            'stripe_price' => 'price_'.$subscriptionPlan->app_sumo_tier_id,
            'quantity' => 1,
        ]);
        
        return $subscription;
    }
    
}
