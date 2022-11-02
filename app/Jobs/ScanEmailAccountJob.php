<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\Newsletter;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;

class ScanEmailAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected Email $email,
        protected Newsletter $newsletter
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws ConnectionFailedException
     */
    public function handle()
    {
        $connection = $this->email->imap->connection();

        try {
            $connection->connect();
        } catch (Exception $e) {
            $this->email->newsletters()->updateExistingPivot($this->newsletter, [
                'status' => 'skipped',
                'found_at_mailbox' => $e->getMessage(),
            ]);

            return;
        }

        $landedIn = null;

        foreach ($connection->getFolders(false) as $folderPath) {
            $folderInstance = $connection->getFolderByPath($folderPath);

            if (! $folderInstance) {
                continue;
            }

            try {
                $messages = $folderInstance->query()
                    ->from($this->newsletter->email)
                    ->since($this->newsletter->created_at)
                    ->text($this->newsletter->keyword)
                    ->get();
            } catch (Exception $e) {
                continue;
            }

            if (! $messages || ! count($messages)) {
                continue;
            }

            foreach ($messages as $message) {
                if ($folderInstance->path === $this->email->spam_folder) {
                    $landedIn = 'spam';
                } else {
                    $landedIn = $folderInstance->name;
                }

                try {
                    $message->setFlag('Seen');
                    $message->delete();
                } catch (Exception $e) {
//                    info('found and deleting');
                }

                break 2;
            }
        }

        // update pivot
        $this->email->newsletters()->updateExistingPivot($this->newsletter, [
            'status' => 'scanned',
            'found_at_mailbox' => $landedIn ?? null,
        ]);
    }
}
