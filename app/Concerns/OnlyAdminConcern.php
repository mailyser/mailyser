<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;

trait OnlyAdminConcern
{
    public static function can(string $action, ?Model $record = null): bool
    {
        return auth()->user()->admin;
    }
}
