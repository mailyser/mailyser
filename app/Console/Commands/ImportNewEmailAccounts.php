<?php

namespace App\Console\Commands;

use App\Imports\EmailImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportNewEmailAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:import-csv';

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
        Excel::import(new EmailImport(), 'emails.csv', 'local');

        return Command::SUCCESS;
    }
}
