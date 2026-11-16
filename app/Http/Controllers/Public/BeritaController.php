<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Public\Concerns\InteractsWithWeatherAndIcons;
use App\Models\Berita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    use InteractsWithWeatherAndIcons;

    /**
     * Halaman Publikasi & Berita Kebencanaan
     */
    public function news(Request $request)
    {
        $query = Berita::orderBy('created_at', 'desc');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($b) use ($q) {
                $b->where('judul', 'like', "%{$q}%")
                    ->orWhere('isi', 'like', "%{$q}%")
                    ->orWhere('sumber', 'like', "%{$q}%");
            });
        }

        $news = $query->paginate(9);
        $weather = $this->getWeatherData();

        return view('public.news', [
            'news' => $news,
            'weather' => $weather,
        ]);
    }

    /**
     * Detail Berita Kebencanaan
     */
    public function newsDetail($id)
    {
        $article = Berita::findOrFail($id);
        $relatedNews = Berita::where('id_berita', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        $weather = $this->getWeatherData();

        return view('public.news-detail', [
            'article' => $article,
            'relatedNews' => $relatedNews,
            'weather' => $weather,
        ]);
    }
}
