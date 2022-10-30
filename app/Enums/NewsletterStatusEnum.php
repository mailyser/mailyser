<?php

namespace App\Enums;

enum NewsletterStatusEnum
{
    case Draft; // can download the contacts
    case Waiting; // contacts downloaded, waiting for campaign to be sent
    case Sent; // campaign sent
    case Scanning; // they sent the newsletter, and pushed the button. system is scanning
    case Finished; // all emails are scanned and we have the results. show results.

    public static function canDownloadContacts(string $status): bool
    {
        return in_array($status, [
            NewsletterStatusEnum::Draft->name,
            NewsletterStatusEnum::Waiting->name,
        ]);
    }
}
