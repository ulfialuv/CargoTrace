<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BeritaController extends Controller
{
    /**
     * Halaman utama berita CargoTrace.
     */
    public function index()
    {
        return view('berita.index');
    }

    /**
     * API berita GNews.
     */
    public function api(Request $request)
    {
        $apiKey = config('services.gnews.key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key GNews belum dikonfigurasi.',
                'articles' => [],
            ], 500);
        }

        $keyword = trim((string) $request->query('q', ''));

        $category = strtolower(
            trim((string) $request->query('category', 'all'))
        );

        $page = max(1, (int) $request->query('page', 1));

        $allowedCategories = [
            'all',
            'nasional',
            'internasional',
            'politik',
            'ekonomi',
            'bisnis',
            'teknologi',
            'olahraga',
            'hiburan',
            'kesehatan',
            'pendidikan',
            'lingkungan',
            'logistik',
            'transportasi',
        ];

        if (!in_array($category, $allowedCategories, true)) {
            $category = 'all';
        }

        try {
            /*
            |--------------------------------------------------------------------------
            | Pencarian berita
            |--------------------------------------------------------------------------
            |
            | Jika pengguna memasukkan kata pencarian, gunakan endpoint search.
            | GNews Search tidak memiliki parameter kategori seperti Top Headlines.
            |
            */

            if ($keyword !== '') {

                $searchQuery = $keyword;

                /*
                 * Jika kategori tertentu dipilih bersama pencarian,
                 * tambahkan konteks kategori ke query.
                 *
                 * Contoh:
                 * keyword = korea selatan
                 * category = teknologi
                 */

                if ($category !== 'all') {
                    $categoryKeyword = $this->getCategorySearchKeyword(
                        $category
                    );

                    if ($categoryKeyword !== '') {
                        $searchQuery = $keyword . ' ' . $categoryKeyword;
                    }
                }

                $response = Http::timeout(25)
                    ->get(
                        'https://gnews.io/api/v4/search',
                        [
                            'q' => $searchQuery,
                            'lang' => 'id',
                            'country' => 'id',
                            'max' => 10,
                            'page' => $page,
                            'apikey' => $apiKey,
                        ]
                    );

            } else {

                /*
                |--------------------------------------------------------------------------
                | Berita berdasarkan kategori
                |--------------------------------------------------------------------------
                */

                $apiCategory = $this->getApiCategory($category);

                $response = Http::timeout(25)
                    ->get(
                        'https://gnews.io/api/v4/top-headlines',
                        [
                            'category' => $apiCategory,
                            'lang' => 'id',
                            'country' => 'id',
                            'max' => 10,
                            'page' => $page,
                            'apikey' => $apiKey,
                        ]
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi respons GNews
            |--------------------------------------------------------------------------
            */

            if (!$response->successful()) {

                Log::warning('GNews API gagal.', [
                    'status' => $response->status(),
                    'category' => $category,
                    'keyword' => $keyword,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'GNews gagal mengambil berita. Coba kembali.',
                    'articles' => [],
                ], 502);
            }

            $data = $response->json();

            /*
            |--------------------------------------------------------------------------
            | Format artikel
            |--------------------------------------------------------------------------
            */

            $articles = collect($data['articles'] ?? [])
                ->map(function ($article) {

                    return [
                        'title' => $article['title']
                            ?? 'Judul tidak tersedia',

                        'description' => $article['description']
                            ?? '',

                        'image' => $article['image']
                            ?? null,

                        'url' => $article['url']
                            ?? '#',

                        'source' => $article['source']['name']
                            ?? 'Sumber tidak tersedia',

                        'publishedAt' => $article['publishedAt']
                            ?? null,

                        'content' => $article['content']
                            ?? '',
                    ];

                })
                ->values();

            /*
            |--------------------------------------------------------------------------
            | Respons JSON
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'success' => true,

                'totalArticles' => $data['totalArticles']
                    ?? $articles->count(),

                'articles' => $articles,

                'page' => $page,

                'category' => $category,

                'keyword' => $keyword,

                'updatedAt' => now()->toIso8601String(),
            ]);

        } catch (\Throwable $e) {

            Log::error('Kesalahan mengambil berita GNews.', [
                'message' => $e->getMessage(),
                'category' => $category,
                'keyword' => $keyword,
            ]);

            return response()->json([
                'success' => false,

                'message' => 'Tidak dapat menghubungi layanan berita. Periksa koneksi internet.',

                'articles' => [],
            ], 500);
        }
    }

    /**
     * Konversi kategori CargoTrace ke kategori GNews.
     */
    private function getApiCategory(string $category): string
    {
        return match ($category) {

            'nasional' => 'nation',

            'internasional' => 'world',

            'politik' => 'nation',

            'ekonomi' => 'business',

            'bisnis' => 'business',

            'teknologi' => 'technology',

            'olahraga' => 'sports',

            'hiburan' => 'entertainment',

            'kesehatan' => 'health',

            'pendidikan' => 'nation',

            'lingkungan' => 'science',

            'logistik' => 'business',

            'transportasi' => 'business',

            default => 'general',
        };
    }

    /**
     * Kata tambahan untuk pencarian berdasarkan kategori.
     */
    private function getCategorySearchKeyword(string $category): string
    {
        return match ($category) {

            'nasional' => 'Indonesia',

            'internasional' => 'international world',

            'politik' => 'politik pemerintahan',

            'ekonomi' => 'ekonomi',

            'bisnis' => 'bisnis perusahaan',

            'teknologi' => 'teknologi digital',

            'olahraga' => 'olahraga',

            'hiburan' => 'hiburan',

            'kesehatan' => 'kesehatan',

            'pendidikan' => 'pendidikan',

            'lingkungan' => 'lingkungan',

            'logistik' => 'logistik pengiriman',

            'transportasi' => 'transportasi',

            default => '',
        };
    }
}
