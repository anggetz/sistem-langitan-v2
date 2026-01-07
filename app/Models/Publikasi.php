<?php

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class Publikasi extends Model
{
    use Blameable;

    protected $table = 'publikasi';
    protected $primaryKey = 'id_publikasi';
    public $timestamps = false;

    protected $fillable = [
        'id_dosen',
        'judul',
        'penerbit',
        'tanggal_publikasi',
        'id_jenis_publikasi',
        'doi',
        'issn',
        'isbn',
        'volume',
        'issue',
        'halaman',
        'abstrak',
        'kata_kunci',
        'bahasa',
        'pendanaan',
        'status',
        'url',
        'id_pengindeks_publikasi',
        'sjr_kuartil',
        'sinta',
        'is_approved',
        'approved_at',
        'approved_by',
        'is_rejected',
        'rejected_at',
        'rejected_by',
    ];

    protected $casts = [
        'tanggal_publikasi' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'is_approved' => 'boolean',
        'is_rejected' => 'boolean',
    ];

    /**
     * Status constants
     */
    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_SUBMITTED = 'SUBMITTED';
    public const STATUS_ACCEPTED = 'ACCEPTED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_PUBLISHED = 'PUBLISHED';

    /**
     * Get all status options
     *
     * @return array
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
            self::STATUS_PUBLISHED,
        ];
    }

    /**
     * SJR Kuartil constants
     */
    public const SJR_Q1 = 'Q1';
    public const SJR_Q2 = 'Q2';
    public const SJR_Q3 = 'Q3';
    public const SJR_Q4 = 'Q4';

    /**
     * Get all SJR Kuartil options
     *
     * @return array
     */
    public static function sjrKuartilOptions(): array
    {
        return [
            self::SJR_Q1,
            self::SJR_Q2,
            self::SJR_Q3,
            self::SJR_Q4,
        ];
    }

    /**
     * SINTA constants
     */
    public const SINTA_1 = '1';
    public const SINTA_2 = '2';
    public const SINTA_3 = '3';
    public const SINTA_4 = '4';
    public const SINTA_5 = '5';
    public const SINTA_6 = '6';

    /**
     * Get all SINTA options
     *
     * @return array
     */
    public static function sintaOptions(): array
    {
        return [
            self::SINTA_1,
            self::SINTA_2,
            self::SINTA_3,
            self::SINTA_4,
            self::SINTA_5,
            self::SINTA_6,
        ];
    }

    /**
     * Relasi ke Dosen
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'id_dosen');
    }

    /**
     * Relasi ke Jenis Publikasi
     */
    public function jenisPublikasi()
    {
        return $this->belongsTo(PublikasiJenis::class, 'id_jenis_publikasi', 'id_jenis_publikasi');
    }

    /**
     * Relasi ke Pengindeks Publikasi
     */
    public function pengindeksPublikasi()
    {
        return $this->belongsTo(PublikasiPengindeks::class, 'id_pengindeks_publikasi', 'id_pengindeks_publikasi');
    }

    /**
     * Relasi ke Penulis Publikasi
     */
    public function penulis()
    {
        return $this->hasMany(PublikasiPenulis::class, 'id_publikasi', 'id_publikasi');
    }

    /**
     * Relasi ke Pengguna yang approve
     */
    public function approvedBy()
    {
        return $this->belongsTo(Pengguna::class, 'approved_by', 'id_pengguna');
    }

    /**
     * Relasi ke Pengguna yang reject
     */
    public function rejectedBy()
    {
        return $this->belongsTo(Pengguna::class, 'rejected_by', 'id_pengguna');
    }
}
