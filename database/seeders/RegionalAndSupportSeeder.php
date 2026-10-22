<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Berita;
use App\Models\DaerahRawan;
use App\Models\Bencana;

class RegionalAndSupportSeeder extends Seeder
{
    public function run(): void
    {
        $kabupaten = Kabupaten::firstOrCreate(['nama_kabupaten' => 'Kabupaten Bogor']);

        $cibinong = Kecamatan::firstOrCreate([
            'id_kabupaten' => $kabupaten->id_kabupaten,
            'nama_kecamatan' => 'Cibinong'
        ]);
        $sukaraja = Kecamatan::firstOrCreate([
            'id_kabupaten' => $kabupaten->id_kabupaten,
            'nama_kecamatan' => 'Sukaraja'
        ]);

        $kelPakansari = Kelurahan::firstOrCreate([
            'id_kecamatan' => $cibinong->id_kecamatan,
            'nama_kelurahan' => 'Pakansari'
        ]);
        $kelCijujung = Kelurahan::firstOrCreate([
            'id_kecamatan' => $sukaraja->id_kecamatan,
            'nama_kelurahan' => 'Cijujung'
        ]);

        Berita::create([
            'judul' => 'Waspada Banjir Luapan Sungai Cileungsi Malam Ini',
            'isi' => 'Badan Penanggulangan Bencana Daerah (BPBD) Kabupaten Bogor mengimbau warga di sepanjang bantaran Sungai Cileungsi untuk waspada terhadap potensi banjir kiriman.',
            'gambar' => 'https://images.unsplash.com/photo-1547683905-f686c993aae5?auto=format&fit=crop&w=800&q=80',
            'sumber' => 'Pusdalops BPBD Kab Bogor'
        ]);

        Berita::create([
            'judul' => 'Sosialisasi Simulasi Evakuasi Gempa Bumi di Kecamatan Cibinong',
            'isi' => 'Pemerintah Kabupaten Bogor bersama tim pemadam kebakaran dan relawan SIGAB menyelenggarakan simulasi evakuasi mandiri gempa bumi bagi warga Kecamatan Cibinong.',
            'gambar' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80',
            'sumber' => 'BPBD Kab Bogor'
        ]);

        Berita::create([
            'judul' => 'Kebakaran Lahan Kosong di Sukaraja Berhasil Dipadamkan',
            'isi' => 'Petugas pemadam kebakaran berhasil menjinakkan kobaran api yang melahap lahan kosong di kawasan Sukaraja siang tadi.',
            'gambar' => 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&w=800&q=80',
            'sumber' => 'Damkar Kab Bogor'
        ]);

        $banjir = Bencana::where('nama_bencana', 'Banjir')->first();
        $idBanjir = $banjir ? $banjir->id_bencana : 1;

        $kebakaran = Bencana::where('nama_bencana', 'Kebakaran')->first();
        $idKebakaran = $kebakaran ? $kebakaran->id_bencana : 4;

        DaerahRawan::create([
            'id_kelurahan' => $kelPakansari->id_kelurahan,
            'id_bencana' => $idBanjir,
            'zona' => 'KUNING',
            'keterangan' => 'Potensi genangan air 30-50 cm saat curah hujan ekstrem lokal tinggi.',
            'latitude' => -6.4920,
            'longitude' => 106.8320,
            'radius_meter' => 500,
            'is_active' => true
        ]);

        DaerahRawan::create([
            'id_kelurahan' => $kelCijujung->id_kelurahan,
            'id_bencana' => $idKebakaran,
            'zona' => 'MERAH',
            'keterangan' => 'Kawasan padat dengan kerawanan tinggi kebakaran.',
            'latitude' => -6.5385,
            'longitude' => 106.8280,
            'radius_meter' => 250,
            'is_active' => true
        ]);
    }
}
