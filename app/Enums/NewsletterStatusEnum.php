<?php

namespace App\Enums;

enum NewsletterStatusEnum
{
   case Draft; // can download the contacts
   case Ready;
   case Scanning; // they sent the newsletter, and pushed the button. system is scanning
   case Finished; // all emails are scanned and we have the results. show results.
}
