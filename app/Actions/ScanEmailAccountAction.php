<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\ScanEmailAccountJob;
use App\Models\Email;
use App\Models\Newsletter;

class ScanEmailAccountAction
{
    public function __invoke(Email $email): void
    {
        if (filled($email->pivot->status)) {
            return;
        }

        ScanEmailAccountJob::dispatch($email, Newsletter::find($email->pivot->newsletter_id))
            ->onQueue('scans');
    }
}
