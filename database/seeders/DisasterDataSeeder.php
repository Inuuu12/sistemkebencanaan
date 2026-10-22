<?php

namespace Database\Seeders;

use App\Models\Bencana;
use App\Models\Berita;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\LaporanBencana;
use App\Models\Penanganan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DisasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrNew(['email' => 'warga@sigab.id']);

        if (! $user->exists) {
            $user->fill([
                'nama' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'no_telp' => '081288889999',
                'role' => 'USER',
                'is_active' => true,
            ]);
            $user->save();
        }

        // 2. Master Wilayah
        $kabupaten = Kabupaten::firstOrCreate(
            ['nama_kabupaten' => 'Kabupaten Bogor']
        );

        $kecamatan = Kecamatan::firstOrCreate(
            ['nama_kecamatan' => 'Cibinong'],
            ['id_kabupaten' => $kabupaten->id_kabupaten]
        );

        $kelurahan = Kelurahan::firstOrCreate(
            ['nama_kelurahan' => 'Cibinong Kota'],
            ['id_kecamatan' => $kecamatan->id_kecamatan]
        );

        // 3. Master Data Jenis Bencana (Kabupaten Bogor)
        $bencanaTypes = [
            'Banjir' => 'Bencana akibat luapan sungai atau genangan air hujan tinggi.',
            'Tanah Longsor' => 'Pergeseran tanah di area lereng atau perbukitan.',
            'Angin Puting Beliung' => 'Angin kencang berputar yang merusak bangunan.',
            'Kebakaran' => 'Kebakaran pemukiman atau fasilitas umum.',
            'Kebakaran Lahan' => 'Kebakaran pada area lahan terbuka, semak belukar, atau perkebunan.',
            'Kekeringan' => 'Kondisi kekurangan air bersih dalam jangka waktu yang lama.',
            'Gempa Bumi' => 'Guncangan bumi akibat aktivitas tektonik/vulkanik.',
            'Gunung Meletus' => 'Erupsi gunung berapi mengeluarkan abu & material vulkanik.',
        ];

        $bencanaMap = [];
        foreach ($bencanaTypes as $nama => $deskripsi) {
            $b = Bencana::firstOrCreate(
                ['nama_bencana' => $nama],
                ['deskripsi' => $deskripsi]
            );
            $bencanaMap[$nama] = $b->id_bencana;
        }

        // 4. Master Berita
        if (Berita::count() === 0) {
            Berita::create([
                'judul' => 'Peringatan Dini: Potensi Hujan Lebat Disertai Petir di Bogor & Jakarta',
                'isi' => 'Badan Penanggulangan Bencana Daerah (BPBD) merilis peringatan dini cuaca untuk wilayah Jabodetabek. Warga dihimbau mengantisipasi terjadinya genangan air dan menghindari berteduh di bawah pohon rindang saat hujan deras.',
                'gambar' => 'https://images.unsplash.com/photo-1504608524841-42fe6f032b4b?auto=format&fit=crop&w=600&q=80',
                'sumber' => 'BPBD Pusdalops',
            ]);

            Berita::create([
                'judul' => 'Sosialisasi Siaga Kebakaran Pemukiman Padat Penduduk',
                'isi' => 'Dinas Penanggulangan Kebakaran dan Penyelamatan menyelenggarakan penyuluhan penggunaan APAR kepada warga. Kegiatan ini bertujuan memperkuat ketahanan warga terhadap potensi kebakaran sirkuit pendek listrik.',
                'gambar' => 'https://images.unsplash.com/photo-1508873696983-2df519fcd3ad?auto=format&fit=crop&w=600&q=80',
                'sumber' => 'Damkar Diskominfo',
            ]);

            Berita::create([
                'judul' => 'Genangan Air di Jalan Utama Berangsur Surut',
                'isi' => 'Kondisi air di saluran utama dilaporkan telah kembali ke batas normal. Petugas gabungan PPSU dan warga mulai bergotong royong membersihkan sisa material lumpur pasca genangan tadi malam.',
                'gambar' => 'https://images.unsplash.com/photo-1469571486090-75993ef49852?auto=format&fit=crop&w=600&q=80',
                'sumber' => 'Dinas Sosial',
            ]);
        }

        // 5. Sample Laporan Bencana & Penanganan
        if (LaporanBencana::count() === 0) {
            $rep1 = LaporanBencana::create([
                'id_user' => $user->id_user,
                'id_bencana' => $bencanaMap['Banjir'] ?? 1,
                'id_kecamatan' => $kecamatan->id_kecamatan,
                'id_kelurahan' => $kelurahan->id_kelurahan,
                'judul' => 'Genangan Air Setinggi 50 cm',
                'deskripsi' => 'Hujan deras semalaman menyebabkan genangan di kawasan Cibinong, Kabupaten Bogor. Lalu lintas roda dua terhambat.',
                'jumlah_korban' => 0,
                'latitude' => -6.4971,
                'longitude' => 106.8375,
                'alamat_detail' => 'Kawasan Pakansari, Kecamatan Cibinong, Kabupaten Bogor',
                'foto_laporan' => 'https://images.unsplash.com/photo-1547683905-f686c993aae5?auto=format&fit=crop&w=600&q=80',
                'status' => 'Processed',
            ]);

            Penanganan::create([
                'id_laporan' => $rep1->id_laporan,
                'catatan' => 'Laporan diterima oleh sistem SIGAB.',
                'status_baru' => 'Pending',
                'updated_by' => $user->id_user,
            ]);

            Penanganan::create([
                'id_laporan' => $rep1->id_laporan,
                'catatan' => 'Petugas BPBD dikerahkan menuju lokasi untuk penyedotan air.',
                'status_baru' => 'Processed',
                'updated_by' => $user->id_user,
            ]);

            $rep2 = LaporanBencana::create([
                'id_user' => $user->id_user,
                'id_bencana' => $bencanaMap['Kebakaran'] ?? 2,
                'id_kecamatan' => $kecamatan->id_kecamatan,
                'id_kelurahan' => $kelurahan->id_kelurahan,
                'judul' => 'Kebakaran Kabel Tiang Listrik',
                'deskripsi' => 'Kabel udara tiang listrik di kawasan Cibinong terbakar dan mengeluarkan percikan api besar.',
                'jumlah_korban' => 0,
                'latitude' => -6.4850,
                'longitude' => 106.8520,
                'alamat_detail' => 'Kawasan Cibinong, Kecamatan Cibinong, Kabupaten Bogor',
                'foto_laporan' => 'https://images.unsplash.com/photo-1508873696983-2df519fcd3ad?auto=format&fit=crop&w=600&q=80',
                'status' => 'Completed',
            ]);

            Penanganan::create([
                'id_laporan' => $rep2->id_laporan,
                'catatan' => 'Laporan diterima oleh sistem SIGAB.',
                'status_baru' => 'Pending',
                'updated_by' => $user->id_user,
            ]);

            Penanganan::create([
                'id_laporan' => $rep2->id_laporan,
                'catatan' => 'Kabel gardu listrik telah diperbaiki. Aliran listrik kembali normal.',
                'status_baru' => 'Completed',
                'updated_by' => $user->id_user,
            ]);

            $rep3 = LaporanBencana::create([
                'id_user' => $user->id_user,
                'id_bencana' => $bencanaMap['Gempa Bumi'] ?? 3,
                'id_kecamatan' => $kecamatan->id_kecamatan,
                'id_kelurahan' => $kelurahan->id_kelurahan,
                'judul' => 'Retakan Dinding Pasca Gempa',
                'deskripsi' => 'Gempa magnitudo 5.2 tadi malam menyisakan retakan pada bangunan di kawasan Cibinong.',
                'jumlah_korban' => 0,
                'latitude' => -6.4810,
                'longitude' => 106.8350,
                'alamat_detail' => 'Kawasan Cibinong, Kecamatan Cibinong, Kabupaten Bogor',
                'foto_laporan' => 'https://images.unsplash.com/photo-1594897030264-ab7d87efc473?auto=format&fit=crop&w=600&q=80',
                'status' => 'Pending',
            ]);

            Penanganan::create([
                'id_laporan' => $rep3->id_laporan,
                'catatan' => 'Laporan masuk. Menunggu verifikasi tim teknis kelurahan.',
                'status_baru' => 'Pending',
                'updated_by' => $user->id_user,
            ]);
        }

        foreach ([
            'Genangan Air Setinggi 50 cm' => [
                'latitude' => -6.4971,
                'longitude' => 106.8375,
                'alamat_detail' => 'Kawasan Pakansari, Kecamatan Cibinong, Kabupaten Bogor',
            ],
            'Kebakaran Kabel Tiang Listrik' => [
                'latitude' => -6.4850,
                'longitude' => 106.8520,
                'alamat_detail' => 'Kawasan Cibinong, Kecamatan Cibinong, Kabupaten Bogor',
            ],
            'Retakan Dinding Pasca Gempa' => [
                'latitude' => -6.4810,
                'longitude' => 106.8350,
                'alamat_detail' => 'Kawasan Cibinong, Kecamatan Cibinong, Kabupaten Bogor',
            ],
        ] as $title => $location) {
            LaporanBencana::where('judul', $title)->update($location);
        }
    }
}
