<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use JeffGreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'sending_address',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'admin' => 'boolean',
    ];

    /**
     * @return HasMany
     */
    public function newsletters(): HasMany
    {
        return $this->hasMany(Newsletter::class);
    }

    /**
     * @return HasMany
     */
    public function senders(): HasMany
    {
        return $this->hasMany(Sender::class);
    }

    public function canAccessFilament(): bool
    {
        return true;
    }

    public function hasAccess(): bool
    {
        if (app()->environment('local')) {
            // return true;
        }
        if($this->parent_id > 0) {
          $user = User::find($this->parent_id);
          return $user->subscribed();
        }
        return $this->subscribed();
    }

    public function hasAvailableSenders()
    {
      if (! $subscription = $this->getSubscription()) {
        return false;
      }
      
      $currentDate = Carbon::today();
      
      return $this->senders()->whereDate('created_at', $currentDate)->count() < $subscription->quantity;
      
    }
    public function hasAvailableTestMails()
    {
      if (! $subscription = $this->getSubscription()) {
        return false;
      }
      
      $currentDate = Carbon::today();
      
      return $this->newsletters()->whereDate('created_at', $currentDate)->count() < $subscription->no_of_email_tests;
    }
    
    public function profile(): HasOne
    {
      return $this->hasOne(Profile::class);
    }
    
    public function sendEmailVerificationNotification()
    {
      $this->notify(new VerifyEmailNotification);
    }
    
    public function subscriptions()
    {
      return $this->hasMany(CustomSubscription::class, $this->getForeignKey())->orderBy('created_at', 'desc');
    }
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
      'business_name'
    ];
    
    public function getBusinessNameAttribute()
    {
      return $this->profile?->new_business;
    }
    
    public function getSubscription() {
      if($this->parent_id > 0) {
        $user = User::find($this->parent_id);
        return $user->subscription();
      }
      return $this->subscription();
    }
    
    public function getAppSumoActivationId() {
      if($this->parent_id > 0) {
        $user = User::find($this->parent_id);
        return $user->app_sumo_activation_id;
      }
      return $this->app_sumo_activation_id;
    }
    
    public function getApiKey() {
      if(!$this->api_key) {
        $this->api_key = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
          // 32 bits for "time_low"
          mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
          
          // 16 bits for "time_mid"
          mt_rand( 0, 0xffff ),
          
          // 16 bits for "time_hi_and_version",
          // four most significant bits holds version number 4
          mt_rand( 0, 0x0fff ) | 0x4000,
          
          // 16 bits, 8 bits for "clk_seq_hi_res",
          // 8 bits for "clk_seq_low",
          // two most significant bits holds zero and one for variant DCE1.1
          mt_rand( 0, 0x3fff ) | 0x8000,
          
          // 48 bits for "node"
          mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
          );
        $this->save();
      }
      
      if($this->api_key) {
        return $this->api_key;
      }
    } 
}
