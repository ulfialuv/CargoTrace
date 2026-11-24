<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Berita ala Google News, lengkap dengan gambar.
 * Sumber (diambil bersamaan, lalu digabung):
 *  1. Bing News RSS  : ada gambar thumbnail + ringkasan, tanpa API key
 *  2. GNews API      : ada gambar (hanya jika services.gnews.key diisi)
 *  3. Google News RSS: cadangan agar hasil selalu banyak, tanpa gambar
 */
class BeritaController extends Controller
{
    private const REGIONS = [
        'id'    => ['label' => 'Indonesia',        'hl' => 'id',    'gl' => 'ID', 'ceid' => 'ID:id'],
        'world' => ['label' => 'Dunia (English)',  'hl' => 'en',    'gl' => 'US', 'ceid' => 'US:en'],
        'my'    => ['label' => 'Malaysia',         'hl' => 'ms',    'gl' => 'MY', 'ceid' => 'MY:ms'],
        'sg'    => ['label' => 'Singapura',        'hl' => 'en',    'gl' => 'SG', 'ceid' => 'SG:en'],
        'ph'    => ['label' => 'Filipina',         'hl' => 'en',    'gl' => 'PH', 'ceid' => 'PH:en'],
        'au'    => ['label' => 'Australia',        'hl' => 'en',    'gl' => 'AU', 'ceid' => 'AU:en'],
        'gb'    => ['label' => 'Inggris',          'hl' => 'en',    'gl' => 'GB', 'ceid' => 'GB:en'],
        'in'    => ['label' => 'India',            'hl' => 'en',    'gl' => 'IN', 'ceid' => 'IN:en'],
        'jp'    => ['label' => 'Jepang',           'hl' => 'ja',    'gl' => 'JP', 'ceid' => 'JP:ja'],
        'kr'    => ['label' => 'Korea Selatan',    'hl' => 'ko',    'gl' => 'KR', 'ceid' => 'KR:ko'],
        'cn'    => ['label' => 'Tiongkok (Taiwan)', 'hl' => 'zh-TW', 'gl' => 'TW', 'ceid' => 'TW:zh-Hant'],
        'de'    => ['label' => 'Jerman',           'hl' => 'de',    'gl' => 'DE', 'ceid' => 'DE:de'],
        'fr'    => ['label' => 'Prancis',          'hl' => 'fr',    'gl' => 'FR', 'ceid' => 'FR:fr'],
        'es'    => ['label' => 'Spanyol',          'hl' => 'es',    'gl' => 'ES', 'ceid' => 'ES:es'],
        'br'    => ['label' => 'Brasil',           'hl' => 'pt-BR', 'gl' => 'BR', 'ceid' => 'BR:pt-419'],
        'sa'    => ['label' => 'Arab Saudi',       'hl' => 'ar',    'gl' => 'SA', 'ceid' => 'SA:ar'],
        'za'    => ['label' => 'Afrika Selatan',   'hl' => 'en',    'gl' => 'ZA', 'ceid' => 'ZA:en'],
    ];

    private const TOPICS = [
        'nation'        => 'NATION',
        'world'         => 'WORLD',
        'business'      => 'BUSINESS',
        'technology'    => 'TECHNOLOGY',
        'science'       => 'SCIENCE',
        'health'        => 'HEALTH',
        'sports'        => 'SPORTS',
        'entertainment' => 'ENTERTAINMENT',
    ];

    private const WHEN = ['any', '1h', '1d', '7d', '30d', '1y'];

    public function index()
    {
        $regions = collect(self::REGIONS)->map(fn ($r) => ['label' => $r['label']])->all();

        return view('berita.index', compact('regions'));
    }

    public function api(Request $request)
    {
        $q = trim(mb_substr((string) $request->query('q', ''), 0, 150));
        $category = strtolower(trim((string) $request->query('category', 'all')));
        $region = strtolower(trim((string) $request->query('region', 'id')));
        $when = strtolower(trim((string) $request->query('when', 'any')));

        if (!isset(self::REGIONS[$region])) {
            $region = 'id';
        }
        if (!in_array($when, self::WHEN, true)) {
            $when = 'any';
        }

        try {
            $articles = Cache::remember('berita.v3.' . md5($q . $category . $region . $when), 300, fn () => $this->collect($q, $category, $region, $when));
        } catch (\Throwable $e) {
            Log::warning('Berita gagal dimuat.', ['q' => $q, 'message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Sumber berita sedang tidak bisa dihubungi. Coba lagi sebentar.',
                'articles' => [],
            ], 502);
        }

        return response()->json([
            'success' => true,
            'total' => count($articles),
            'articles' => $articles,
            'region' => self::REGIONS[$region]['label'],
            'query' => $q,
            'category' => $category,
            'updatedAt' => now()->toIso8601String(),
        ]);
    }

    private const BING_MKT = [
        'id' => 'id-ID', 'world' => 'en-US', 'my' => 'ms-MY', 'sg' => 'en-SG', 'ph' => 'en-PH', 'au' => 'en-AU',
        'gb' => 'en-GB', 'in' => 'en-IN', 'jp' => 'ja-JP', 'kr' => 'ko-KR', 'cn' => 'zh-TW', 'de' => 'de-DE',
        'fr' => 'fr-FR', 'es' => 'es-ES', 'br' => 'pt-BR', 'sa' => 'ar-SA', 'za' => 'en-ZA',
    ];

    private const TOPIC_WORDS_ID = [
        'nation' => 'berita nasional Indonesia', 'world' => 'berita dunia internasional', 'business' => 'ekonomi bisnis',
        'technology' => 'teknologi', 'science' => 'sains penelitian', 'health' => 'kesehatan',
        'sports' => 'olahraga', 'entertainment' => 'hiburan selebriti', 'logistics' => 'logistik pengiriman kargo pelabuhan',
    ];

    private const TOPIC_WORDS_EN = [
        'nation' => 'national news', 'world' => 'world news', 'business' => 'business economy',
        'technology' => 'technology', 'science' => 'science research', 'health' => 'health',
        'sports' => 'sports', 'entertainment' => 'entertainment', 'logistics' => 'logistics freight cargo shipping port',
    ];

    private function collect(string $q, string $category, string $region, string $when): array
    {
        $r = self::REGIONS[$region];
        $isId = $r['hl'] === 'id';
        $words = $isId ? self::TOPIC_WORDS_ID : self::TOPIC_WORDS_EN;
        $searchQ = $q !== '' ? $q : ($words[$category] ?? ($isId ? 'berita terbaru' : 'latest news'));
        $key = config('services.gnews.key');

        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
            'Accept' => 'application/rss+xml, application/xml, text/xml, application/json, */*',
        ];

        $bingUrl = 'https://www.bing.com/news/search?format=rss&q=' . rawurlencode($searchQ)
            . '&setmkt=' . self::BING_MKT[$region] . '&setlang=' . $r['hl']
            . ($when !== 'any' ? '&qft=' . rawurlencode($this->bingInterval($when)) : '');

        $googleUrl = $this->googleUrl($q, $category, $region, $when);

        $responses = Http::pool(function (Pool $pool) use ($headers, $bingUrl, $googleUrl, $key, $searchQ, $q, $category, $r) {
            $calls = [
                $pool->as('bing0')->withHeaders($headers)->timeout(12)->get($bingUrl),
                $pool->as('bing1')->withHeaders($headers)->timeout(12)->get($bingUrl . '&first=11&count=10'),
                $pool->as('bing2')->withHeaders($headers)->timeout(12)->get($bingUrl . '&first=21&count=10'),
                $pool->as('google')->withHeaders($headers)->timeout(12)->get($googleUrl),
            ];

            if ($key) {
                $topic = ['nation' => 'nation', 'world' => 'world', 'business' => 'business', 'technology' => 'technology',
                    'science' => 'science', 'health' => 'health', 'sports' => 'sports', 'entertainment' => 'entertainment'][$category] ?? 'general';
                $params = ['lang' => substr($r['hl'], 0, 2), 'country' => strtolower($r['gl']), 'max' => 10, 'apikey' => $key];

                $calls[] = ($q !== '' || $category === 'logistics')
                    ? $pool->as('gnews')->timeout(12)->get('https://gnews.io/api/v4/search', $params + ['q' => $searchQ])
                    : $pool->as('gnews')->timeout(12)->get('https://gnews.io/api/v4/top-headlines', $params + ['category' => $topic]);
            }

            return $calls;
        });

        $lists = [];

        foreach (['bing0' => 'parseBing', 'gnews' => 'parseGnews', 'bing1' => 'parseBing', 'bing2' => 'parseBing', 'google' => 'parseGoogle'] as $name => $method) {
            $res = $responses[$name] ?? null;
            if (!$res instanceof Response || !$res->successful()) {
                continue;
            }
            try {
                $lists[] = $this->$method($res->body());
            } catch (\Throwable $e) {
                Log::info("Sumber berita {$name} dilewati.", ['message' => $e->getMessage()]);
            }
        }

        if (!$lists) {
            throw new \RuntimeException('Semua sumber berita gagal');
        }

        return $this->merge($lists);
    }

    private function bingInterval(string $when): string
    {
        return match ($when) {
            '1h' => 'interval="4"',
            '1d' => 'interval="7"',
            '7d' => 'interval="8"',
            '30d' => 'interval="9"',
            default => '',
        };
    }

    private function googleUrl(string $q, string $category, string $region, string $when): string
    {
        $r = self::REGIONS[$region];
        $params = 'hl=' . urlencode($r['hl']) . '&gl=' . $r['gl'] . '&ceid=' . urlencode($r['ceid']);
        $base = 'https://news.google.com/rss';

        if ($q === '' && $category === 'logistics') {
            $q = $r['hl'] === 'id'
                ? 'logistik OR "pengiriman barang" OR kargo OR pelabuhan'
                : 'logistics OR freight OR "cargo shipping" OR port';
        }

        if ($q !== '') {
            return $base . '/search?q=' . rawurlencode($q . ($when !== 'any' ? ' when:' . $when : '')) . '&' . $params;
        }

        if (isset(self::TOPICS[$category])) {
            return $base . '/headlines/section/topic/' . self::TOPICS[$category] . '?' . $params;
        }

        return $base . '?' . $params;
    }

    private function xml(string $body): \SimpleXMLElement
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET);

        if ($xml === false || !isset($xml->channel)) {
            throw new \RuntimeException('Format RSS tidak valid');
        }

        return $xml;
    }

    private function iso(?string $v): ?string
    {
        try {
            return $v ? Carbon::parse($v)->toIso8601String() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function clean(?string $v, int $max = 220): string
    {
        $t = trim(preg_replace('/\s+/u', ' ', strip_tags(html_entity_decode((string) $v, ENT_QUOTES | ENT_HTML5, 'UTF-8'))));

        return mb_strlen($t) > $max ? rtrim(mb_substr($t, 0, $max)) . '…' : $t;
    }

    private function parseBing(string $body): array
    {
        // Prefix namespace "News:" diubah supaya elemen bisa dibaca langsung (News_Image, News_Source).
        $body = preg_replace('/<(\/?)News:/i', '<$1News_', $body);
        $xml = $this->xml($body);
        $out = [];

        foreach ($xml->channel->item as $item) {
            $title = $this->clean((string) $item->title, 300);
            $link = trim((string) $item->link);

            // Link Bing berisi url asli di parameter "url".
            parse_str((string) parse_url($link, PHP_URL_QUERY), $qs);
            if (!empty($qs['url'])) {
                $link = $qs['url'];
            }

            if ($title === '' || !preg_match('#^https?://#i', $link)) {
                continue;
            }

            $img = trim((string) ($item->News_Image ?? ''));
            if ($img !== '') {
                $img = preg_replace('#^http://#i', 'https://', $img);
                if (str_contains($img, 'bing.com/th') && !str_contains($img, 'w=')) {
                    $img .= '&w=640&h=360&c=7&rs=1';
                }
            }

            $out[] = [
                'title' => $title,
                'url' => $link,
                'image' => $img !== '' ? $img : null,
                'description' => $this->clean((string) $item->description),
                'source' => trim((string) ($item->News_Source ?? '')) ?: (parse_url($link, PHP_URL_HOST) ?: 'Sumber tidak diketahui'),
                'host' => parse_url($link, PHP_URL_HOST) ?: null,
                'publishedAt' => $this->iso((string) $item->pubDate),
            ];
        }

        return $out;
    }

    private function parseGnews(string $body): array
    {
        $data = json_decode($body, true);
        $out = [];

        foreach (($data['articles'] ?? []) as $a) {
            if (empty($a['title']) || empty($a['url'])) {
                continue;
            }
            $out[] = [
                'title' => $this->clean($a['title'], 300),
                'url' => $a['url'],
                'image' => !empty($a['image']) ? $a['image'] : null,
                'description' => $this->clean($a['description'] ?? ''),
                'source' => $a['source']['name'] ?? 'Sumber tidak diketahui',
                'host' => parse_url($a['source']['url'] ?? $a['url'], PHP_URL_HOST) ?: null,
                'publishedAt' => $this->iso($a['publishedAt'] ?? null),
            ];
        }

        return $out;
    }

    private function parseGoogle(string $body): array
    {
        $xml = $this->xml($body);
        $out = [];

        foreach ($xml->channel->item as $item) {
            $source = trim((string) $item->source);
            $title = trim(html_entity_decode((string) $item->title, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $link = trim((string) $item->link);

            if ($source !== '' && str_ends_with($title, ' - ' . $source)) {
                $title = rtrim(mb_substr($title, 0, mb_strlen($title) - mb_strlen($source) - 3));
            }

            if ($title === '' || $link === '') {
                continue;
            }

            $out[] = [
                'title' => $title,
                'url' => $link,
                'image' => null,
                'description' => '',
                'source' => $source !== '' ? $source : 'Sumber tidak diketahui',
                'host' => parse_url((string) $item->source['url'], PHP_URL_HOST) ?: null,
                'publishedAt' => $this->iso((string) $item->pubDate),
            ];
        }

        return $out;
    }

    // Gabungkan semua sumber, buang judul kembar, lengkapi gambar/ringkasan yang kosong.
    private function merge(array $lists): array
    {
        $seen = [];
        $out = [];
        $i = 0;
        $max = max(array_map('count', $lists));

        // Selang-seling antar sumber supaya campuran gambar & non-gambar merata.
        for ($i = 0; $i < $max; $i++) {
            foreach ($lists as $list) {
                if (!isset($list[$i])) {
                    continue;
                }
                $a = $list[$i];
                $k = mb_strtolower(preg_replace('/[^\p{L}\p{N}]+/u', '', $a['title']));
                if ($k === '') {
                    continue;
                }
                if (isset($seen[$k])) {
                    $j = $seen[$k];
                    $out[$j]['image'] ??= $a['image'];
                    if ($out[$j]['description'] === '') {
                        $out[$j]['description'] = $a['description'];
                    }
                    continue;
                }
                $seen[$k] = count($out);
                $out[] = $a;
            }
        }

        // Berita ber-gambar didahulukan. Yang tanpa gambar (umumnya dari Google News) hanya
        // dipakai sebagai pengisi bila berita ber-gambar kurang dari 12.
        $with = array_values(array_filter($out, fn ($a) => !empty($a['image'])));

        if (count($with) >= 12) {
            return array_slice($with, 0, 100);
        }

        $without = array_values(array_filter($out, fn ($a) => empty($a['image'])));

        return array_slice(array_merge($with, $without), 0, 100);
    }
}
