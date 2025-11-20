<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncPenelitianScopus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-penelitian-scopus {--id_dosen=}';

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
        $idDosen = $this->option('id_dosen');
        //

        try {
            sleep(10);
            $response = Http::post(env('WS_HOOK_ADDRESS') . '/broadcast', [ // Changed endpoint to /broadcast
                'topic' => 'sync-' . $idDosen, // Use the topic for the specific presensi
                'message' => json_encode([
                    'status' => 'success',
                ]),
            ]);
        } catch (Exception $err) {
            $response = Http::post(env('WS_HOOK_ADDRESS') . '/broadcast', [ // Changed endpoint to /broadcast
                'topic' => 'sync-' . $idDosen, // Use the topic for the specific presensi
                'message' => json_encode([
                    'status' => 'failed',
                    'message' => $err->getMessage()
                ]),
            ]);
        }
    }
}
