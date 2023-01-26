<?php

namespace App\Imports;

use App\Models\Email;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmailImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $rows
     * @return void
     */
    public function collection(Collection $rows)
    {
       // var_dump($rows);
        
        foreach ($rows as $row) {
            
           // var_dump($row);
           // die;
            
            if ($row['status'] !== 'deliverable') { // || $row['user_id'] !== 0) {
                var_dump('stop 1');
                
                continue;
            }

            if (Email::where('email', $row['email'])->exists()) {
                var_dump('stop 2');
                
                continue;
            }

            $email = Email::create([
                'type' => $row['type'],
                'email' => $row['email'],
                'reply_email' => $row['email'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'inbox_folder' => isset($row['inbox_folder']) ? $row['inbox_folder'] : 'INBOX',
                'sent_folder' => isset($row['sent_folder']) ? $row['sent_folder'] : '[Gmail]/Sent Mail',
                'spam_folder' => isset($row['spam_folder']) ? $row['spam_folder'] : '[Gmail]/Spam',
            ]);

            $email->smtp()->create([
                'host' => $row['smtp_host'],
                'port' => $row['smtp_port'],
                'protocol' => $row['smtp_protocol'],
                'username' => $row['email'],
                'password' => $row['app_password'],
            ]);

            $email->imap()->create([
                'host' => $row['imap_host'],
                'port' => $row['imap_port'],
                'protocol' => $row['imap_protocol'],
                'username' => $row['email'],
                'password' => $row['app_password'],
            ]);
            var_dump('created');
        }
    }
}
