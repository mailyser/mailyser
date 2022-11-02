<?php

namespace App\Console\Commands;

use App\Actions\ScanNewsletterAction;
use App\Models\Newsletter;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScanPendingNewslettersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletters:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan ready newsletters that are pending.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Newsletter::query()
            ->with('user')
            ->whereNull('scanning_at')
            ->where('scheduled_for', '<=', Carbon::now())
            ->get()
            ->filter(fn (Newsletter $newsletter) => $newsletter->user->subscribed())
            ->each(fn (Newsletter $newsletter) => app(ScanNewsletterAction::class)($newsletter));

        return Command::SUCCESS;
    }
}
