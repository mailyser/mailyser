<?php

namespace App\Console\Commands;

use App\Imports\EmailImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Email;
use App\Models\Newsletter;
use App\Actions\ScanEmailAccountAction;
use Illuminate\Support\Carbon;

class CheckEmailConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:check-connection';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import new email accounts from a csv file.';
    
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      
      $newsletters = Newsletter::where("has_mail_tester", 1)->get();
      foreach($newsletters as $index => $newsletter) {
        var_dump($index.'/'.count($newsletters));
        $newsletter->getMailtesterData();
        sleep(3);
      }
      
        $newsletter= Newsletter::find(107);
        
       $newsletter->emails->each(function (Email $contact) {
         app(ScanEmailAccountAction::class)($contact);
       });
         
         $newsletter->update([
           'scanning_at' => Carbon::now(),
           'scheduled_for' => null,
         ]);
       die('DONE');
       
        $ids = [3054, 3055];
        
        foreach($ids as $id) {
            $email = Email::find($id);
            
            var_dump($email->email);
            $connection = $email->imap->connection();
            
            try {
                $connection->checkConnection();
                
                $folderList = [];
                foreach ($connection->getFolders(false) as $folder) {
                    $folderList[] = $folder->name;
                    /*
                    $messages = $folder->query()
                    ->from($this->newsletter->sender->email_address)
                    ->since($this->newsletter->created_at->clone()->subDay()->format('d.m.Y'))
                    ->text($this->newsletter->keyword)
                    ->limit(1)
                    ->get();
                    
                     foreach ($messages as $message) {
                        if ($message->getFolderPath() === $this->email->spam_folder) {
                            $this->landedIn = 'Spam';
                        } else {
                             $folder = $message->getFolder();
                            $this->landedIn = $folder->name;
                        }
                    }
                    */
                }
                
                var_dump(implode(', ', $folderList));
            } catch (\Exception $e) {
                var_dump($e->getMessage());
            }
            
         }
        
         
        return Command::SUCCESS;
    }
}
