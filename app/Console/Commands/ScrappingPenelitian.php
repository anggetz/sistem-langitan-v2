<?php

namespace App\Console\Commands;

use App\Models\PublikasiJurnal;
use App\Models\PublikasiJurnalAuthor;
use DateTime;
use DOMDocument;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        // $this->info('Starting scrapping penelitian...');
        // get file.json inside storage path

        $typeScrap = $this->option('type_scrap');

        $filePath = storage_path('app/scrapping/' . $typeScrap . '.json');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return 1; // Return a non-zero exit code for error
        }

        // trigger the executeable name webscrapper-windows.exe or webscrapper-linux depending on the OS
        $executable = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'webscrapper-windows.exe' : 'webscrapper-linux';

        $command = $executable . ' ' . escapeshellarg($filePath) . ' "VbYmCMUAAAAJ" ' . " 2>&1";

        // get the output
        $output = shell_exec($command);

        $jsonOutput = json_decode($output);

        $finalResult = [];

        foreach ($jsonOutput as $json) {
            $html = $json->body; // your HTML snippet here

            if (empty($html)) {
                break;
            }

            $dom = new DOMDocument();
            libxml_use_internal_errors(true); // suppress warnings for malformed HTML
            $dom->loadHTML($html);
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);

            // get all field-value pairs
            $fields = $xpath->query("//div[@class='gs_scl']");

            $result = [];

            foreach ($fields as $field) {
                $keyNode = $xpath->query(".//div[@class='gsc_oci_field']", $field)->item(0);
                $valueNode = $xpath->query(".//div[@class='gsc_oci_value']", $field)->item(0);

                if ($keyNode && $valueNode) {
                    $key = trim($keyNode->textContent);
                    $value = trim($valueNode->textContent);
                    $result[$key] = $value;
                }
            }

            array_push($finalResult, $result);
        }


        foreach ($finalResult as $result) {
            $newEntity = new PublikasiJurnal();
            $authors = [];

            if(array_key_exists('Authors', $result)) {
                $authors = explode(',', $result['Authors']);
            }

            if(array_key_exists('Journal', $result)) {
                $newEntity->title = $result['Journal'];
            }


            if(array_key_exists('Scholar articles', $result)) {
                $newEntity->title = $result['Scholar articles'];
            }

            if(array_key_exists('Publication date', $result)) {
                $formattedDate = DateTime::createFromFormat('Y/m/d', $result['Publication date']);
                if ($formattedDate) {
                    $newEntity->tanggal_publikasi = $formattedDate;
                    $newEntity->tahun = (int)$newEntity->tanggal_publikasi->format('Y');
                }
            }

            if(array_key_exists('Volume', $result)) {
                $newEntity->volume = (int)$result['Volume'];
            }

            if(array_key_exists('Pages', $result)) {
                $newEntity->pages = $result['Pages'];
            }

            if(array_key_exists('Description', $result)) {
                $newEntity->description = $result['Description'];
            }

            if(array_key_exists('Issue', $result)) {
                $newEntity->description = (int)$result['Issue'];
            }

            $newEntity->save();
            // set the authors
            foreach ($authors as $author) {
                $author = trim($author);
                $authData = PublikasiJurnalAuthor::where("nama", $author)->where('id_publikasi_jurnal', $newEntity->ID_PUBLIKASI_JURNAL)->first();
                if (empty($authData)) {

                    $newAuthor = new PublikasiJurnalAuthor();
                    $newAuthor->nama = $author;
                    $newAuthor->id_publikasi_jurnal = $newEntity->ID_PUBLIKASI_JURNAL;
                    $newAuthor->save();
                }
            }
        }


        if ($output === null) {
            $this->error('Failed to execute the command.');
            return 1; // Return a non-zero exit code for error
        }

        $this->info('Scrapping completed successfully.');
        // $this->info($finalResult);
    }
}
