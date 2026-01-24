<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {teamA} {teamB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Match DB Backup';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $teamA = $this->argument('teamA');
        $teamB = $this->argument('teamB');

        // Check if the db_backup directory exists, if not, create it
        $backupDir = storage_path('app/db_backup');
        
        if (!Storage::exists('db_backup')) {
            Storage::makeDirectory('db_backup');
        }

        $filename = "backup-" . Carbon::now()->format('Y-m-d H:i:s') . " - $teamA vs $teamB" . ".sql";
        $filePath = $backupDir . '/' . $filename;

        $mysqldumpPath = '/usr/bin/mysqldump';  // Full path to mysqldump binary (change this if needed)

		 $userName= "pitchburnersLeague";
      	 $passwd = "w4AOkP1YVsa8SxUzcdOV";
      	 $host = "127.0.0.1";
      	 $db = "pitchburnersLeague";
		//$userName= env('DB_USERNAME');
      	//$passwd = env('DB_PASSWORD');
      	//$host = env('DB_HOST');
      	//$db = env('DB_DATABASE');
      
		$command = "/usr/bin/mysqldump --user=" . escapeshellarg($userName) .
            " --password=" . escapeshellarg($passwd) .
            " --host=" . escapeshellarg($host) .
            " " . escapeshellarg($db) .
            " > " . escapeshellarg($filePath);

        $returnVar = NULL;
        $output = NULL;

      	exec($command, $output, $returnVar);

        // Output the result of the shell command
        $this->info("Command output: " . implode("\n", (array) $output));

        // Check if the backup file exists
        if (Storage::exists('db_backup/' . $filename)) {
            $info = "Backup created successfully: $filePath";
            $this->info($info);
            Log::info($info);
        } else {
            $error = "Backup failed! Command output: " . implode("\n", (array) $output);
            $this->error($error);
            Log::error($error);
        }

        return 0;
    }
}

