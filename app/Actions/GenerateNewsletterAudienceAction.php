<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Email;
use App\Models\Newsletter;

class GenerateNewsletterAudienceAction
{
    public function __invoke(Newsletter $newsletter): array
    {
        $audience = Email::query()
            ->select(array_merge(Newsletter::audienceFields(), ['id']))
            ->inRandomOrder()
            ->limit(app()->environment('production') ? 1000 : 10)
            ->get();

        $newsletter->emails()->sync($audience);

        return $audience->toArray();
    }
}
