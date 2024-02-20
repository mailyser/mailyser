<?php

namespace App\Jobs;

use App\Models\Email;
use App\Models\Newsletter;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use App\Models\NewsletterSpamScore;

class ScanEmailAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?string $landedIn = null;

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
        /** @var \Webklex\PHPIMAP\Client $connection */
        $connection = $this->email->imap->connection();

        try {
            $connection->checkConnection();
        } catch (Exception $e) {
            $this->markAsFailed($e);

            return;
        }

        $hasNewsletterSpam = false;
        
        /** @var \Webklex\PHPIMAP\Folder $folder */
        foreach ($connection->getFolders(false) as $folder) {
            $messages = [];
            if($this->newsletter->sender) {
              $messages = $folder->query()
                ->from($this->newsletter->sender->email_address)
                ->since($this->newsletter->created_at->clone()->subDay()->format('d.m.Y'))
                ->text($this->newsletter->keyword)
                ->limit(1)
                ->get();
            }
            /** @var \Webklex\PHPIMAP\Message $message */
            foreach ($messages as $message) {
                if ($message->getFolderPath() === $this->email->spam_folder) {
                    $this->landedIn = 'Spam';
                } else {
                    /** @var \Webklex\PHPIMAP\Folder $folder */
                    $folder = $message->getFolder();
                    if($folder)
                      $this->landedIn = $folder->name;
                }
                
                $html = isset($message->bodies['html']) ? $message->bodies['html'] : '';
                $full_content = $message->header->raw.''.$html;
                if($this->landedIn && !$hasNewsletterSpam) {
                    $newsLetterSpamScore = NewsletterSpamScore::where("newsletter_id", $this->newsletter->id)->first();
                    if(!$newsLetterSpamScore) {
                        $newsLetterSpamScore = new NewsletterSpamScore();
                        $newsLetterSpamScore->newsletter_id = $this->newsletter->id;
                        $newsLetterSpamScore->full_content = $full_content;
                        $newsLetterSpamScore->save();
                        $hasNewsletterSpam = true;
                    }
                }
                
            }
        }

        $this->email->newsletters()->updateExistingPivot($this->newsletter, [
            'status' => 'scanned',
            'found_at_mailbox' => $this->landedIn ?? 'Not found',
        ]);
    }

    public function fail($exception = null)
    {
        $this->markAsFailed($exception);
    }

    public function markAsFailed(Exception $e): void
    {
        $this->email->newsletters()->updateExistingPivot($this->newsletter, [
            'status' => 'skipped',
            'error' => $e->getMessage(),
        ]);
    }
}
