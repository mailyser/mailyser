<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\SubscriptionPlan;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Stripe;
use Stripe\Subscription;
use Stripe\StripeClient;
use App\Notifications\RegisterSubUserNotification;
use Illuminate\Support\Facades\Auth;
use App\Models\AppSumoActivation;

class SetupAccountForm extends Component implements HasForms
{
    use InteractsWithForms;
    
    public User $user;
    public $stripe_token = null;
    public $stripePublicKey = null;
    public $isLoading = false;
    public $subscription_plans = NULL;
    public $user_plan = NULL;
    public $intent = NULL;
    public $organization = NULL;
    public function mount()
    {
        $user = auth()->user();
        $this->user = $user;
        
        $this->subscription_plans = SubscriptionPlan::limit(3)->get();
        
        $this->organization = User::where('id', '!=', $user->id)->whereNull('parent_id')->whereAdmin(false)->get();
        
        $this->stripePublicKey = \config('services.stripe.secret');
        //$this->intent = $this->user->createSetupIntent();
    }
    
    public function render()
    {
        return view('livewire.setup-account-form');
    }
    
    public function setupAccount($formData)
    {
        
        $this->isLoading = true;
        // $stripetoken = (!empty($formData['stripe_token'])? $formData['stripe_token'] : NULL );
        
        unset($formData['stripe_token']);
        unset($formData['billingaddress']);
        unset($formData['zipcode']);
        
        if(!empty($formData['business_type']) && $formData['business_type'] == 'existing_business'){
            
            $this->user->profile()->updateOrCreate(['user_id' => $this->user->id],$formData);
            $this->user->parent_id = $formData['existing_business'];
            $this->user->is_accountcomplete = 1;
            $this->user->save();
            $company = User::find($this->user->parent_id);
            $company->notify(new RegisterSubUserNotification($this->user,$company));
            
            Auth::logout();
            
            $this->isLoading = false;
            
            return redirect()->to('/setup-account-thankyou');
        }
        
        unset($formData['existing_business']);
        
        //$this->createCustomerstripe($stripetoken);
        
        $plan = SubscriptionPlan::where('stripe_id',$formData['plan_id'])->first();
        
        $this->user_plan = $plan;
        
        Stripe\Stripe::setApiKey($this->stripePublicKey);
        $subscription = Subscription::retrieve($formData['subscriptionId']);
        
        $this->addUserSubscription($subscription);
        
        $this->user->profile()->updateOrCreate(['user_id' => $this->user->id],$formData);
        
        $this->user->is_accountcomplete = 1;
        $this->user->save();
        
        $this->isLoading = false;
        
        return redirect()->to('/setup-account-thankyou');
        
    }
    
    
    public function retrievePlans()
    {
        $stripe = new StripeClient($this->stripePublicKey);
        $plansraw = $stripe->plans->all();
        $plans = $plansraw->data;
        
        foreach($plans as $plan) {
            $prod = $stripe->products->retrieve(
                $plan->product,[]
                );
            $plan->product = $prod;
        }
        return $plans;
    }
    
    public function planSubscription ($customer, $price,$meta =[])
    {
        $stripe = new StripeClient($this->stripePublicKey);
        
        $subscription = $stripe->subscriptions->create([
            
            'customer' => $customer->stripe_id,
            
            'items' => [
                
                ['price' => $price->stripe_id],
            ],
            
            'metadata' => $meta
            
        ]);
        
        $subscription = $this->addUserSubscription($subscription);
        
        return $subscription;
    }
    
    public function createCustomerstripe($tokan)
    {
        if (is_null($this->user->stripe_id)) {
            Stripe\Stripe::setApiKey($this->stripePublicKey);
            $customer = \Stripe\Customer::create([
                
                'name' => $this->user->name,
                'address' => [
                    'line1' => 'New York',
                    'postal_code' => '10001',
                    'city' => 'New York',
                    'state' => 'New York',
                    'country' => 'USA',
                ],
                
                'email' => $this->user->email,
                'source' => $tokan
            ]);
            
            $this->user->stripe_id = $customer->id;
            
            $this->user->save();
        }
    }
    
    public function addUserSubscription ($stripeSubscription)
    {
        $firstItem = $stripeSubscription->items->first();
        
        $isSinglePrice = $stripeSubscription->items->count() === 1;
        
        $subscription = $this->user->subscriptions()->create([
            'name' => 'default',
            'stripe_id' => $stripeSubscription->id,
            'stripe_status' => $stripeSubscription->status,
            'stripe_price' => $isSinglePrice ? $firstItem->price->id : null,
            'quantity' => $this->user_plan->no_of_email_domains, //$isSinglePrice ? ($firstItem->quantity ?? null) : null,
            'trial_ends_at' =>  null,
            'ends_at' => null,
            'no_of_email_tests' => $this->user_plan->no_of_email_tests,
            'no_of_email_domains' => $this->user_plan->no_of_email_domains,
        ]);
        
        foreach ($stripeSubscription->items as $item) {
            $subscription->items()->create([
                'stripe_id' => $item->id,
                'stripe_product' => $item->price->product,
                'stripe_price' => $item->price->id,
                'quantity' => $item->quantity ?? null,
            ]);
        }
        
        return $subscription;
    }
    
}
