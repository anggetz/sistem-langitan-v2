<?php

namespace App\Http\Controllers\Dosen\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DosenListResource;
use App\Models\Dosen;
use App\Models\Jenjang;
use App\Models\Message;
use App\Models\Pengguna;
use App\Models\ProgramStudi;
use App\Services\Mahasiswa\AkademikService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

class DosenController extends Controller
{
    private $akademikService;
    public function __construct(AkademikService $akademikService)
    {
        $this->akademikService = $akademikService;
    }

    public function profile()
    {
        try {
            $user = \App\Models\Pengguna::with([
                'dosen',
                'dosen.penghargaan',
                'dosen.departemen.departemen',
                'dosen.penelitian',
                'dosen.programStudi',
                'dosen.pengampuMk.kelas_mk' => function ($query) {
                    $query->whereHas('semester', function ($q) {
                        $q->where('status_aktif_semester', 'True');
                    });
                },
                // 'dosen.prestasi',
                'kotaLahir'
            ])->find(auth()->user()->id_pengguna);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $dosen = $user->dosen;

            $programStudi = $dosen->programStudi ?? new ProgramStudi();

            $data = [
                'id'             => $user->id_pengguna,
                'id_dosen'        => $dosen->id_dosen,
                'nidn'        => $dosen->nidn_dosen,
                'nama_lengkap'   => $user->nama_lengkap,
                'nip'            => $dosen->nip_dosen,
                'foto'           => $user->foto_pengguna,
                'tempat_lahir'   => $user->kotaLahir->nm_kota ?? '-',
                'tanggal_lahir'  => $user->tgl_lahir_pengguna,
                'alamat'         => $dosen->alamat_rumah_dosen,
                'no_hp'          => $dosen->mobile_dosen,
                'status_dosen'   => $dosen->status_dosen,
                'departemen'   => $dosen->departemen->departemen->nm_departemen ?? '-',
                'program_studi' => $programStudi->nm_program_studi ?? '-',
                'kode_program_studi' => $programStudi->kode_program_studi ?? '-',
                'jenjang' => $programStudi->jenjang ?? null,
                'penelitian'    => $dosen->penelitian->map(function ($item) {
                    return [
                        'judul_penelitian' => $item->judul,
                        'tahun'            => $item->tahun,
                        'tgl_input'            => $item->tgl_input,
                    ];
                })->values(),
                'pengampu_mk'    => $dosen->pengampuMk->filter(function ($item) {
                    return $item->kelas_mk
                        && $item->kelas_mk->semester
                        && $item->kelas_mk->semester->status_aktif_semester;
                })->map(function ($item) {
                    $mataKuliah = $item->kelas_mk->mataKuliah;
                    return [
                        'nama_mk' => $mataKuliah->nm_mata_kuliah ?? '-',
                    ];
                })->values(),
                'penghargaan'    => $dosen->penghargaan,
            ];

            return response()->json([
                'message' => Message::OK,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function biodata(Request $request)
    {
        try {
            $user = Pengguna::select([
                'id_pengguna',
                'nm_pengguna',
                'gelar_depan',
                'gelar_belakang',
                'id_kota_lahir',
                'tgl_lahir_pengguna',
                'kelamin_pengguna',
                'id_agama',
                'id_status_pernikahan',
                'email_pengguna',
                'email_alternate',
            ])->with([
                'kotaLahir:id_kota,nm_kota',
                'agama:id_agama,nm_agama',
                'statusPernikahan:id_status_pernikahan,nm_status_pernikahan',
                'dosen' => function ($query) {
                    $query->select([
                        'id_dosen',
                        'id_pengguna',
                        'nip_dosen',
                        'nidn_dosen',
                        'serdos',
                        'status_dosen',
                        'nomor_npwp',
                        'no_ktp',
                        'alamat_rumah_dosen',
                        'kode_pos',
                        'tlp_dosen',
                        'mobile_dosen',
                        'id_program_studi',
                        'id_golongan',
                        'id_jabatan_fungsional',
                        'id_status_pengguna',
                        // 'id_departemen',
                    ])->with([
                        'programStudi:id_program_studi,id_jenjang,nm_program_studi,id_fakultas,id_departemen',
                        'programStudi.jenjang:id_jenjang,nm_jenjang',
                        'programStudi.fakultas:id_fakultas,nm_fakultas',
                        // 'departemen:id_departemen,nm_departemen',
                        'sejarahGolongan:id_sejarah_golongan,id_pengguna,id_golongan,tmt_sejarah_golongan',
                        'sejarahGolongan.golongan:id_golongan,nm_golongan',
                        'sejarahJabatanFungsional:id_pengguna,id_jabatan_fungsional,TMT_SEJ_JAB_FUNGSIONAL',
                        'sejarahJabatanFungsional.jabatanFungsional:id_jabatan_fungsional,nm_jabatan_fungsional',
                        'sejarahJabatanStruktural:id_pengguna,id_jabatan_struktural,tmt_sej_jab_struktural',
                        'sejarahJabatanStruktural.jabatanStruktural:id_jabatan_struktural,nm_jabatan_struktural',
                        'statusPengguna:id_status_pengguna,nm_status_pengguna',
                        'sejarahPendidikan:id_sejarah_pendidikan,id_pengguna,id_pendidikan_akhir',
                        'sejarahPendidikan.pendidikanAkhir:id_pendidikan_akhir,nama_pendidikan_akhir',
                    ]);
                },
            ])->find(auth()->user()->id_pengguna);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $dosen = $user->dosen;


            $data = [
                'id_pengguna'           => $user->id_pengguna,
                'nm_pengguna'           => $user->nm_pengguna,
                'gelar_depan'           => $user->gelar_depan,
                'gelar_belakang'        => $user->gelar_belakang,
                'tgl_lahir_pengguna'    => $user->tgl_lahir_pengguna,
                'kelamin_pengguna'      => $user->kelamin_pengguna,
                'email_alternate'        => $user->email_pengguna,
                'email_pribadi'       => $user->email_alternate,
                'kota_lahir'            => $user->kotaLahir?->nm_kota,
                'agama'                 => $user->agama?->nm_agama,
                'status_pernikahan'     => $user->statusPernikahan?->nm_status_pernikahan,
                'id_dosen'              => $dosen->id_dosen,
                'nip_dosen'             => $dosen->nip_dosen,
                'nidn_dosen'            => $dosen->nidn_dosen,
                'serdos'                => $dosen->serdos,
                'status_dosen'          => $dosen->status_dosen,
                'nomor_npwp'            => $dosen->nomor_npwp,
                'no_ktp'                => $dosen->no_ktp,
                'alamat_rumah_dosen'    => $dosen->alamat_rumah_dosen,
                'kode_pos'              => $dosen->kode_pos,
                'tlp_dosen'             => $dosen->tlp_dosen,
                'mobile_dosen'          => $dosen->mobile_dosen,
                'program_studi'         => $dosen->programStudi?->nm_program_studi,
                'jenjang_program_studi' => $dosen->programStudi?->jenjang?->nm_jenjang,
                'fakultas'              => $dosen->programStudi?->fakultas?->nm_fakultas,
                'golongan'              => $dosen->sejarahGolongan->first()?->golongan?->nm_golongan,
                'sejarah_golongan'      => $dosen->sejarahGolongan->first()?->tmt_sejarah_golongan,
                'jabatan_fungsional'    => $dosen->sejarahJabatanFungsional->first()?->jabatanFungsional?->nm_jabatan_fungsional,
                'status_pengguna'       => $dosen->statusPengguna?->nm_status_pengguna,
                'sejarah_jabatan_fungsional' => $dosen->sejarahJabatanFungsional->first()?->tmt_sej_jab_fungsional,
                'sejarah_jabatan_strutural' => $dosen->sejarahJabatanStruktural->first()?->tmt_sej_jab_struktural,
                'jabatan_struktural'    => $dosen->sejarahJabatanStruktural->first()?->jabatanStruktural?->nm_jabatan_struktural,
                'pendidikan_akhir' => $dosen->sejarahPendidikan->first()?->pendidikanAkhir?->nama_pendidikan_akhir,
            ];

            return response()->json([
                'message' => Message::OK,
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function editBiodata(Request $request)
    {
        try {
            $data = $request->validate([
                'nm_pengguna'          => 'required|string|max:255',
                'gelar_depan'          => 'nullable|string|max:50',
                'gelar_belakang'       => 'nullable|string|max:50',
                'tgl_lahir_pengguna'   => 'required|date_format:Y-m-d',
                'kelamin_pengguna'     => 'required|in:L,P',
                'email_pengguna'       => 'nullable|email',
                'email_alternate'      => 'nullable|email',
                'id_kota_lahir'        => 'required|integer',
                'id_status_pernikahan' => 'required|integer',
                'id_pendidikan_akhir'  => 'nullable|integer',
                'nomor_npwp'           => 'nullable|string|max:30',
                'no_ktp'               => 'nullable|string|max:20',
                'alamat_rumah_dosen'   => 'nullable|string|max:255',
                'kode_pos'             => 'nullable|string|max:10',
                'tlp_dosen'            => 'nullable|string|max:20',
                'mobile_dosen'         => 'nullable|string|max:20',
            ]);

            $user = Pengguna::with('dosen.sejarahPendidikan')->find(auth()->user()->id_pengguna);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            DB::beginTransaction();

            $user->fill([
                'nm_pengguna'          => $data['nm_pengguna'],
                'gelar_depan'          => $data['gelar_depan'] ?? null,
                'gelar_belakang'       => $data['gelar_belakang'] ?? null,
                'tgl_lahir_pengguna'   => $data['tgl_lahir_pengguna'],
                'kelamin_pengguna'     => $data['kelamin_pengguna'],
                'email_pengguna'       => $data['email_pengguna'] ?? null,
                'email_alternate'      => $data['email_alternate'] ?? null,
                'id_kota_lahir'        => $data['id_kota_lahir'],
                'id_status_pernikahan' => $data['id_status_pernikahan'],
            ])->save();

            $user->dosen->fill([
                'nomor_npwp'         => $data['nomor_npwp'] ?? null,
                'no_ktp'             => $data['no_ktp'] ?? null,
                'alamat_rumah_dosen' => $data['alamat_rumah_dosen'] ?? null,
                'kode_pos'           => $data['kode_pos'] ?? null,
                'tlp_dosen'          => $data['tlp_dosen'] ?? null,
                'mobile_dosen'       => $data['mobile_dosen'] ?? null,
            ])->save();

            if (!empty($data['id_pendidikan_akhir'])) {
                $sejarahPendidikan = $user->dosen->sejarahPendidikan->first();
                if ($sejarahPendidikan) {
                    $sejarahPendidikan->update(['id_pendidikan_akhir' => $data['id_pendidikan_akhir']]);
                } else {
                    \App\Models\SejarahPendidikan::create([
                        'id_pengguna'        => $user->id_pengguna,
                        'id_pendidikan_akhir' => $data['id_pendidikan_akhir'],
                    ]);
                }
            }

            DB::commit();

            return response()->json(['message' => Message::OK]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function EditProfile(Request $request)
    {
        try {
            $request->validate([
                'nm_pengguna' => 'required|string|max:255',
                'no_hp' => 'required|string|max:15',
                'alamat_rumah_dosen' => 'required|string|max:255',
                "kota_lahir" => 'required|exists:kota,id_kota',
                'tgl_lahir_pengguna' => 'required|date',
            ]);
            $user = \App\Models\Pengguna::with(['dosen'])->find(auth()->user()->id_pengguna);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            DB::beginTransaction();

            $dosen = $user->dosen;

            $user->nm_pengguna = $request->input('nm_pengguna');
            $dosen->mobile_dosen = $request->input('no_hp');
            $dosen->alamat_rumah_dosen = $request->input('alamat_rumah_dosen');
            $user->id_kota_lahir = $request->input('kota_lahir');
            $user->tgl_lahir_pengguna = $request->input('tgl_lahir_pengguna');

            $dosen->save();
            $user->save();

            DB::commit();
            return response()->json([
                'message' => Message::OK,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function EditPhotoProfile(Request $request)
    {
        try {
            $request->validate([
                'foto_pengguna' => 'required|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
            ]);
            $user = \App\Models\Pengguna::with(['dosen'])->find(auth()->id());
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Handle file upload
            if ($request->hasFile('foto_pengguna')) {
                // Hapus foto lama jika ada
                if ($user->foto_pengguna && Storage::disk('public')->exists($user->foto_pengguna)) {
                    Storage::disk('public')->delete($user->foto_pengguna);
                }

                $path = $request->file('foto_pengguna')->store('photos', 'public');
                $user->foto_pengguna = asset('storage/' . $path);
            }

            $user->save();

            return response()->json([
                'message' => Message::OK,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function kalender()
    {
        $data = $this->akademikService->kalender();
        return response()->json([
            'status' => Message::OK,
            'data' => $data
        ], 200);
    }

    public function list(Request $request)
    {
        try {
            $query = \App\Models\Dosen::with('pengguna:id_pengguna,nm_pengguna');

            // Search by name if q parameter is provided and length > 3
            if ($request->has('q') && strlen($request->q) >= 3) {
                $search = $request->q;
                $query->whereHas('pengguna', function ($q) use ($search) {
                    // sebelum dicari dijadikan upper case
                    $searchUpper = strtoupper($search);
                    $q->whereRaw('UPPER(nm_pengguna) like ?', ['%' . $searchUpper . '%']);
                });
            }

            // Order by name and limit to 10 records
            $dosen = $query->join('pengguna', 'dosen.id_pengguna', '=', 'pengguna.id_pengguna')
                ->orderBy('pengguna.nm_pengguna')
                ->select('dosen.id_dosen', 'dosen.id_pengguna')
                ->limit(10)
                ->get();

            return response()->json([
                'status' => Message::OK,
                'data' => DosenListResource::collection($dosen)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
