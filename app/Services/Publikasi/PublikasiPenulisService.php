<?php

namespace App\Services\Publikasi;

use App\Models\PublikasiPenulis;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PublikasiPenulisService
{
    /**
     * Get paginated list of publikasi penulis
     */
    public function getAll(array $params)
    {
        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 10;
        $offset = ($page - 1) * $perPage;

        $query = PublikasiPenulis::query();

        // Search functionality
        if (isset($params['q']) && !empty($params['q'])) {
            $search = $params['q'];
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%$search%")
                  ->orWhere('afiliasi', 'LIKE', "%$search%");
            });
        }

        // Filter by publikasi
        if (isset($params['id_publikasi']) && !empty($params['id_publikasi'])) {
            $query->where('id_publikasi', $params['id_publikasi']);
        }

        // Filter by dosen
        if (isset($params['id_dosen']) && !empty($params['id_dosen'])) {
            $query->where('id_dosen', $params['id_dosen']);
        }

        $total = $query->count();
        
        $data = $query
            ->with(['publikasi:id_publikasi,judul', 'dosen:id_dosen,nm_dosen'])
            ->orderBy('id_publikasi', 'desc')
            ->orderBy('urutan', 'asc')
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
     * Create a new publikasi penulis
     */
    public function create(array $data)
    {
        return PublikasiPenulis::create($data);
    }

    /**
     * Get publikasi penulis by ID
     */
    public function getById($id)
    {
        $publikasiPenulis = PublikasiPenulis::with(['publikasi', 'dosen'])->find($id);
        
        if (!$publikasiPenulis) {
            throw new ModelNotFoundException('Publikasi penulis tidak ditemukan');
        }

        return $publikasiPenulis;
    }

    /**
     * Update publikasi penulis
     */
    public function update($id, array $data)
    {
        $publikasiPenulis = PublikasiPenulis::find($id);
        
        if (!$publikasiPenulis) {
            throw new ModelNotFoundException('Publikasi penulis tidak ditemukan');
        }

        $publikasiPenulis->update($data);
        
        return $publikasiPenulis->fresh(['publikasi', 'dosen']);
    }

    /**
     * Delete publikasi penulis
     */
    public function delete($id)
    {
        $publikasiPenulis = PublikasiPenulis::find($id);
        
        if (!$publikasiPenulis) {
            throw new ModelNotFoundException('Publikasi penulis tidak ditemukan');
        }

        $publikasiPenulis->delete();
        
        return true;
    }
}
