<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'active' => 'bool',
        'features' => 'array',
    ];

    public function upgradePlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'id', 'plan_upgrade_id');
    }
}
