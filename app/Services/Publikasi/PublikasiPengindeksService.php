<?php

namespace App\Services\Publikasi;

use App\Models\PublikasiPengindeks;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PublikasiPengindeksService
{
    /**
     * Get paginated list of publikasi pengindeks
     */
    public function getAll(array $params)
    {
        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 10;
        $offset = ($page - 1) * $perPage;

        $query = PublikasiPengindeks::query();

        // Search functionality
        if (isset($params['q']) && !empty($params['q'])) {
            $search = $params['q'];
            $query->where('pengindeks_publikasi', 'LIKE', "%$search%");
        }

        $total = $query->count();
        
        $data = $query
            ->orderBy('id_pengindeks_publikasi', 'desc')
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
     * Create a new publikasi pengindeks
     */
    public function create(array $data)
    {
        return PublikasiPengindeks::create($data);
    }

    /**
     * Get publikasi pengindeks by ID
     */
    public function getById($id)
    {
        $publikasiPengindeks = PublikasiPengindeks::find($id);
        
        if (!$publikasiPengindeks) {
            throw new ModelNotFoundException('Publikasi pengindeks tidak ditemukan');
        }

        return $publikasiPengindeks;
    }

    /**
     * Update publikasi pengindeks
     */
    public function update($id, array $data)
    {
        $publikasiPengindeks = $this->getById($id);
        $publikasiPengindeks->update($data);
        
        return $publikasiPengindeks;
    }

    /**
     * Delete publikasi pengindeks
     */
    public function delete($id)
    {
        $publikasiPengindeks = $this->getById($id);

        // Check if this pengindeks_publikasi is being used by any publikasi
        $publikasiCount = $publikasiPengindeks->publikasi()->count();
        
        if ($publikasiCount > 0) {
            throw new Exception('Tidak dapat menghapus publikasi pengindeks karena masih digunakan oleh ' . $publikasiCount . ' publikasi');
        }

        $publikasiPengindeks->delete();
        
        return true;
    }
}
