<?php

namespace App\Console\Commands;

use App\Actions\CompleteNewsletterAction;
use App\Models\Newsletter;
use Illuminate\Console\Command;
use Spatie\ModelStatus\Exceptions\InvalidStatus;

class CompleteScannedNewslettersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletters:complete-scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete newsletters being scanned.';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws InvalidStatus
     */
    public function handle()
    {
        Newsletter::query()
            ->with('user')
            ->whereNotNull('scanning_at')
            ->whereNull('completed_at')
            ->get()
            ->filter(fn (Newsletter $newsletter) => $newsletter->user->hasAccess())
            ->each(fn (Newsletter $newsletter) => app(CompleteNewsletterAction::class)($newsletter));

        return Command::SUCCESS;
    }
}
