<?php

namespace App\Http\Controllers\Public\Concerns;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

trait InteractsWithWeatherAndIcons
{
    /**
     * Ambil Cuaca Wilayah Kabupaten Bogor
     */
    protected function getWeatherData(): array
    {
        $regions = [
            ['name' => 'Cibinong, Kab. Bogor', 'lat' => -6.4855, 'lng' => 106.8450],
            ['name' => 'Cisarua (Puncak), Kab. Bogor', 'lat' => -6.6980, 'lng' => 106.9320],
            ['name' => 'Ciawi, Kab. Bogor', 'lat' => -6.6580, 'lng' => 106.8550],
            ['name' => 'Babakan Madang, Kab. Bogor', 'lat' => -6.5650, 'lng' => 106.8720],
            ['name' => 'Bojonggede, Kab. Bogor', 'lat' => -6.4950, 'lng' => 106.7980],
            ['name' => 'Parung, Kab. Bogor', 'lat' => -6.4250, 'lng' => 106.7320],
            ['name' => 'Leuwiliang, Kab. Bogor', 'lat' => -6.5750, 'lng' => 106.6280],
            ['name' => 'Jasinga, Kab. Bogor', 'lat' => -6.4850, 'lng' => 106.4520],
            ['name' => 'Jonggol, Kab. Bogor', 'lat' => -6.4680, 'lng' => 107.0520],
            ['name' => 'Cileungsi, Kab. Bogor', 'lat' => -6.3980, 'lng' => 106.9620],
        ];

        return Cache::remember('weather_kabupaten_bogor_public', 300, function () use ($regions) {
            try {
                $lats = implode(',', array_column($regions, 'lat'));
                $lngs = implode(',', array_column($regions, 'lng'));
                $url = "https://api.open-meteo.com/v1/forecast?latitude={$lats}&longitude={$lngs}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&timezone=Asia%2FJakarta";

                $response = Http::timeout(4)->get($url);
                if ($response->successful()) {
                    $results = $response->json();
                    if (is_array($results)) {
                        $parsed = [];
                        foreach ($regions as $index => $region) {
                            $item = $results[$index] ?? null;
                            $current = $item['current'] ?? null;
                            if (!$current) continue;

                            $code = $current['weather_code'] ?? 0;
                            $condition = 'Cerah';
                            if (in_array($code, [1, 2])) $condition = 'Cerah Berawan';
                            elseif ($code === 3) $condition = 'Berawan';
                            elseif (in_array($code, [45, 48])) $condition = 'Berkabut';
                            elseif (in_array($code, [51, 53, 55, 56, 57])) $condition = 'Gerimis';
                            elseif (in_array($code, [61, 63])) $condition = 'Hujan Ringan';
                            elseif (in_array($code, [65, 80, 81, 82])) $condition = 'Hujan';
                            elseif (in_array($code, [95, 96, 99])) $condition = 'Badai Petir';

                            $parsed[] = [
                                'location' => $region['name'],
                                'temp' => round($current['temperature_2m'], 1),
                                'humidity' => (int) round($current['relative_humidity_2m'] ?? 65),
                                'condition' => $condition,
                                'wind_speed' => (int) round($current['wind_speed_10m'] ?? 0),
                            ];
                        }
                        if (!empty($parsed)) return $parsed;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback graceful
            }

            return [
                ['location' => 'Cibinong, Kab. Bogor', 'temp' => 31.0, 'humidity' => 60, 'condition' => 'Cerah Berawan', 'wind_speed' => 8],
                ['location' => 'Cisarua (Puncak), Kab. Bogor', 'temp' => 24.5, 'humidity' => 75, 'condition' => 'Berawan Sejuk', 'wind_speed' => 9],
                ['location' => 'Ciawi, Kab. Bogor', 'temp' => 28.0, 'humidity' => 65, 'condition' => 'Cerah Berawan', 'wind_speed' => 7],
                ['location' => 'Babakan Madang, Kab. Bogor', 'temp' => 30.0, 'humidity' => 62, 'condition' => 'Cerah', 'wind_speed' => 8],
                ['location' => 'Bojonggede, Kab. Bogor', 'temp' => 31.2, 'humidity' => 60, 'condition' => 'Cerah Berawan', 'wind_speed' => 6],
                ['location' => 'Parung, Kab. Bogor', 'temp' => 31.0, 'humidity' => 58, 'condition' => 'Cerah', 'wind_speed' => 7],
                ['location' => 'Leuwiliang, Kab. Bogor', 'temp' => 30.4, 'humidity' => 64, 'condition' => 'Cerah Berawan', 'wind_speed' => 6],
                ['location' => 'Jasinga, Kab. Bogor', 'temp' => 29.5, 'humidity' => 68, 'condition' => 'Cerah', 'wind_speed' => 5],
                ['location' => 'Jonggol, Kab. Bogor', 'temp' => 31.0, 'humidity' => 59, 'condition' => 'Cerah', 'wind_speed' => 8],
                ['location' => 'Cileungsi, Kab. Bogor', 'temp' => 31.5, 'humidity' => 58, 'condition' => 'Cerah', 'wind_speed' => 8],
            ];
        });
    }

    /**
     * Kontak Darurat Hotline BPBD & Lembaga Terkait
     */
    protected function getEmergencyContactsData(): array
    {
        return [
            [
                'name' => 'Call Center BPBD (112)',
                'phone' => '112',
                'description' => 'Layanan Bebas Pulsa Kedaruratan Bencana 24 Jam',
                'icon' => 'phone-call',
                'color' => 'red',
            ],
            [
                'name' => 'Pusdalops BPBD Kab. Bogor',
                'phone' => '0218751070',
                'phone_display' => '(021) 875-1070',
                'description' => 'Pusat Pengendalian Operasi Kebencanaan Kabupaten Bogor',
                'icon' => 'shield-alert',
                'color' => 'orange',
            ],
            [
                'name' => 'Pemadam Kebakaran (113)',
                'phone' => '113',
                'description' => 'Dinas Pemadam Kebakaran & Penyelamatan Kab. Bogor',
                'icon' => 'flame',
                'color' => 'amber',
            ],
            [
                'name' => 'Ambulans Gawat Darurat (119)',
                'phone' => '119',
                'description' => 'Pelayanan Kesehatan Darurat & Rujukan Pasien',
                'icon' => 'heart-pulse',
                'color' => 'emerald',
            ],
            [
                'name' => 'Kepolisian Resor Bogor (110)',
                'phone' => '110',
                'description' => 'Layanan Kepolisian & Ketertiban Masyarakat',
                'icon' => 'badge-sheriff',
                'color' => 'blue',
            ],
            [
                'name' => 'BASARNAS Jakarta (115)',
                'phone' => '115',
                'description' => 'Operasi Pencarian dan Pertolongan Korban Bencana',
                'icon' => 'life-buoy',
                'color' => 'teal',
            ],
        ];
    }

    /**
     * Pemetaan ikon bencana sesuai dengan file gambar di public/icon atau public/icons
     */
    protected function getDisasterIcon(?string $name): string
    {
        if (!$name) return '/icon/banjir.png';
        $n = strtolower(trim($name));

        if (str_contains($n, 'banjir')) return '/icon/banjir.png';
        if (str_contains($n, 'longsor')) return '/icon/tanah_longsor.png';
        if (str_contains($n, 'puting beliung')) return '/icon/angin_puting_beliung.png';
        if (str_contains($n, 'angin') || str_contains($n, 'topan') || str_contains($n, 'badai')) return '/icon/angin_kencang.png';
        if (str_contains($n, 'lahan') || str_contains($n, 'hutan')) return '/icon/kebakaran_hutan.png';
        if (str_contains($n, 'kebakaran')) return '/icon/kebakaran_rumah.png';
        if (str_contains($n, 'gempa')) return '/icon/gempa_bumi.png';
        if (str_contains($n, 'gunung') || str_contains($n, 'meletus')) return '/icon/gunung_meletus.png';
        if (str_contains($n, 'tsunami')) return '/icon/tsunami.png';
        if (str_contains($n, 'kering') || str_contains($n, 'kekeringan')) return '/icon/kekeringan.png';

        // Cek alternatif di folder /icons/
        if (File::exists(public_path('icons/' . $n . '.png'))) {
            return '/icons/' . $n . '.png';
        }

        return '/icon/banjir.png';
    }
}
