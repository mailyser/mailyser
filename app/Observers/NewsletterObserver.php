<?php

namespace App\Observers;

use App\Enums\NewsletterStatusEnum;
use App\Models\Newsletter;
use Illuminate\Support\Str;
use Spatie\ModelStatus\Exceptions\InvalidStatus;

class NewsletterObserver
{
    public function creating(Newsletter $newsletter)
    {
        $newsletter->uuid = Str::uuid()->toString();
        $newsletter->user()->associate(auth()->user());
    }

    /**
     * @throws InvalidStatus
     */
    public function created(Newsletter $newsletter)
    {
        $newsletter->setStatus(NewsletterStatusEnum::Draft->name);
    }
}
