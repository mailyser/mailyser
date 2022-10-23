<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Email extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function smtp(): HasOne
    {
        return $this->hasOne(Smtp::class);
    }

    public function imap(): HasOne
    {
        return $this->hasOne(Imap::class);
    }
}
