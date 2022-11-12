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

        return $this->subscribed();
    }

    public function hasAvailableSenders()
    {
        if (! $subscription = $this->subscription()) {
            return false;
        }

        return $this->senders->count() < $subscription->quantity;
    }
}
