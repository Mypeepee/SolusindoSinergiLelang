<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Spatie\Browsershot\Browsershot;
use App\Models\Property;

class ScrapeProperty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrape-property';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape semua URL detail property Rumah dari lelang.go.id';

    /**
     * Execute the console command.
     */
    public function handle()
{
    $baseUrl = 'https://lelang.go.id';
    $kategori = 'Rumah';
    $page = 1;
    $allLinks = [];
    $allData = []; // 🆕 untuk menyimpan link + gambar

    $this->info("📄 Mulai scrape semua halaman kategori: $kategori");

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

                    // 🔥 Ambil data tambahan (judul, harga, dll)
                    $detailsJson = $browser->evaluate('
                    JSON.stringify({
                        judul: document.querySelector("h3.mb-5.text-2xl.text-ternary-gray-200")?.innerText || null,
                        harga: (function(){
                            const h = document.querySelectorAll("h6.text-primary-500")[0]?.innerText || null;
                            return h ? parseInt(h.replace(/[^\d]/g, "")) : null; // 💰 Rp jadi integer
                        })(),
                        uang_jaminan: (function(){
                            const u = document.querySelectorAll("h6.text-primary-500")[1]?.innerText || null;
                            return u ? parseInt(u.replace(/[^\d]/g, "")) : null; // 💵 Rp jadi integer
                        })(),
                        penjual: document.querySelectorAll("h6.text-ternary-gray-200")[0]?.innerText || null,
                        batas_penawaran: document.querySelectorAll("h6.text-ternary-gray-200")[1]?.innerText || null,
                        penyelenggara: document.querySelectorAll("h6.text-ternary-gray-200")[3]?.innerText || null,
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
                                    tanggal_kepemilikan: textXs[1]?.innerText.trim() || null
                                };
                            }
                            return { bukti_kepemilikan: null, tanggal_kepemilikan: null };
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
                ');$details = json_decode($detailsJson, true);

                    $buktiKepemilikan = $details['bukti_kepemilikan_data']['bukti_kepemilikan'] ?? null;
                    $tanggalKepemilikan = $details['bukti_kepemilikan_data']['tanggal_kepemilikan'] ?? null;

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

                        // 🎯 Cari Kecamatan
                        if (preg_match('/\bkec(?:amatan|\.)?\s*([a-zA-Z\s]+)/i', $alamat, $kecMatch)) {
                            $kecamatanRaw = trim($kecMatch[1]);
                            // 🧠 Hapus kata tambahan "Kota ..." / "Kabupaten ..."
                            $kecamatanClean = preg_replace('/\b(kota|kabupaten)\b.*$/i', '', $kecamatanRaw);
                            $kecamatan = ucwords(strtolower(trim($kecamatanClean))); // 🔥 Capitalize
                        }

                        // 🎯 Cari Kelurahan
                        if (preg_match('/\bkel(?:urahan|\.)?\s*([a-zA-Z\s]+)/i', $alamat, $kelMatch)) {
                            $kelurahanRaw = trim($kelMatch[1]);
                            // 🧠 Hapus kata tambahan "Kecamatan ..." / "Kota ..." / "Kabupaten ..."
                            $kelurahanClean = preg_replace('/\b(kecamatan|kota|kabupaten)\b.*$/i', '', $kelurahanRaw);
                            $kelurahan = ucwords(strtolower(trim($kelurahanClean))); // 🔥 Capitalize
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

                    $this->info("📄 Judul: " . ($details['judul'] ?? 'Tidak ditemukan'));
                    $this->info("💰 Harga: " . ($hargaInt ?? 'Tidak ditemukan'));
                    $this->info("💵 Uang Jaminan: " . ($uangJaminanInt ?? 'Tidak ditemukan'));
                    $this->info("🏦 Penjual: " . ($details['penjual'] ?? 'Tidak ditemukan'));
                    $this->info("📆 Batas Penawaran: " . ($details['batas_penawaran'] ?? 'Tidak ditemukan'));
                    $this->info("🏢 Penyelenggara: " . ($details['penyelenggara'] ?? 'Tidak ditemukan'));
                    $this->info("💳 Batas Setor Jaminan: " . ($details['batas_setor_jaminan'] ?? 'Tidak ditemukan'));
                    $this->info("📜 Bukti Kepemilikan: " . ($buktiKepemilikan ?? 'Tidak ditemukan'));
                    $this->info("📅 Tanggal Kepemilikan: " . ($tanggalKepemilikan ?? 'Tidak ditemukan'));
                    $this->info("📏 Luas Tanah (m2): " . ($details['luas_tanah'] ?? 'Tidak ditemukan'));
                    $this->info("📍 Alamat: " . ($details['alamat'] ?? 'Tidak ditemukan'));
                    $this->info("🌎 Provinsi: " . ($provinsi ?? 'Tidak ditemukan'));
                    $this->info("🏛️ Kabupaten: " . ($kabupaten ?? 'Tidak ditemukan'));
                    $this->info("🏘️ Kecamatan: " . ($kecamatan ?? 'Tidak ditemukan'));
                    $this->info("🏘️ Kelurahan: " . ($kelurahan ?? 'Tidak ditemukan'));


                    // 🗂️ Tambahkan ke data
                    // $allData[] = [
                    //     'url' => $detailUrl,
                    //     'images' => $allImages,
                    //     'judul' => $details['judul'],
                    //     'harga' => $details['harga'],
                    //     'uang_jaminan' => $details['uang_jaminan'],
                    //     'penjual' => $details['penjual'],
                    //     'batas_penawaran' => $details['batas_penawaran'],
                    //     'penyelenggara' => $details['penyelenggara'],
                    //     'batas_setor_jaminan' => $details['batas_setor_jaminan'],
                    //     'bukti_kepemilikan' => $buktiKepemilikan,
                    //     'tanggal_kepemilikan' => $tanggalKepemilikan,
                    //     'luas_tanah' => $details['luas_tanah'],
                    //     'alamat' => $details['alamat'],
                    //     'provinsi' => $provinsi,
                    //     'kabupaten' => $kabupaten,
                    //     'kecamatan' => $kecamatan,
                    //     'kelurahan' => $kelurahan,
                    // ];
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

