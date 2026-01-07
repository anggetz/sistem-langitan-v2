<?php

namespace App\Services\Publikasi;

use App\Models\PublikasiJenis;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PublikasiJenisService
{
    /**
     * Get paginated list of publikasi jenis
     */
    public function getAll(array $params)
    {
        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 10;
        $offset = ($page - 1) * $perPage;

        $query = PublikasiJenis::query();

        // Search functionality
        if (isset($params['q']) && !empty($params['q'])) {
            $search = $params['q'];
            $query->where('jenis_publikasi', 'LIKE', "%$search%");
        }

        $total = $query->count();
        
        $data = $query
            ->orderBy('id_jenis_publikasi', 'desc')
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
     * Create a new publikasi jenis
     */
    public function create(array $data)
    {
        return PublikasiJenis::create($data);
    }

    /**
     * Get publikasi jenis by ID
     */
    public function getById($id)
    {
        $publikasiJenis = PublikasiJenis::find($id);
        
        if (!$publikasiJenis) {
            throw new ModelNotFoundException('Jenis publikasi tidak ditemukan');
        }

        return $publikasiJenis;
    }

    /**
     * Update publikasi jenis
     */
    public function update($id, array $data)
    {
        $publikasiJenis = $this->getById($id);
        $publikasiJenis->update($data);
        
        return $publikasiJenis;
    }

    /**
     * Delete publikasi jenis
     */
    public function delete($id)
    {
        $publikasiJenis = $this->getById($id);

        // Check if this jenis_publikasi is being used by any publikasi
        $publikasiCount = $publikasiJenis->publikasi()->count();
        
        if ($publikasiCount > 0) {
            throw new Exception('Tidak dapat menghapus jenis publikasi karena masih digunakan oleh ' . $publikasiCount . ' publikasi');
        }

        $publikasiJenis->delete();
        
        return true;
    }
}
