<?php

namespace App\Models;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webklex\IMAP\Facades\Client;

class Imap extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function email(): BelongsTo
    {
        return $this->belongsTo(Email::class);
    }

    public function connection(): \Webklex\PHPIMAP\Client|Connection
    {
        return Client::make([
            'host'          => $this->host,
            'port'          => $this->port,
            'encryption'    => $this->protocol,
            'username'      => $this->email->email,
            'password'      => $this->password,
            'protocol'      => 'imap',
            'validate_cert' => true,
        ]);
    }
}
