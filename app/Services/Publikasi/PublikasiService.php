<?php

namespace App\Services\Publikasi;

use App\Models\Publikasi;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class PublikasiService
{
    /**
     * Get paginated list of publikasi
     */
    public function getAll(array $params)
    {
        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 10;
        $offset = ($page - 1) * $perPage;

        //        dd($params);

        $query = Publikasi::with([
            'dosen:id_dosen,id_pengguna',
            'dosen.pengguna:id_pengguna,nm_pengguna',
            'jenisPublikasi:id_jenis_publikasi,jenis_publikasi',
            'pengindeksPublikasi:id_pengindeks_publikasi,pengindeks_publikasi',
            'penulis',
        ]);

        // Filter by dosen (required - must be filtered first)
        if (isset($params['id_dosen']) && ! empty($params['id_dosen'])) {
            $query->where('id_dosen', $params['id_dosen']);
        }

        // Search functionality
        if (isset($params['q']) && ! empty($params['q'])) {
            $search = $params['q'];
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(judul) LIKE ?', ['%'.strtolower($search).'%'])
                    ->orWhereRaw('LOWER(penerbit) LIKE ?', ['%'.strtolower($search).'%'])
                    ->orWhereRaw('LOWER(abstrak) LIKE ?', ['%'.strtolower($search).'%'])
                    ->orWhereRaw('LOWER(kata_kunci) LIKE ?', ['%'.strtolower($search).'%']);
            });
        }

        // Filter by jenis publikasi
        if (isset($params['id_jenis_publikasi']) && ! empty($params['id_jenis_publikasi'])) {
            $query->where('id_jenis_publikasi', $params['id_jenis_publikasi']);
        }

        // Filter by pengindeks publikasi
        if (isset($params['id_pengindeks_publikasi']) && ! empty($params['id_pengindeks_publikasi'])) {
            $query->where('id_pengindeks_publikasi', $params['id_pengindeks_publikasi']);
        }

        // Filter by status
        if (isset($params['status']) && ! empty($params['status'])) {
            $query->whereRaw('LOWER(status) = ?', [strtolower($params['status'])]);
        }

        // Filter by approval status
        if (isset($params['is_approved'])) {
            $query->where('is_approved', $params['is_approved']);
        }

        if (isset($params['is_rejected'])) {
            $query->where('is_rejected', $params['is_rejected']);
        }

        // Filter by year
        if (isset($params['year']) && ! empty($params['year'])) {
            $query->whereYear('tanggal_publikasi', $params['year']);
        }

        $total = $query->count();

        $data = $query
            ->orderBy('tanggal_publikasi', 'desc')
            ->orderBy('id_publikasi', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();

        return [
            'data' => $data,
            'total' => $total,
            'page' => (int) $page,
            'per_page' => (int) $perPage,
        ];
    }

    /**
     * Create a new publikasi
     */
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            // Pisahkan data penulis dari data publikasi
            $penulisData = $data['penulis'] ?? [];
            unset($data['penulis']);

            // Buat publikasi
            $publikasi = Publikasi::create($data);

            // Simpan data penulis jika ada
            if (! empty($penulisData)) {
                foreach ($penulisData as $penulis) {
                    $publikasi->penulis()->create([
                        'id_dosen' => $penulis['id_dosen'] ?? null,
                        'nama' => $penulis['nama'],
                        'afiliasi' => $penulis['afiliasi'],
                        'urutan' => $penulis['urutan'],
                    ]);
                }
            }

            DB::commit();

            return $publikasi->load([
                'dosen',
                'jenisPublikasi',
                'pengindeksPublikasi',
                'penulis',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get publikasi by ID
     */
    public function getById($id)
    {
        $publikasi = Publikasi::with([
            'dosen:id_dosen,id_pengguna',
            'dosen.pengguna:id_pengguna,nm_pengguna',
            'jenisPublikasi:id_jenis_publikasi,jenis_publikasi',
            'pengindeksPublikasi:id_pengindeks_publikasi,pengindeks_publikasi',
            'penulis.dosen:id_dosen,id_pengguna',
            'penulis.dosen.pengguna:id_pengguna,nm_pengguna',
            'approvedBy:id_pengguna,nm_pengguna',
            'rejectedBy:id_pengguna,nm_pengguna',
        ])->find($id);

        if (! $publikasi) {
            throw new ModelNotFoundException('Publikasi tidak ditemukan');
        }

        return $publikasi;
    }

    /**
     * Update publikasi
     */
    public function update($id, array $data)
    {
        DB::beginTransaction();
        try {
            $publikasi = Publikasi::find($id);

            if (! $publikasi) {
                throw new ModelNotFoundException('Publikasi tidak ditemukan');
            }

            // Pisahkan data penulis dari data publikasi
            $penulisData = $data['penulis'] ?? null;
            unset($data['penulis']);

            // Update publikasi
            $publikasi->update($data);

            // Update data penulis jika ada
            if ($penulisData !== null) {
                // Hapus semua penulis lama
                $publikasi->penulis()->delete();

                // Tambahkan penulis baru
                if (! empty($penulisData)) {
                    foreach ($penulisData as $penulis) {
                        $publikasi->penulis()->create([
                            'id_dosen' => $penulis['id_dosen'] ?? null,
                            'nama' => $penulis['nama'],
                            'afiliasi' => $penulis['afiliasi'],
                            'urutan' => $penulis['urutan'],
                        ]);
                    }
                }
            }

            DB::commit();

            return $publikasi->fresh([
                'dosen:id_dosen,id_pengguna',
                'dosen.pengguna:id_pengguna,nm_pengguna',
                'jenisPublikasi',
                'pengindeksPublikasi',
                'penulis',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete publikasi
     */
    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $publikasi = Publikasi::find($id);

            if (! $publikasi) {
                throw new ModelNotFoundException('Publikasi tidak ditemukan');
            }

            // Delete related penulis first
            $publikasi->penulis()->delete();

            // Delete publikasi
            $publikasi->delete();

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Approve publikasi
     */
    public function approve($id, $userId)
    {
        DB::beginTransaction();
        try {
            $publikasi = Publikasi::find($id);

            if (! $publikasi) {
                throw new ModelNotFoundException('Publikasi tidak ditemukan');
            }

            $publikasi->update([
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => $userId,
                'is_rejected' => false,
                'rejected_at' => null,
                'rejected_by' => null,
            ]);

            DB::commit();

            return $publikasi->fresh([
                'dosen:id_dosen,id_pengguna',
                'dosen.pengguna:id_pengguna,nm_pengguna',
                'jenisPublikasi',
                'pengindeksPublikasi',
                'approvedBy:id_pengguna,nm_pengguna',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reject publikasi
     */
    public function reject($id, $userId)
    {
        DB::beginTransaction();
        try {
            $publikasi = Publikasi::find($id);

            if (! $publikasi) {
                throw new ModelNotFoundException('Publikasi tidak ditemukan');
            }

            $publikasi->update([
                'is_rejected' => true,
                'rejected_at' => now(),
                'rejected_by' => $userId,
                'is_approved' => false,
                'approved_at' => null,
                'approved_by' => null,
            ]);

            DB::commit();

            return $publikasi->fresh([
                'dosen:id_dosen,id_pengguna',
                'dosen.pengguna:id_pengguna,nm_pengguna',
                'jenisPublikasi',
                'pengindeksPublikasi',
                'rejectedBy:id_pengguna,nm_pengguna',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
