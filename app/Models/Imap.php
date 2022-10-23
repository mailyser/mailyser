<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Imap extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function email(): BelongsTo
    {
        return $this->belongsTo(Email::class);
    }
}
