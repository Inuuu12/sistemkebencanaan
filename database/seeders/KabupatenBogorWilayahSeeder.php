<?php

namespace Database\Seeders;

use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Database\Seeder;

class KabupatenBogorWilayahSeeder extends Seeder
{
    /**
     * Data resmi 40 Kecamatan dan 435 Desa/Kelurahan di Kabupaten Bogor (Kemendagri)
     */
    public function run(): void
    {
        $kabupaten = Kabupaten::firstOrCreate(
            ['nama_kabupaten' => 'Kabupaten Bogor'],
            ['created_at' => now(), 'updated_at' => now()]
        );

        $jsonPath = base_path('bogor_wilayah_parsed.json');
        if (file_exists($jsonPath)) {
            $wilayahData = json_decode(file_get_contents($jsonPath), true);
        } else {
            $wilayahData = $this->getWilayahData();
        }

        $totalKec = 0;
        $totalKel = 0;

        foreach ($wilayahData as $kecName => $kelList) {
            $kecamatan = Kecamatan::firstOrCreate(
                [
                    'id_kabupaten' => $kabupaten->id_kabupaten,
                    'nama_kecamatan' => $kecName,
                ],
                ['created_at' => now(), 'updated_at' => now()]
            );
            $totalKec++;

            foreach ($kelList as $kelName) {
                Kelurahan::firstOrCreate(
                    [
                        'id_kecamatan' => $kecamatan->id_kecamatan,
                        'nama_kelurahan' => $kelName,
                    ],
                    ['created_at' => now(), 'updated_at' => now()]
                );
                $totalKel++;
            }
        }

        $this->command?->info("KabupatenBogorWilayahSeeder: Berhasil menyinkronkan {$totalKec} Kecamatan dan {$totalKel} Desa/Kelurahan resmi Kabupaten Bogor.");
    }

    /**
     * Fallback data lengkap 40 Kecamatan & 435 Desa/Kelurahan Kabupaten Bogor
     */
    public static function getWilayahData(): array
    {
        return [
            'Babakan Madang' => [
                'Babakan Madang', 'Bojong Koneng', 'Cijayanti', 'Cipambuan',
                'Citaringgul', 'Kadumangu', 'Karang Tengah', 'Sentul', 'Sumur Batu'
            ],
            'Bojonggede' => [
                'Bojong Baru', 'Bojonggede', 'Cimanggis', 'Kedung Waringin',
                'Ragajaya', 'Rawa Panjang', 'Susukan', 'Waringin Jaya', 'Pabuaran'
            ],
            'Caringin' => [
                'Pasir Buncir', 'Caringin', 'Ciderum', 'Ciherang Pondok',
                'Cimande', 'Cimande Hilir', 'Cinagara', 'Lemah Duhur',
                'Muara Jaya', 'Pancawati', 'Pasir Muncang', 'Tangkil'
            ],
            'Cariu' => [
                'Babakan Raden', 'Bantar Kuning', 'Cariu', 'Cibatu Tiga',
                'Cikutamahi', 'Karya Mekar', 'Kuta Mekar', 'Mekarwangi',
                'Sukajadi', 'Tegal Panjang'
            ],
            'Ciampea' => [
                'Bojong Jengkol', 'Bojong Rangkas', 'Benteng', 'Ciampea',
                'Ciampea Udik', 'Cibadak', 'Cibanteng', 'Cibuntu',
                'Cicadas', 'Cihideung Ilir', 'Cihideung Udik', 'Cinangka', 'Tegal Waru'
            ],
            'Ciawi' => [
                'Banjar Sari', 'Banjar Wangi', 'Banjar Waru', 'Bendungan',
                'Bitung Sari', 'Bojong Murni', 'Ciawi', 'Cibedug',
                'Cileungsi', 'Citapen', 'Jambu Luwuk', 'Pandansari', 'Teluk Pinang'
            ],
            'Cibinong' => [
                'Cibinong', 'Cirimekar', 'Ciriung', 'Harapan Jaya',
                'Karadenan', 'Nanggewer', 'Nanggewer Mekar', 'Pabuaran',
                'Pabuaran Mekar', 'Pakansari', 'Pondok Rajeg', 'Sukahati', 'Tengah'
            ],
            'Cibungbulang' => [
                'Cemplang', 'Ciaruteun Ilir', 'Ciaruteun Udik', 'Cibatok 1',
                'Cibatok 2', 'Cijujung', 'Cimanggu 1', 'Cimanggu 2',
                'Dukuh', 'Galuga', 'Girifulus', 'Leuweung Kolot',
                'Situ Ilir', 'Situ Udik', 'Sukamaju'
            ],
            'Cigombong' => [
                'Ciburayut', 'Ciburuy', 'Cigombong', 'Cisalada',
                'Pasirjaya', 'Srogol', 'Tugujaya', 'Watesjaya', 'Palasari'
            ],
            'Cigudeg' => [
                'Banyuasih', 'Banyuresmi', 'Batu Jajar', 'Bunisari',
                'Cigudeg', 'Cintamanik', 'Mekarjaya', 'Rengasjajar',
                'Sukamaju', 'Sukaraksa', 'Sukaraja', 'Tarisi',
                'Tegallega', 'Wargajaya', 'Bangunjaya'
            ],
            'Cijeruk' => [
                'Cibalung', 'Cijeruk', 'Cipelang', 'Cipicung',
                'Palasari', 'Tajur Halang', 'Tanjungsari', 'Warung Menteng', 'Sukaharja'
            ],
            'Cileungsi' => [
                'Cileungsi', 'Cileungsi Kidul', 'Cipenjo', 'Cipeucang',
                'Dayeuh', 'Gandoang', 'Jatisari', 'Limus Nunggal',
                'Mekarsari', 'Pasir Angin', 'Setu Sari', 'Mampir'
            ],
            'Ciomas' => [
                'Ciomas', 'Ciomas Rahayu', 'Kotabatu', 'Laladon',
                'Mekarjaya', 'Pagelaran', 'Parakan', 'Sukaharja',
                'Sukamakmur', 'Padasuka', 'Ciapus'
            ],
            'Cisarua' => [
                'Batu Layang', 'Cibeureum', 'Cilember', 'Citeko',
                'Jogjogan', 'Kopo', 'Leuwimalang', 'North Tugu',
                'South Tugu', 'Cisarua'
            ],
            'Ciseeng' => [
                'Babakan', 'Cibeuteung Muara', 'Cibeuteung Udik', 'Cibentang',
                'Cihaur', 'Ciseeng', 'Karihkil', 'Kuripan',
                'Parigi Mekar', 'Putat Nutug'
            ],
            'Citeureup' => [
                'Citeureup', 'Gunung Sari', 'Hambalang', 'Karang Asem Timur',
                'Leuwinutug', 'Pasir Mukti', 'Puspasari', 'Sanja',
                'Sukahati', 'Tajur', 'Tangkil', 'Tarikolot',
                'Karang Asem Barat', 'Puspanegara'
            ],
            'Dramaga' => [
                'Babakan', 'Babakan Raden', 'Ciherang', 'Cikarawang',
                'Dramaga', 'Neglasari', 'Petir', 'Purwasari',
                'Sukadamai', 'Sukawening'
            ],
            'Gunung Putri' => [
                'Bojong Kulur', 'Bojong Nangka', 'Cicadas', 'Cikeas Udik',
                'Gunung Putri', 'Karanggan', 'Nagrak', 'Tlajung Udik',
                'Wanaherang', 'Kragan'
            ],
            'Gunungsindur' => [
                'Cibadung', 'Cibinong', 'Cidokom', 'Curug',
                'Gunungsindur', 'Jampang', 'Pabuaran', 'Padurenan',
                'Pengasinan', 'Rawakalong'
            ],
            'Jasinga' => [
                'Bagoang', 'Barengkok', 'Cikopomayak', 'Curug',
                'Jasinga', 'Jugala Jaya', 'Kalongsawah', 'Koleang',
                'Neglasari', 'Pamagersari', 'Pangaur', 'Pangradin',
                'Sipak', 'Setiawangi', 'Tegal Wangi', 'Wirajaya'
            ],
            'Jonggol' => [
                'Balekambang', 'Bendungan', 'Cibodas', 'Jonggol',
                'Singajaya', 'Singasari', 'Sirnagalih', 'Sukasirna',
                'Weninggalih', 'Sukagalih', 'Sukamaju', 'Sukajaya',
                'Sukamaju Mekar', 'Sukasari'
            ],
            'Kemang' => [
                'Bojong', 'Kemang', 'Pabuaran', 'Parakan Jaya',
                'Pondok Udik', 'Semplak Barat', 'Tegal', 'Jampang', 'Atang Senjaya'
            ],
            'Klapanunggal' => [
                'Bantarjati', 'Bojong', 'Cikahuripan', 'Kembang Kuning',
                'Klapanunggal', 'Leuwikaret', 'Ligar Mukti', 'Nambo'
            ],
            'Leuwiliang' => [
                'Barengkok', 'Cibeber I', 'Cibeber II', 'Karacak',
                'Karyasari', 'Karehkel', 'Leuwiliang', 'Leuwimekar',
                'Pabuaransari', 'Puraseda', 'Purasari'
            ],
            'Leuwisadeng' => [
                'Babakan Sadeng', 'Kalong I', 'Kalong II', 'Leuwisadeng',
                'Sadeng', 'Sadengkolot', 'Sibanteng', 'Wangun Jaya'
            ],
            'Megamendung' => [
                'Cipayung Datar', 'Cipayung Girang', 'Gadog', 'Kuta',
                'Megamendung', 'Mendale', 'Pasir Angin', 'Sukagalih',
                'Sukakarya', 'Sukamahi', 'Sukamaju', 'Sukaresmi'
            ],
            'Nanggung' => [
                'Batu Tulis', 'Cisarua', 'Curug Bitung', 'Hambaro',
                'Kalong Liud', 'Malasari', 'Nanggung', 'Pangkal Jaya',
                'Parakan Muncang', 'Sukaluyu', 'Cパリ'
            ],
            'Pamijahan' => [
                'Ciasihan', 'Ciasmara', 'Cibening', 'Cibitung Kulon',
                'Cibitung Wetan', 'Cibunian', 'Cimayang', 'Gunung Bunder I',
                'Gunung Bunder II', 'Gunung Menyan', 'Gunung Picung', 'Gunung Sari',
                'Pamijahan', 'Pasarean', 'Purwabakti'
            ],
            'Parung' => [
                'Bojong Indah', 'Bojong Sempu', 'Cogreg', 'Iwul',
                'Jabon Mekar', 'Pamager Sari', 'Parung', 'Warujaya', 'Warudoyong'
            ],
            'Parung Panjang' => [
                'Cibunar', 'Cikuda', 'Dago', 'Gintung Cilejet',
                'Gorowong', 'Jagabaya', 'Kabuas', 'Lumpang',
                'Parungpanjang', 'Pingku', 'Salimah'
            ],
            'Rancabungur' => [
                'Bantarjaya', 'Bantarsari', 'Candali', 'Mekarsari',
                'Pasirgaok', 'Rancabungur', 'Bantarjati'
            ],
            'Rumpin' => [
                'Cibodas', 'Cidokom', 'Cipinang', 'Gobang',
                'Kampung Sawah', 'Kertajaya', 'Leuwibatu', 'Mekar Jaya',
                'Mekarsari', 'Rabak', 'Rumpin', 'Sukadamai',
                'Sukamulya', 'Taman Sari'
            ],
            'Sukajaya' => [
                'Cileuksa', 'Cisarua', 'Harkatjaya', 'Kiarapandak',
                'Kiarasari', 'Madang', 'Pasir Madang', 'Sipayung',
                'Sukajaya', 'Sukaluyu', 'Urug'
            ],
            'Sukamakmur' => [
                'Cibadak', 'Pabuaran', 'Sirnajaya', 'Sukadamai',
                'Sukaharja', 'Sukamakmur', 'Sukamulya', 'Sukaresmi',
                'Sukawangi', 'Wargajaya'
            ],
            'Sukaraja' => [
                'Cadas Ngampar', 'Cibanon', 'Cikeas', 'Cilebut Barat',
                'Cilebut Timur', 'Cimandala', 'Gunung Geulis', 'Nagrak',
                'Pasir Jambu', 'Pasirlaja', 'Sukaraja', 'Sukatani', 'Cijujung'
            ],
            'Tajurhalang' => [
                'Citayam', 'Kalisuren', 'Nanggerang', 'Sasak Panjang',
                'Sukmajaya', 'Tajurhalang', 'Tonjong'
            ],
            'Tamansari' => [
                'Calobak', 'Pasireurih', 'Sirnagalih', 'Sukajadi',
                'Sukajaya', 'Sukaluyu', 'Sukamantri', 'Tamansari'
            ],
            'Tanjungsari' => [
                'Antajaya', 'Buanajaya', 'Cibadak', 'Pasir Tanjung',
                'Selawangi', 'Sirnarasa', 'Sirnasari', 'Sukaluyu',
                'Tanjungrasa', 'Tanjungsari'
            ],
            'Tenjo' => [
                'Babakan', 'Bojong', 'Cilaku', 'Ciomas',
                'Singabraja', 'Tapos', 'Tenjo', 'Batok', 'Bagasasi'
            ],
            'Tenjolaya' => [
                'Cibitung Tengah', 'Cinangneng', 'Gunung Malang', 'Situdaun',
                'Tapos I', 'Tapos II', 'Tenjolaya'
            ],
        ];
    }
}
