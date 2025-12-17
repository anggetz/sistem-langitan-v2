<?php

namespace App\Console\Commands;

use App\Models\PublikasiJobStatus;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncPenelitianScopus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-penelitian-scopus {--id_pengguna=} {--websocket_topic=}';

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
        $startTime = microtime(true);

        $idPengguna = $this->option('id_pengguna');
        $websocketTopic = $this->option('websocket_topic');

        $jobStatus = PublikasiJobStatus::create([
            "WEBSOCKET_TOPIC" => $websocketTopic,
            "JOB_STATUS" => "ON PROGRESS",
            "PARAMETER" => "'".$idPengguna."' '".$websocketTopic."'",
            "ID_PENGGUNA" => $idPengguna
        ]);

        Log::info("Running scrap!!");
        //

        try {
            sleep(10);
            $response = Http::post(config('app.ws_hook_address') . '/broadcast', [ // Changed endpoint to /broadcast
                'topic' => $websocketTopic, // Use the topic for the specific presensi
                'message' => json_encode([
                    'status' => 'success',
                ]),
            ]);
            $endTime = microtime(true);
            $processTime = $endTime - $startTime; // in seconds (float)
            $jobStatus->JOB_STATUS = "SUCCESS";
            $jobStatus->PROCESS_TIME = $processTime;
            $jobStatus->save();
            Log::error("success");
        } catch (Exception $err) {
            $endTime = microtime(true);
            $processTime = $endTime - $startTime; // in seconds (float)
            $jobStatus->JOB_STATUS = "FAILED";
            $jobStatus->PROCESS_TIME = $processTime;
            $jobStatus->save();
            $response = Http::post(config('app.ws_hook_address') . '/broadcast', [ // Changed endpoint to /broadcast
                'topic' => $websocketTopic, // Use the topic for the specific presensi
                'message' => json_encode([
                    'status' => 'failed',
                    'message' => $err->getMessage()
                ]),
            ]);

            Log::error("error " + $err->getMessage());
        }
    }
}
