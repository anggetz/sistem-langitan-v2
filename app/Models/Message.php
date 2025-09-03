<?php

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory, Blameable;

    protected $table = 'messages';
    protected $primaryKey = 'id_message';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'id_pengirim',
        'id_penerima',
        'tema',
        'isi_pesan',
        'id_replay',
        'status_terbaca',
        'waktu_kirim',
        'waktu_baca',
        'status_hapus_pengirim',
        'status_hapus_penerima'
    ];

    protected $casts = [
        'status_terbaca' => 'boolean',
        'status_hapus_pengirim' => 'boolean',
        'status_hapus_penerima' => 'boolean',
        'waktu_kirim' => 'datetime',
        'waktu_baca' => 'datetime'
    ];

    // Status constants
    public const OK = 'OK';
    public const FAIL = 'FAIL';

    /**
     * Relationship dengan pengguna sebagai pengirim
     */
    public function pengirim()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengirim', 'id_pengguna');
    }

    /**
     * Relationship dengan pengguna sebagai penerima
     */
    public function penerima()
    {
        return $this->belongsTo(Pengguna::class, 'id_penerima', 'id_pengguna');
    }

    /**
     * Relationship untuk pesan yang direply
     */
    public function pesanDireply()
    {
        return $this->belongsTo(Message::class, 'id_replay', 'id_message');
    }

    /**
     * Relationship untuk pesan-pesan reply dari pesan ini
     */
    public function replies()
    {
        return $this->hasMany(Message::class, 'id_replay', 'id_message');
    }

    /**
     * Scope untuk pesan yang belum terbaca
     */
    public function scopeUnread($query)
    {
        return $query->where('status_terbaca', false);
    }

    /**
     * Scope untuk pesan yang sudah terbaca
     */
    public function scopeRead($query)
    {
        return $query->where('status_terbaca', true);
    }

    /**
     * Scope untuk pesan antara dua pengguna
     */
    public function scopeBetweenUsers($query, $user1, $user2)
    {
        return $query->where(function ($q) use ($user1, $user2) {
            $q->where('id_pengirim', $user1)->where('id_penerima', $user2);
        })->orWhere(function ($q) use ($user1, $user2) {
            $q->where('id_pengirim', $user2)->where('id_penerima', $user1);
        });
    }

    /**
     * Scope untuk pesan yang tidak dihapus oleh pengguna tertentu
     */
    public function scopeNotDeletedBy($query, $userId, $role)
    {
        if ($role === 'pengirim') {
            return $query->where('status_hapus_pengirim', false);
        } else {
            return $query->where('status_hapus_penerima', false);
        }
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        $this->update([
            'status_terbaca' => true,
            'waktu_baca' => now()
        ]);
    }

    /**
     * Soft delete untuk pengirim
     */
    public function deleteForSender()
    {
        $this->update(['status_hapus_pengirim' => true]);
    }

    /**
     * Soft delete untuk penerima
     */
    public function deleteForReceiver()
    {
        $this->update(['status_hapus_penerima' => true]);
    }
}
