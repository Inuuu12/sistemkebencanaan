<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoskoPengungsian extends Model
{
    protected $table = 'app_posko_pengungsian';
    protected $primaryKey = 'id_posko';

    protected $fillable = [
        'id_laporan',
        'id_kecamatan',
        'id_kelurahan',
        'nama_posko',
        'jenis_posko',
        'alamat_lengkap',
        'latitude',
        'longitude',
        'kapasitas_maksimal',
        'jumlah_pengungsi',
        'jumlah_balita',
        'jumlah_lansia',
        'fasilitas',
        'pj_nama',
        'pj_kontak',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'kapasitas_maksimal' => 'integer',
        'jumlah_pengungsi' => 'integer',
        'jumlah_balita' => 'integer',
        'jumlah_lansia' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function laporanBencana(): BelongsTo
    {
        return $this->belongsTo(LaporanBencana::class, 'id_laporan', 'id_laporan');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'id_kecamatan');
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'id_kelurahan', 'id_kelurahan');
    }
}
