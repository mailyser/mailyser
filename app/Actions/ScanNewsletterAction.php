<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Email;
use App\Models\Newsletter;
use Carbon\Carbon;

class ScanNewsletterAction
{
    public function __invoke(Newsletter $newsletter): void
    {
        $newsletter->emails->each(function (Email $contact) {
            app(ScanEmailAccountAction::class)($contact);
        });

        $newsletter->update([
            'scanning_at' => Carbon::now(),
            'scheduled_for' => null,
        ]);
    }
}
