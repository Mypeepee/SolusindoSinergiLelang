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
    // Pastikan sequence id_listing sesuai
    DB::statement("
        SELECT setval(
            pg_get_serial_sequence('property', 'id_listing'),
            COALESCE(MAX(id_listing), 1),
            true
        ) FROM property
    ");

    $baseUrl = 'https://lelang.go.id';
    $kategori = $this->argument('kategori') ?? 'Rumah';
    $page = 1;
    $allLinks = [];

    $this->info("📄 Mulai scrape semua halaman kategori: $kategori");

    $tipeProperti = strtolower($kategori);

    // Ambil semua link existing sekali di awal
    $existingLinks = DB::table('property')->pluck('link')->toArray();
    $existingLinks = array_map('trim', $existingLinks);

    while (true) {
        $listUrl = "$baseUrl/lot-lelang/katalog-lot-lelang?kategori=" . urlencode($kategori) . "&page=$page";
        $this->info("🌐 Scraping halaman ke-$page: $listUrl");

        try {
            $html = \Spatie\Browsershot\Browsershot::url($listUrl)
                ->waitUntilNetworkIdle()
                ->waitForFunction('document.querySelectorAll("a[href*=\"/detail-auction/\"]").length > 0')
                ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/119 Safari/537.36')
                ->setOption('headless', true)
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
                return rtrim(trim($baseUrl . $href), '/');
            }
            return null;
        });

        $pageLinks = array_unique(array_filter($pageLinks));

        if (empty($pageLinks)) {
            $this->warn("❌ Tidak ada link detail property di halaman ke-$page.");
            break;
        }

        // Hanya ambil link yang belum ada di DB
        $pageLinks = array_diff($pageLinks, $existingLinks);

        if (empty($pageLinks)) {
            $this->info("⏭️ Semua link di halaman $page sudah ada di database. Skip.");
            $page++;
            continue;
        }

        $this->info("🔗 Ditemukan " . count($pageLinks) . " link baru di halaman $page.");
        foreach ($pageLinks as $detailUrl) {
            if (!in_array($detailUrl, $allLinks)) {
                $allLinks[] = $detailUrl;
                $this->info("➡️ $detailUrl");

                // 🔥 MASUK KE DETAIL & SCRAPE GAMBAR
                try {
                    $browser = \Spatie\Browsershot\Browsershot::url($detailUrl)
                        ->setOption('headless', true) // 🔥 Non-headless biar lihat proses
                        ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/119 Safari/537.36')
                        ->waitUntilNetworkIdle()
                        ->waitForFunction('document.querySelector("div.scrollbar-hide") !== null', null, 18000)
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
                            if (stableTime >= 2500) { // ✅ stabil selama 2 detik
                                clearInterval(interval);
                                resolve(true);
                            }
                        }, checkInterval);
                    });
                })()
            ', null, 18000); // Timeout maksimum 15 detik


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

                    // 🔥 Batasi maksimal 7 gambar
                    $allImages = array_slice($allImages, 0, 6);


                    $detailsJson = $browser->evaluate('
                    JSON.stringify({
                        // 🎯 Ambil judul properti
                        judul: (function(){
                            const el = document.querySelector("h3.mb-5.text-2xl.text-ternary-gray-200");
                            return el ? el.innerText.trim() : null;
                        })(),

                        // 💰 Harga +26.8%
                        harga: (function(){
                            const h = document.querySelectorAll("h6.text-primary-500")[0];
                            if (h) {
                                const hargaAsli = parseInt(h.innerText.replace(/[^\d]/g, ""));
                                const hargaMarkup = Math.round(hargaAsli * 1.278); // 🔥 Tambah 27.8%
                                return hargaMarkup;
                            }
                            return null;
                        })(),

                        // 💵 Uang Jaminan +20%
                        uang_jaminan: (function(){
                            const u = document.querySelectorAll("h6.text-primary-500")[1];
                            if (u) {
                                const jaminanAsli = parseInt(u.innerText.replace(/[^\d]/g, ""));
                                const jaminanMarkup = Math.round(jaminanAsli * 1.2); // 🔥 Tambah 20%
                                return jaminanMarkup;
                            }
                            return null;
                        })(),

                        // 👤 Nama penjual
                        penjual: (function(){
                            const el = document.querySelectorAll("h6.text-ternary-gray-200")[0];
                            return el ? el.innerText.trim() : null;
                        })(),

                        // ⏳ Batas Penawaran
                        batas_penawaran: (function(){
                            const el = document.querySelectorAll("h6.text-ternary-gray-200")[1];
                            return el ? el.innerText.trim() : null;
                        })(),

                        // ⏳ Batas Setor Jaminan
                        batas_setor_jaminan: (function(){
                            const el = document.querySelectorAll("h6.text-ternary-gray-200")[4];
                            return el ? el.innerText.trim() : null;
                        })(),

                        bukti_kepemilikan: (function(){
                            // Cari div dengan label "Bukti Kepemilikan"
                            const labelDiv = Array.from(document.querySelectorAll("div.font-bold"))
                                .find(el => el.innerText.trim().toLowerCase() === "bukti kepemilikan");

                            if (labelDiv) {
                                // Ambil semua div.text-xs sesudahnya
                                const siblingDivs = [];
                                let next = labelDiv.nextElementSibling;
                                while (next && next.classList.contains("text-xs")) {
                                    siblingDivs.push(next.innerText.trim());
                                    next = next.nextElementSibling;
                                }
                                return siblingDivs.length ? siblingDivs.join(" | ") : null; // Gabungkan isi
                            }
                            return null;
                        })(),

                        // 📏 Luas tanah (integer)
                        luas_tanah: (function(){
                            const div = Array.from(document.querySelectorAll("div.text-xs"))
                                .find(el => /Luas/i.test(el.textContent));
                            if (div) {
                                const match = div.innerText.match(/Luas:\s*(\d+)/i);
                                return match ? parseInt(match[1]) : null;
                            }
                            return null;
                        })(),

                        // 🗺️ Alamat lengkap
                        alamat: (function(){
                            const div = Array.from(document.querySelectorAll("div.text-xs"))
                                .find(el => /Alamat/i.test(el.textContent));
                            return div ? div.innerText.replace(/\s+/g, " ").trim() : null;
                        })()
                    })
                    ');
                    $details = json_decode($detailsJson, true);



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
                    $kecamatan = null;
                    $kelurahan = null;

                    if (!empty($details['alamat'])) {
                        $alamat = $details['alamat'];

                        // 🎯 CARI PROVINSI (support typo Prop., Prov., dll + hapus titik)
                        if (preg_match('/\b(prov(?:insi)?|prop(?:insi)?)\.?\s*([a-zA-Z\s]+)/i', $alamat, $provMatch)) {
                            $provinsi = strtoupper(trim(preg_replace('/\.$/', '', $provMatch[2]))); // ✅ Hapus titik di belakang
                        }

                        // 🎯 CARI PROVINSI (support Prov, Prop, titik, dll)
                        if (preg_match('/\b(prov(?:insi)?|prop(?:insi)?)\.?\s*([a-zA-Z\s]+)/i', $alamat, $provMatch)) {
                            $provinsi = strtoupper(trim($provMatch[2])); // ✅ Provinsi bersih
                        }

                        // 🎯 Cari Kecamatan
                        if (preg_match('/\bkec(?:amatan|\.)?\s*[:,]*\s*([a-zA-Z\s]+)/i', $alamat, $kecMatch)) {
                            $kecamatanRaw = trim($kecMatch[1]);

                            // 🎯 Ambil hanya sampai sebelum kata kunci (Kab/Kota/Prov)
                            $kecamatanClean = preg_replace('/\s*\b(kab(?:upaten)?|kota|prov(?:insi)?|prop(?:insi)?)\b.*$/i', '', $kecamatanRaw);
                            $kecamatan = ucwords(strtolower(trim($kecamatanClean))); // 🔥 Capitalize
                        }

                        // 🎯 Cari Kelurahan
                        if (preg_match('/\bkel(?:urahan|\.)?\s*([a-zA-Z\s]+)/i', $alamat, $kelMatch)) {
                            $kelurahanRaw = trim($kelMatch[1]);

                            // 🎯 Ambil hanya sampai sebelum kata kunci (Kec/Kab/Kota/Prov)
                            $kelurahanClean = preg_replace('/\s*\b(kec(?:amatan)?|kab(?:upaten)?|kota|prov(?:insi)?|prop(?:insi)?)\b.*$/i', '', $kelurahanRaw);
                            $kelurahan = ucwords(strtolower(trim($kelurahanClean))); // 🔥 Capitalize

                            // ✅ Tambahan pembersih: jika masih ada “Kec/Kab/Kota” di Kelurahan
                            if (stripos($kelurahan, 'Kec') !== false || stripos($kelurahan, 'Kab') !== false || stripos($kelurahan, 'Kota') !== false) {
                                if (preg_match('/\bkel(?:urahan|\.)?\s*([a-zA-Z\s]+?)(?=\s*(kec|kab|kota|prov|prop|$))/i', $alamat, $kelFixMatch)) {
                                    $kelurahan = ucwords(strtolower(trim($kelFixMatch[1]))); // 🎯 Ambil versi bersih
                                }
                            }
                        } else {
                            // ✅ Jika regex lama gagal, coba regex presisi
                            if (preg_match('/\bkel(?:urahan|\.)?\s*([a-zA-Z\s]+?)(?=\s*(kec|kab|kota|prov|prop|$))/i', $alamat, $kelFixMatch)) {
                                $kelurahan = ucwords(strtolower(trim($kelFixMatch[1])));
                            }
                        }

                        // ✅ Tambahan: Jika Kelurahan sama dengan Kecamatan, kosongkan Kelurahan
                        if (!empty($kelurahan) && strtolower($kelurahan) === strtolower($kecamatan)) {
                            $kelurahan = null;
                        }

                        $kabupaten = null;
                        if (!empty($details['judul'])) {
                            if (preg_match('/di\s+(Kota|Kab\.?|Kabupaten)\s+([a-zA-Z\s]+)/i', $details['judul'], $judulMatch)) {
                                $namaKab = strtoupper(trim($judulMatch[2]));
                                if (stripos($judulMatch[1], 'kota') !== false) {
                                    $kabupaten = "KOTA " . $namaKab;
                                } else {
                                    $kabupaten = "KAB. " . $namaKab;
                                }
                            }
                        }

                        // 🧹 Bersihkan alamat
                        if (!empty($details['alamat'])) {
                            $alamatClean = preg_replace('/^Alamat:\s*/i', '', $details['alamat']);
                            $details['alamat'] = trim($alamatClean);
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
                    'vendor' => $details['penjual'] ?? null, // 🆕 isi vendor dari penjual
                    'judul' => $details['judul'] ?? 'Property Rumah Lelang',
                    'deskripsi' => "Lelang properti dengan luas tanah {$details['luas_tanah']} m2 di {$kabupaten}, {$provinsi}. Sertifikat: " . ($buktiKepemilikan ?? 'Tidak tersedia') . ". Batas penawaran hingga {$details['batas_penawaran']}.",
                    'tipe' => $tipeProperti,
                    'harga' => $hargaInt,
                    'lokasi' => $details['alamat'] ?? 'Lokasi tidak diketahui',
                    'luas' => $details['luas_tanah'] ?? null,
                    'provinsi' => $provinsi ?? null,
                    'kota' => $kabupaten ?? null,
                    'kecamatan' => $kecamatan ?? null,
                    'kelurahan' => $kelurahanFinal,
                    'sertifikat' => $details['bukti_kepemilikan'] ?? null,
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
                    'link' => $detailUrl,
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
    $this->info("🎯 Total link unik ditemukan: " . count($allLinks));
    $this->info("📂 Semua data disimpan ke scraped-data.json");
    $this->info("✅ Selesai scrape semua halaman.");
}

}

