<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StokBantuan extends Model
{
    protected $table = 'app_stok_bantuan';
    protected $primaryKey = 'id_stok';

    protected $fillable = [
        'id_kategori',
        'tipe',
        'nama_item',
        'jumlah',
        'satuan',
        'tanggal',
        'sumber',
        'id_kecamatan',
        'id_kelurahan',
        'tujuan',
        'keterangan',
        'dicatat_oleh',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'tanggal' => 'date',
    ];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan', 'id_kecamatan');
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'id_kelurahan', 'id_kelurahan');
    }

    public function kategoriStok(): BelongsTo
    {
        return $this->belongsTo(KategoriStok::class, 'id_kategori', 'id_kategori');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh', 'id_user')->withTrashed();
    }

    public function distribusiBantuan(): HasMany
    {
        return $this->hasMany(DistribusiBantuan::class, 'id_stok', 'id_stok');
    }
}
