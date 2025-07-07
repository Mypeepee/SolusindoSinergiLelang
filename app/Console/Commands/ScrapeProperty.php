<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Spatie\Browsershot\Browsershot;
use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScrapeProperty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrape-property {kategori}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape semua URL detail property Rumah dari lelang.go.id';

    /**
     * Execute the console command.
     */
    private function parseTanggalIndonesia($tanggalString)
{
    if (empty($tanggalString)) {
        return null; // 🛡️ Jika kosong, kembalikan null
    }

    $bulanMap = [
        'Januari' => '01',
        'Februari' => '02',
        'Maret' => '03',
        'April' => '04',
        'Mei' => '05',
        'Juni' => '06',
        'Juli' => '07',
        'Agustus' => '08',
        'September' => '09',
        'Oktober' => '10',
        'November' => '11',
        'Desember' => '12',
    ];

    // 🔥 Bersihkan WIB/WITA/WIT
    $tanggalString = preg_replace('/\s*(WIB|WITA|WIT)\s*/i', '', $tanggalString);

    // 🔥 Ganti bulan Indonesia ke angka
    foreach ($bulanMap as $indo => $num) {
        if (stripos($tanggalString, $indo) !== false) {
            $tanggalString = str_ireplace($indo, $num, $tanggalString);
            break;
        }
    }

    // 🛡️ Cek apakah string punya jam
    $hasTime = preg_match('/\d{1,2}:\d{2}/', $tanggalString);

    try {
        if ($hasTime) {
            return \Carbon\Carbon::createFromFormat('d m Y H:i', trim($tanggalString));
        } else {
            return \Carbon\Carbon::createFromFormat('d m Y', trim($tanggalString));
        }
    } catch (\Exception $e) {
        // 🛡️ Fallback: log error dan kembalikan null
        \Log::warning("Gagal parse tanggal: '{$tanggalString}' - {$e->getMessage()}");
        return null;
    }
}

    public function handle()
{
    DB::statement("
    SELECT setval(
        pg_get_serial_sequence('property', 'id_listing'),
        COALESCE(MAX(id_listing), 1),
        true
    ) FROM property
");
$baseUrl = 'https://lelang.go.id';
$kategori = $this->argument('kategori') ?? 'Rumah'; // 🆕 bisa ganti kategori lewat argument artisan
$page = 1;
$allLinks = [];
$allData = []; // 🆕 untuk menyimpan link + gambar

$this->info("📄 Mulai scrape semua halaman kategori: $kategori");

// Ubah kategori jadi lowercase untuk field tipe di DB
$tipeProperti = strtolower($kategori); // e.g. Rumah -> rumah, Ruko -> ruko

while (true) {
    $listUrl = "$baseUrl/lot-lelang/katalog-lot-lelang?kategori=$kategori&page=$page";
    $this->info("🌐 Scraping halaman ke-$page: $listUrl");

        try {
            $html = \Spatie\Browsershot\Browsershot::url($listUrl)
        ->waitUntilNetworkIdle()
        ->waitForFunction('document.querySelectorAll("a[href*=\"/detail-auction/\"]").length > 0')
        ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/119 Safari/537.36')
        ->setOption('headless', false) // ⬅️ Debug visual
        ->timeout(90)
        ->bodyHtml();
        } catch (\Exception $e) {
            $this->warn("⚠️ Tidak ada data di halaman ke-$page. Stop scraping.");
            break;
        }

        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);

        $pageLinks = $crawler->filter('a')->each(function ($linkNode) use ($baseUrl) {
            $href = $linkNode->attr('href');
            if (str_contains($href, '/detail-auction/')) {
                $cleanUrl = rtrim(trim($baseUrl . $href), '/');
                return $cleanUrl;
            }
            return null;
        });

        $pageLinks = array_unique(array_filter($pageLinks));

        if (empty($pageLinks)) {
            $this->warn("❌ Tidak ada link detail property di halaman ke-$page.");
            break;
        }

        $this->info("🔗 Ditemukan " . count($pageLinks) . " link detail property di halaman $page.");

        foreach ($pageLinks as $detailUrl) {
            if (!in_array($detailUrl, $allLinks)) {
                $allLinks[] = $detailUrl;
                $this->info("➡️ $detailUrl");

                // 🔥 MASUK KE DETAIL & SCRAPE GAMBAR
                try {
                    $browser = \Spatie\Browsershot\Browsershot::url($detailUrl)
                        ->setOption('headless', false) // 🔥 Non-headless biar lihat proses
                        ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/119 Safari/537.36')
                        ->waitUntilNetworkIdle()
                        ->waitForFunction('document.querySelector("div.scrollbar-hide") !== null', null, 20000)
                        ->timeout(300); // Timeout lebih lama

                    sleep(2); // 🕒 Tunggu halaman stabil

                    // 🔥 Scroll bertahap ke kanan & pastikan semua gambar lazy-load
$browser->evaluate('
    (async () => {
        const scrollDiv = document.querySelector("div.scrollbar-hide");
        if (scrollDiv) {
            const totalWidth = scrollDiv.scrollWidth;
            const viewportWidth = scrollDiv.clientWidth;
            const step = viewportWidth / 2; // scroll setengah viewport
            for (let x = 0; x <= totalWidth; x += step) {
                scrollDiv.scrollLeft = x;
                await new Promise(resolve => setTimeout(resolve, 1000)); // ⏱️ tunggu 1 detik tiap scroll
            }
            // ⏩ Scroll ke kanan penuh sekali lagi sebagai jaga-jaga
            scrollDiv.scrollLeft = scrollDiv.scrollWidth;
            await new Promise(resolve => setTimeout(resolve, 1000));
        }
    })()
');

// 🕒 Tunggu sampai jumlah <img> stabil selama 2 detik
$browser->waitForFunction('
    (() => {
        let lastCount = 0;
        let stableTime = 0;
        const checkInterval = 500; // cek tiap 0.5 detik

        return new Promise(resolve => {
            const interval = setInterval(() => {
                const currentCount = document.querySelectorAll("div.scrollbar-hide img").length;
                if (currentCount === lastCount) {
                    stableTime += checkInterval;
                } else {
                    stableTime = 0;
                    lastCount = currentCount;
                }
                if (stableTime >= 2000) { // ✅ stabil selama 2 detik
                    clearInterval(interval);
                    resolve(true);
                }
            }, checkInterval);
        });
    })()
', null, 15000); // Timeout maksimum 15 detik


// 🔥 Ambil semua <img> setelah scroll selesai & stabil
$imageJson = $browser->evaluate('
JSON.stringify(
    Array.from(
        document.querySelectorAll("div.scrollbar-hide img")
    ).map(img => img.src)
    .filter(url => url && url.startsWith("https://file.lelang.go.id/lelang/photo_barang/"))
)
');
$imageUrls = json_decode($imageJson, true);
$allImages = array_unique(array_filter($imageUrls));

$detailsJson = $browser->evaluate('
JSON.stringify({
    judul: document.querySelector("h3.mb-5.text-2xl.text-ternary-gray-200")?.innerText || null,

    // 💰 Harga +26.8%
    harga: (function(){
        const h = document.querySelectorAll("h6.text-primary-500")[0]?.innerText || null;
        if (h) {
            const hargaAsli = parseInt(h.replace(/[^\d]/g, ""));
            const hargaMarkup = Math.round(hargaAsli * 1.268); // 🔥 Tambah 26.8%
            return hargaMarkup;
        }
        return null;
    })(),

    // 💵 Uang Jaminan +20%
    uang_jaminan: (function(){
        const u = document.querySelectorAll("h6.text-primary-500")[1]?.innerText || null;
        if (u) {
            const jaminanAsli = parseInt(u.replace(/[^\d]/g, ""));
            const jaminanMarkup = Math.round(jaminanAsli * 1.2); // 🔥 Tambah 20%
            return jaminanMarkup;
        }
        return null;
    })(),

    penjual: document.querySelectorAll("h6.text-ternary-gray-200")[0]?.innerText || null,
    batas_penawaran: document.querySelectorAll("h6.text-ternary-gray-200")[1]?.innerText || null,
    batas_setor_jaminan: document.querySelectorAll("h6.text-ternary-gray-200")[4]?.innerText || null,

    // 🎯 Ambil bukti kepemilikan & tanggal lebih aman
    bukti_kepemilikan_data: (function(){
        const blocks = Array.from(document.querySelectorAll("div.flex.w-full.flex-col"));
        const buktiBlock = blocks.find(block =>
            block.querySelector("div.mb-3")?.innerText.trim() === "Bukti Kepemilikan"
        );
        if (buktiBlock) {
            const textXs = buktiBlock.querySelectorAll("div.text-xs");
            return {
                bukti_kepemilikan: textXs[0]?.innerText.trim() || null,
            };
        }
        return { bukti_kepemilikan: null };
    })(),

    // 🎯 Ambil luas tanah (integer saja)
    luas_tanah: (function(){
        const div = Array.from(document.querySelectorAll("div.text-xs"))
                        .find(el => el.textContent.includes("Luas"));
        if (div) {
            const match = div.innerText.match(/Luas:\s*(\d+)/);
            return match ? parseInt(match[1]) : null;
        }
        return null;
    })(),

    alamat: (function(){
        const div = Array.from(document.querySelectorAll("div.text-xs"))
                        .find(el => el.textContent.includes("Alamat"));
        return div ? div.innerText.trim() : null;
    })()
})
');
$details = json_decode($detailsJson, true);

                    $buktiKepemilikan = $details['bukti_kepemilikan_data']['bukti_kepemilikan'] ?? null;

                    // 🆕 Convert harga & uang_jaminan ke integer
                    $hargaInt = null;
                    $uangJaminanInt = null;
                    if (!empty($details['harga'])) {
                        $hargaInt = (int) preg_replace('/[^\d]/', '', $details['harga']);
                    }
                    if (!empty($details['uang_jaminan'])) {
                        $uangJaminanInt = (int) preg_replace('/[^\d]/', '', $details['uang_jaminan']);
                    }

                    // 🆕 Parsing alamat ke provinsi, kabupaten/kota, kecamatan/kelurahan
                    $provinsi = null;
                    $kabupaten = null;
                    $kecamatan = null;
                    $kelurahan = null;

                    if (!empty($details['alamat'])) {
                        $alamat = $details['alamat'];

                        // 🎯 Cari Provinsi (support typo: Provinsi / Propinsi / Prov.)
                        if (preg_match('/\b(prov(?:insi)?|prop(?:insi)?)\s*([a-zA-Z\s]+)/i', $alamat, $provMatch)) {
                            $provinsi = strtoupper(trim($provMatch[2])); // 🔥 Kapital semua
                        }

                        // 🎯 Cari Kabupaten atau Kota
                        if (preg_match('/\b(kab(?:upaten|\.)?|kota)\s*([a-zA-Z\s]+)/i', $alamat, $kabMatch)) {
                            $namaKab = strtoupper(trim($kabMatch[2])); // Kapital semua
                            if (stripos($kabMatch[1], 'kota') !== false) {
                                $kabupaten = "KOTA " . $namaKab;
                            } else {
                                $kabupaten = "KAB. " . $namaKab;
                            }
                        }

                        // 🧹 Bersihkan alamat
                        if (!empty($details['alamat'])) {
                            $alamatClean = preg_replace('/^Alamat:\s*/i', '', $details['alamat']);
                            $details['alamat'] = trim($alamatClean);
                        }

                        $buktiKepemilikan = $details['bukti_kepemilikan_data']['bukti_kepemilikan'] ?? null;

                        if (!empty($buktiKepemilikan)) {
                            // ✅ Ambil SHM No + optional pemilik
                            if (preg_match('/(SHM\s+No\.\s*\d+(\/[^.,]*)?)/i', $buktiKepemilikan, $sertifikatMatch)) {
                                $buktiKepemilikan = trim($sertifikatMatch[1]);
                            } else {
                                // 🧹 Hapus "No:" di akhir jika tidak ada nomor
                                $buktiKepemilikan = preg_replace('/No:\s*$/i', '', $buktiKepemilikan);
                                $buktiKepemilikan = trim($buktiKepemilikan);
                            }
                        }

                        // 🎯 Cari Kecamatan
                        if (preg_match('/\bkec(?:amatan|\.)?\s*([a-zA-Z\s]+)/i', $alamat, $kecMatch)) {
                            $kecamatanRaw = trim($kecMatch[1]);
                            // 🧠 Bersihkan tambahan "Kota ..." / "Kabupaten ..."
                            $kecamatanClean = preg_replace('/\b(kota|kabupaten)\b.*$/i', '', $kecamatanRaw);
                            $kecamatan = ucwords(strtolower(trim($kecamatanClean))); // 🔥 Capitalize
                        }

                        // 🎯 Cari Kelurahan
                        if (preg_match('/\bkel(?:urahan|\.)?\s*([a-zA-Z\s]+)/i', $alamat, $kelMatch)) {
                            $kelurahanRaw = trim($kelMatch[1]);

                            // 🎯 Ambil hanya sampai sebelum kata kunci (Kec/Kab/Kota/Prov)
                            $kelurahanClean = preg_replace('/\s*(kec(?:amatan)?|kab(?:upaten)?|kota|prov(?:insi)?|prop(?:insi)?).*/i', '', $kelurahanRaw);
                            $kelurahan = ucwords(strtolower(trim($kelurahanClean))); // 🔥 Capitalize

                            // ✅ Tambahan pembersih: jika masih ada "Kec"/"Kab"/"Kota" di Kelurahan
                            if (stripos($kelurahan, 'Kec') !== false || stripos($kelurahan, 'Kab') !== false || stripos($kelurahan, 'Kota') !== false) {
                                if (preg_match('/\bkel(?:urahan|\.)?\s*([a-zA-Z\s]+?)(?=\s*(kec|kecamatan|kota|kab|prov|prop|$))/i', $alamat, $kelFixMatch)) {
                                    $kelurahan = ucwords(strtolower(trim($kelFixMatch[1]))); // 🎯 Ambil versi bersih
                                }
                            }
                        } else {
                            // ✅ Jika regex lama gagal, coba regex presisi
                            if (preg_match('/\bkel(?:urahan|\.)?\s*([a-zA-Z\s]+?)(?=\s*(kec|kecamatan|kota|kab|prov|prop|$))/i', $alamat, $kelFixMatch)) {
                                $kelurahan = ucwords(strtolower(trim($kelFixMatch[1])));
                            }
                        }

                        // ✅ Tambahan: Jika Kelurahan sama dengan Kecamatan, kosongkan Kelurahan
                        if (!empty($kelurahan) && strtolower($kelurahan) === strtolower($kecamatan)) {
                            $kelurahan = null;
                        }

                    }
                    // 🖊️ Log hasil
                    if (empty($allImages)) {
                        $this->warn("📸 Tidak ada gambar ditemukan untuk $detailUrl");
                    } else {
                        $this->info("📸 Total gambar ditemukan: " . count($allImages));
                        foreach ($allImages as $i => $imgUrl) {
                            $this->info("    [$i] $imgUrl");
                        }
                    }

                    // ✅ Jika kelurahan kosong tapi kecamatan ada, fallback ke kecamatan
                    $kelurahanFinal = $kelurahan ?: $kecamatan;


                    // 🗄️ INSERT KE DATABASE
                    DB::table('property')->insert([
                    'id_agent' => 'AG001',
                    'judul' => $details['judul'] ?? 'Property Rumah Lelang',
                    'deskripsi' => "Lelang properti dengan luas tanah {$details['luas_tanah']} m2 di {$kabupaten}, {$provinsi}. Sertifikat: " . ($buktiKepemilikan ?? 'Tidak tersedia') . ". Batas penawaran hingga {$details['batas_penawaran']}.",
                    'tipe' => $tipeProperti,
                    'harga' => $hargaInt,
                    'lokasi' => $details['alamat'] ?? 'Lokasi tidak diketahui',
                    'luas' => $details['luas_tanah'] ?? null,
                    'provinsi' => $provinsi ?? null,
                    'kota' => $kabupaten ?? null,
                    'kelurahan' => $kelurahanFinal,
                    'sertifikat' => $buktiKepemilikan ?? null,
                    'status' => 'Tersedia',
                    'gambar' => !empty($allImages) ? implode(',', $allImages) : null,
                    'payment' => 'cash',
                    'uang_jaminan' => $uangJaminanInt ?? null,
                    'batas_akhir_jaminan' => !empty($details['batas_setor_jaminan'])
                        ? $this->parseTanggalIndonesia($details['batas_setor_jaminan'])->format('Y-m-d')
                        : null,
                    'batas_akhir_penawaran' => !empty($details['batas_penawaran'])
                        ? $this->parseTanggalIndonesia($details['batas_penawaran'])->format('Y-m-d')
                        : null,
                    'tanggal_dibuat' => now(),
                    'tanggal_diupdate' => now(),
                ]);


                $this->info("✅ Data berhasil disimpan ke database (judul: {$details['judul']})");
                } catch (\Exception $e) {
                    $this->warn("⚠️ Gagal scrape detail untuk $detailUrl. Error: " . $e->getMessage());
                }


            }
        }

        $page++; // Next page
    }

    // ✅ Simpan semua data ke JSON
    file_put_contents('scraped-data.json', json_encode($allData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    $this->info("🎯 Total link unik ditemukan: " . count($allLinks));
    $this->info("📂 Semua data disimpan ke scraped-data.json");
    $this->info("✅ Selesai scrape semua halaman.");
}

}

