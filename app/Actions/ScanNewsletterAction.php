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
      $temp = Newsletter::where('id', $newsletter->id)->where('scheduled_for', '<=', Carbon::now())->first();
      
      if($temp) {
        $newsletter->update([
          'scheduled_for' => Carbon::now()->addMinutes(20),
        ]);
        
          $newsletter->emails->each(function (Email $contact) {
              app(ScanEmailAccountAction::class)($contact);
          });
  
          $newsletter->update([
              'scanning_at' => Carbon::now(),
              'scheduled_for' => null,
          ]);
      }
    }
}
