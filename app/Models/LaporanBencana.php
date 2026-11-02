<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanBencana extends Model
{
    protected $table = 'app_laporan_bencana';
    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'id_user',
        'id_bencana',
        'id_kecamatan',
        'id_kelurahan',
        'id_titik',
        'nama_pelapor',
        'nomor_telepon',
        'email_pelapor',
        'rt',
        'rw',
        'judul',
        'deskripsi',
        'jumlah_korban',
        'tingkat_korban',
        'jumlah_korban_jiwa',
        'jumlah_korban_luka_ringan',
        'jumlah_korban_luka_sedang',
        'jumlah_korban_luka_berat',
        'jumlah_rumah_rusak_berat',
        'jumlah_rumah_rusak_sedang',
        'jumlah_rumah_rusak_ringan',
        'latitude',
        'longitude',
        'alamat_detail',
        'foto_laporan',
        'kebutuhan_logistik',
        'status',
    ];

    protected $casts = [
        'jumlah_korban' => 'integer',
        'jumlah_korban_jiwa' => 'integer',
        'jumlah_korban_luka_ringan' => 'integer',
        'jumlah_korban_luka_sedang' => 'integer',
        'jumlah_korban_luka_berat' => 'integer',
        'jumlah_rumah_rusak_berat' => 'integer',
        'jumlah_rumah_rusak_sedang' => 'integer',
        'jumlah_rumah_rusak_ringan' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'foto_laporan' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function bencana(): BelongsTo
    {
        return $this->belongsTo(Bencana::class, 'id_bencana', 'id_bencana');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'id_kecamatan');
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'id_kelurahan', 'id_kelurahan');
    }

    public function penanganan(): HasMany
    {
        return $this->hasMany(Penanganan::class, 'id_laporan', 'id_laporan');
    }

    public function korban(): HasMany
    {
        return $this->hasMany(Korban::class, 'id_laporan', 'id_laporan');
    }

    public function dampakKerusakan(): HasMany
    {
        return $this->hasMany(DampakKerusakan::class, 'id_laporan', 'id_laporan');
    }

    public function distribusiBantuan(): HasMany
    {
        return $this->hasMany(DistribusiBantuan::class, 'id_laporan', 'id_laporan');
    }


    public function historisKejadian(): HasMany
    {
        return $this->hasMany(HistorisKejadian::class, 'id_laporan', 'id_laporan');
    }
}

