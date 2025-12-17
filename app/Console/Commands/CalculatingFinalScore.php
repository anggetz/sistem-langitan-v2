<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use App\Models\MahasiswaStatus;
use App\Models\NilaiMk;
use App\Models\PengambilanMk;
use App\Models\PeraturanNilai;
use App\Models\Semester;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CalculatingFinalScore extends Command
{
    /**
     * the argument is id_kelas_mk
     */
    protected $argument = 'id_kelas_mk';


    /**
     * The name and signature of the console command.
     *
     * @var string
     * id_kelas_mk is the id of the class
     */
    protected $signature = 'app:calculating-final-score {--id_kelas_mk=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for calculating the final score of collague students';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // get nilai mk based on id_kelas_mk
        $idKelasMk = $this->option('id_kelas_mk');

        if (!$idKelasMk) {
            return $this->error('The id_kelas_mk option is required.');
        }

        // get the pengaturan nilai
        $peraturanNilai = PeraturanNilai::with('standardNilai')->where('id_perguruan_tinggi', config('app.id_perguruan_tinggi_default'))->get();
        // Validate the argument
        if (!$idKelasMk) {
            $this->error('The id_kelas_mk argument is required.');
            return 1; // Return a non-zero exit code for error
        }

        // group by id mhs
        $nilaiMk = NilaiMk::with(['komponenMk', 'pengambilanMk'])
            ->whereHas('pengambilanMk', function ($query) use ($idKelasMk) {
                $query->where('id_kelas_mk', $idKelasMk);
                $query->where('id_semester', Semester::aktif()->id_semester);
            })
            ->get()->groupBy('id_mhs');


        if ($nilaiMk->isEmpty()) {
            $this->error('No records found for the provided id_kelas_mk.');
            return 1; // Return a non-zero exit code for error
        }

        $mk = null;

        $totalScoreMhs = [];
        // // foreach the nilai mk and do logic calculation inside of loop
        foreach ($nilaiMk as $idMhs => $nilai) {

            $totalScorePerKomponen = [];

            if (empty($totalScoreMhs[$idMhs])) {
                $totalScoreMhs[$idMhs] = [
                    'nilai' => 0,
                    'bobot' => 0,
                    'pengambilan_mk' => null,
                    'id_semester' => null,
                ];
            }


            // foreach the nilai
            foreach ($nilai as $nilaiItem) {
                $komponenMk = $nilaiItem->komponenMk;
                $totalScoreMhs[$idMhs]['pengambilan_mk'] = $nilaiItem->pengambilanMk;

                if (empty($komponenMk)) {
                    continue;
                }

                // calculate
                $nilaiPerComponent = $nilaiItem->besar_nilai_mk;

                if (empty($totalScorePerKomponen[$komponenMk->nm_komponen_mk])) {
                    $totalScorePerKomponen[$komponenMk->nm_komponen_mk] = [
                        'nilai' => 0,
                        'bobot' => $komponenMk->persentase_komponen_mk,
                    ];
                }

                $totalScorePerKomponen[$komponenMk->nm_komponen_mk]['nilai'] += $nilaiPerComponent;
            }

            // Calculate the final score for each component
            foreach ($totalScorePerKomponen as $komponen => $data) {
                $nilaiKomponen = $data['nilai'];
                $bobot = $data['bobot'];

                // Calculate the weighted score for the component
                $weightedScore = $nilaiKomponen * ($bobot / 100);

                // Store the final score for the component
                $totalScoreMhs[$idMhs]['nilai'] += $weightedScore;
            }

            MahasiswaStatus::where(
                [
                    'id_mhs' => $idMhs,
                    'id_semester' => $totalScoreMhs[$idMhs]['pengambilan_mk']->id_semester ?? null,
                ]
            )->update([
                'komponens' => json_encode($totalScorePerKomponen),
            ]);
        }

        // foreach to store the final score in the database
        $dataToUpdate = [];

        foreach ($totalScoreMhs as $idMhsTotalScore => $finalScore) {
            $peraturanNilaiMinMax = $peraturanNilai->where('nilai_min_peraturan_nilai', '<=',  $finalScore['nilai'])
                ->where('nilai_max_peraturan_nilai', '>=',  $finalScore['nilai'])->first();

            array_push($dataToUpdate, [
                'id_mhs' => $idMhsTotalScore,
                'id_kelas_mk' => $idKelasMk,
                'id_pengambilan_mk' => $finalScore['pengambilan_mk']->id_pengambilan_mk ?? null,
                'fd_nilai_angka' => $finalScore['nilai'],
                'fd_nilai_huruf' =>  $peraturanNilaiMinMax ? $peraturanNilaiMinMax->standardNilai->nm_standar_nilai : '',
                'nilai_angka' => $finalScore['nilai'],
                'nilai_huruf' =>  $peraturanNilaiMinMax ? $peraturanNilaiMinMax->standardNilai->nm_standar_nilai : '',
            ]);
            // nilai huruf is based on the peraturan nilai kosong bila tidak ada
        }

        // Log::info($dataToUpdate);
        // return 1;

        PengambilanMk::upsert(
            $dataToUpdate,
            ['id_pengambilan_mk', 'id_mhs'], // Unique keys to check for duplicates
            ['fd_nilai_angka', 'fd_nilai_huruf', 'nilai_huruf', 'nilai_angka'] // Columns to update if a duplicate is found
        );

        // call each mhs to recalculate ips and ipk
        foreach ($totalScoreMhs as $idMhs => $finalScore) {
            // call the command calculate ips by mahasiswa using queue
            Log::info("Calculating IPS for Mahasiswa ID: $idMhs");

            Artisan::queue('app:calculating-ips-by-mahasiswa', [
                '--id_mhs' => $idMhs,
                '--id_semester' => $finalScore['pengambilan_mk']->id_semester ?? null,
            ]);
        }

        return 0; // Return zero exit code for success

    }
}
