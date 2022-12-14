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
        foreach ($rows as $row) {
            if ($row['status'] !== 'deliverable' || $row['user_id'] !== 0) {
                continue;
            }

            if (Email::where('email', $row['email'])->exists()) {
                continue;
            }

            $email = Email::create([
                'type' => $row['type'],
                'email' => $row['email'],
                'reply_email' => $row['email'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'inbox_folder' => $row['inbox_folder'],
                'sent_folder' => $row['sent_folder'],
                'spam_folder' => $row['spam_folder'],
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
        }
    }
}
