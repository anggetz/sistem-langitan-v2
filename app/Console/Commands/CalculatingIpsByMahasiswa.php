<?php

namespace App\Console\Commands;

use App\Models\MahasiswaStatus;
use App\Models\PengambilanMk;
use App\Models\PeraturanNilai;
use App\Models\StandarNilai;
use Illuminate\Console\Command;

class CalculatingIpsByMahasiswa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculating-ips-by-mahasiswa {--id_mhs=} {--id_semester=}';

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


        $idMhs = $this->option('id_mhs');
        $idSemester = $this->option('id_semester');

        $this->info("Calculating IPS for Mahasiswa ID: $idMhs in Semester ID: $idSemester");

        // Validate the argument
        if (!$idSemester) {
            $this->error('The id_semester argument is required.');
            return 1; // Return a non-zero exit code for error
        }
        if (!$idMhs) {
            $this->error('The id_mhs argument is required.');
            return 1; // Return a non-zero exit code for error
        }

        // get standar nilai
        $standarNilai = StandarNilai::get()->keyBy('nm_standar_nilai');

        $countData = 0;
        $sumTheTotalScore = 0;
        // get all the pengambilan by id mhs
        PengambilanMk::where('id_mhs', $idMhs)
            ->where('id_semester', $idSemester)
            ->get()
            ->each(function ($pengambilanMk) use (&$countData, $standarNilai, &$sumTheTotalScore) {
                // Calculate the IPS

                if (!empty($standarNilai[$pengambilanMk->fd_nilai_huruf])) {
                    $sumTheTotalScore += $standarNilai[$pengambilanMk->fd_nilai_huruf]->nilai_standar_nilai;
                }

                $countData++;
            });

        $this->info("Total Data: $countData");
        $this->info("Sum Total Score: $sumTheTotalScore");

        // calculte the ipk get the mahassiswa status below the semester

        $mhsStatuses = MahasiswaStatus::where('id_mhs', $idMhs)
            ->where('id_semester', '<', $idSemester)
            ->get();

        $ips = $mhsStatuses->sum('ips');
        $ipsTotal = $mhsStatuses->count();

        $ipk = $ipsTotal > 0 ? round($ips / $ipsTotal, 2) : 0;

        // update mahasiswa status
        MahasiswaStatus::upsert(
            [
                'id_mhs' => $idMhs,
                'id_semester' => $idSemester,
                'ips' => $countData > 0 ? round($sumTheTotalScore / $countData, 2) : 0,
                'ipk' => $ipk,
            ],
            ['id_mhs', 'id_semester'],
            ['ips', 'ipk']
        );

        $this->info('IPS calculation completed successfully.');
        return 0; // Return a zero exit code for success
    }
}
