<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScrappingPenelitian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrapping-penelitian {--type_scrap=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // trigger executable
        $this->info('Starting scrapping penelitian...');
        // get file.json inside storage path

        $typeScrap = $this->option('type_scrap');

        $filePath = storage_path('app/scrapping/'.$typeScrap.'.json');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return 1; // Return a non-zero exit code for error
        }

        // trigger the executeable name webscrapper-windows.exe or webscrapper-linux depending on the OS
        $executable = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'webscrapper-windows.exe' : 'webscrapper-linux';

        $command = $executable . ' ' . escapeshellarg($filePath) . ' "Adhi Prasnowo" '." 2>&1";

        // get the output
        $output = shell_exec($command);

        if ($output === null) {
            $this->error('Failed to execute the command.');
            return 1; // Return a non-zero exit code for error
        }

        $this->info('Scrapping completed successfully.');
        $this->info('Output: ' . $output);

    }
}
