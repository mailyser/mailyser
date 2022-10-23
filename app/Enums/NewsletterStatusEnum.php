<?php

namespace App\Enums;

enum NewsletterStatusEnum
{
   case Draft; // can download the contacts
   case Prepared; // contacts downloaded at least 1
   case Scanning; // they sent the newsletter, and pushed the button. system is scanning
   case Finished; // all emails are scanned and we have the results. show results.

    public static function badgeColors(): array
    {
        return [
            'secondary' => NewsletterStatusEnum::Draft->name,
            'primary' => NewsletterStatusEnum::Prepared->name,
            'warning' => NewsletterStatusEnum::Scanning->name,
            'success' => NewsletterStatusEnum::Finished->name,
        ];
    }
}
