<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\NewsletterStatusEnum;
use App\Models\Newsletter;
use Carbon\Carbon;
use Spatie\ModelStatus\Exceptions\InvalidStatus;

class CompleteNewsletterAction
{
    /**
     * @throws InvalidStatus
     */
    public function __invoke(Newsletter $newsletter): void
    {
        $completed = true;

        foreach ($newsletter->emails as $email) {
            if (is_null($email->pivot->status)) {
                $completed = false;
                break;
            }
        }

        if ($completed) {
            $newsletter->update([
                'completed_at' => Carbon::now(),
            ]);

            $newsletter->setStatus(NewsletterStatusEnum::Finished->name);
        }
    }
}
