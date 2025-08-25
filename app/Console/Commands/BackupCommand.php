<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Backup\Tasks\Backup\BackupJob;

class BackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the backup process';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting backup...');

        $backupJob = new BackupJob();
        $backupJob->run();

        $this->info('Backup completed successfully!');
    }
}
