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

$pageLinks = $crawler->filter('a')->each(function ($linkNode) use ($baseUrl, $tipeProperti) {
    $href = $linkNode->attr('href');
    if (str_contains($href, '/detail-auction/')) {
        // ✅ Tambahan filter khusus untuk tipe lain-lain
        if ($tipeProperti === 'lain-lain') {
            $descNode = $linkNode->filter('p')->first();
            $desc = $descNode->count() ? strtolower($descNode->text()) : '';

            if (str_contains($desc, 'tanah') && str_contains($desc, 'luas')) {
                return rtrim(trim($baseUrl . $href), '/');
            } else {
                return null; // skip kalau bukan properti
            }
        }

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
        ->setOption('headless', true) // non-headless kalau mau debugging
        ->userAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/119 Safari/537.36')
        ->waitUntilNetworkIdle()
        ->waitForFunction('document.querySelector("div.scrollbar-hide") !== null', null, 20000)
        ->timeout(300);

    // 🕒 Scroll halus & delay panjang
    $browser->evaluate('
        (async () => {
            const scrollDiv = document.querySelector("div.scrollbar-hide");
            if (scrollDiv) {
                const totalWidth = scrollDiv.scrollWidth;
                const step = 200;
                for (let x = 0; x <= totalWidth; x += step) {
                    scrollDiv.scrollLeft = x;
                    await new Promise(r => setTimeout(r, 400));
                }
                scrollDiv.scrollLeft = totalWidth;
                await new Promise(r => setTimeout(r, 3000)); // tunggu extra 3 detik
            }
        })()
    ');

        // 🕒 Tunggu semua <img> punya src & jumlah stabil
        $browser->waitForFunction('
        (() => {
            return new Promise(resolve => {
                let stableTime = 0;
                let lastCount = 0;
                const check = setInterval(() => {
                    const imgs = Array.from(document.querySelectorAll("div.scrollbar-hide img"));
                    const validImgs = imgs.filter(img => img.src && img.src.startsWith("https"));
                    const count = validImgs.length;

                    // Jika sudah ada minimal 7 gambar, langsung resolve
                    if (count >= 7) {
                        clearInterval(check);
                        resolve(true);
                        return;
                    }

                    // Cek kestabilan jumlah gambar
                    if (count === lastCount) {
                        stableTime += 500;
                    } else {
                        stableTime = 0;
                        lastCount = count;
                    }

                    // Jika jumlah stabil selama 3 detik dan minimal 1 gambar, resolve
                    if (stableTime >= 3000 && count > 0) {
                        clearInterval(check);
                        resolve(true);
                    }
                }, 500);
            });
        })()
    ', null, 20000);


    // 🔥 Ambil semua gambar setelah scroll & stabil
    $imageJson = $browser->evaluate('
        JSON.stringify(
            Array.from(document.querySelectorAll("div.scrollbar-hide img"))
            .map(img => img.src)
            .filter(url => url && url.startsWith("https://file.lelang.go.id/lelang/photo_barang/"))
        )
    ');
    $imageUrls = json_decode($imageJson, true);
    $allImages = array_unique(array_filter($imageUrls));

    // 🔥 Kalau gagal ambil gambar → simpan screenshot untuk dicek
    if (empty($allImages)) {
        $this->warn("⚠️ Gagal ambil gambar: $detailUrl → Simpan screenshot.");
        $screenshotPath = storage_path('app/scrape-fail-'.time().'.png');
        $browser->save($screenshotPath);
    }

    // Batasi maksimal 6 gambar
    $allImages = array_slice($allImages, 0, 7);

                    $detailsJson = $browser->evaluate('
                    JSON.stringify({
                        // 🎯 Ambil judul properti
                        judul: (function(){
                            const el = document.querySelector("h3.mb-5.text-2xl.text-ternary-gray-200");
                            return el ? el.innerText.trim() : null;
                        })(),

                        // 💰 Harga +27.8%
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

                        // 🗺️ Alamat lengkap
                        alamat: (function(){
                            const div = Array.from(document.querySelectorAll("div.text-xs"))
                                .find(el => /Alamat/i.test(el.textContent));
                            return div ? div.innerText.replace(/\s+/g, " ").trim() : null;
                        })()
                    })
                    ');
                    $details = json_decode($detailsJson, true);

                    // ================== PARSE LUAS TANAH DARI JUDUL ==================
                    $luasDariJudul = null;
                    $judul = $details['judul'] ?? '';

                    if (!empty($judul)) {
                        // Tangkap pola angka + m2 (contoh: "105 m2" atau "74.8 m2")
                        if (preg_match('/\b(\d+(?:\.\d+)?)\s*m2\b/i', $judul, $m)) {
                            $angka = $m[1];

                            // Cek desimal → simpan sebagai integer tanpa pembulatan
                            if (strpos($angka, '.') !== false) {
                                $luasDariJudul = (int) floor((float)$angka);
                            } else {
                                $luasDariJudul = (int) $angka;
                            }
                        }
                    }

                    // Commit langsung ke details → biar property A punya luasnya sendiri
                    $details['luas_tanah_judul'] = $luasDariJudul;

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
                        if (preg_match('/\bkec(?:amatan|\.|\/kab)?\s*[:,]*\s*([a-zA-Z0-9\'\-\s]+)/i', $alamat, $kecMatch)) {
                            $kecamatanRaw = trim($kecMatch[1]);

                            if (preg_match('/^kota\s+/i', $kecamatanRaw)) {
                                // Kasus khusus: nama kecamatan dimulai dengan "Kota ..."
                                $kecamatanClean = preg_replace('/\s*\b(kab(?:\.|upaten)?|prov(?:\.|insi)?|prop(?:insi)?|kel(?:\.|urahan)?)\b.*$/i', '', $kecamatanRaw);
                            } else {
                                // Normal: stop di kota/kab/kel/prov pertama
                                $kecamatanClean = preg_replace('/\s*\b(kota|kab(?:\.|upaten)?|prov(?:\.|insi)?|prop(?:insi)?|kel(?:\.|urahan)?)\b.*$/i', '', $kecamatanRaw);
                            }

                            $kecamatan = ucwords(strtolower(trim($kecamatanClean)));
                        }

                        // 🎯 Cari Kelurahan atau Desa — logika identik dengan query PostgreSQL
                        if (preg_match('/(?:\s|^)(?:kel(?:urahan|\s*\.|\s*\/kab)?|desa)\s*[:,]*\s*([a-z0-9\'\-\s]+)/i', strtolower($alamat), $kelMatch)) {
                            $kelurahanRaw = trim($kelMatch[1]);

                            // 🔍 Jika setelah kelurahan langsung ada "kota ..." → ambil langsung, tanpa potong di "kota"
                            if (preg_match('/^kota\s+/i', $kelurahanRaw)) {
                                $kelurahanClean = preg_replace(
                                    '/\s*(kec(\.|amatan)?|kab(\.|upaten)?|prov(\.|insi)?|prop(insi)?|kel(\.|urahan)?|desa|rt(\.|[0-9])?|rw(\.|[0-9])?).*$/i',
                                    '',
                                    $kelurahanRaw
                                );
                            }
                            // 🧹 Selain itu, potong di semua kata pengganggu (kec/kota/kab/prov/kel/desa/rt/rw)
                            else {
                                $kelurahanClean = preg_replace(
                                    '/\s+(kec(\.|amatan)?|kota|kab(\.|upaten)?|prov(\.|insi)?|prop(insi)?|kel(\.|urahan)?|desa|rt(\.|[0-9])?|rw(\.|[0-9])?)\s*.*$/i',
                                    '',
                                    $kelurahanRaw
                                );
                            }

                            $kelurahan = ucwords(strtolower(trim($kelurahanClean)));
                        }

                        // ✅ Tambahan: Jika Kelurahan sama dengan Kecamatan, kosongkan Kelurahan
                        if (!empty($kelurahan) && strtolower($kelurahan) === strtolower($kecamatan)) {
                            $kelurahan = null;
                        }

                        // ================== PARSE KOTA/KAB DARI JUDUL (tanpa fallback alamat) ==================
                        $kabupaten = null;
                        $judul = $details['judul'] ?? '';

                        // 1) Prioritas: "Kota Adm. Jakarta {Utara/Selatan/Timur/Barat/Pusat}"
                        if (preg_match('/Kota\s+Adm(?:inistrasi)?\.?\s+Jakarta\s*[,.;-]?\s*(Utara|Selatan|Timur|Barat|Pusat)\b/i', $judul, $m)) {
                            $arah = ucwords(strtolower(trim($m[1])));
                            $kabupaten = 'Kota Adm. Jakarta ' . $arah;

                            // opsional: set provinsi jika belum ada
                            if (empty($provinsi)) { $provinsi = 'DKI JAKARTA'; }
                        }
                        // 2) Fallback generik dari JUDUL saja: "di Kota/Kota Adm./Kab/Kabupaten {Nama}"
                        elseif (preg_match('/\bdi\s+(Kota(?:\s+Adm(?:inistrasi)?\.?)?|Kab(?:\.|upaten)?)\s+([A-Za-z.\s]+?)(?=[,;.]|$)/i', $judul, $jm)) {
                            $label = strtolower($jm[1]);
                            // bersihkan {Nama} dari kata lanjutan (Prov/Kec/Kab/Kota)
                            $nama  = preg_replace('/\s+\b(Prov(?:insi)?|Prop(?:insi)?|Kec(?:amatan)?|Kab(?:\.|upaten)?|Kota)\b.*$/i', '', trim($jm[2]));
                            $nama  = ucwords(strtolower($nama));

                            if (strpos($label, 'kota') === 0) {
                                $kabupaten = (stripos($label, 'adm') !== false)
                                    ? 'Kota Adm. ' . $nama
                                    : 'Kota '      . $nama;
                            } else {
                                $kabupaten = 'Kab. ' . $nama;
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

                    // 🏷️ Mapping emoji per tipe
                    $emojiMap = [
                        'rumah'          => '🏡',
                        'apartemen'      => '🏢',
                        'gudang'         => '📦',
                        'pabrik'         => '🏭',
                        'toko'           => '🏬',
                        'tanah'          => '🌱',
                        'hotel dan villa'=> '🏨',
                        'ruko'           => '🏢',
                        'lain-lain'      => '✨',
                    ];

                    $tipe = strtolower($tipeProperti);
                    $emoji = $emojiMap[$tipe] ?? '✨';

                    // 📝 Buat deskripsi pendek & eye-catching (pakai LT biar jelas bukan harga)
                    if ($tipe === 'hotel dan villa' || $tipe === 'lain-lain') {
                        $deskripsi = "{$emoji} Lelang Properti Murah – LT {$luasDariJudul} m² – {$kabupaten}";
                    } else {
                        $ucTipe = ucwords($tipe);
                        $deskripsi = "{$emoji} Lelang {$ucTipe} Murah – LT {$luasDariJudul} m² – {$kabupaten}";
                    }

                    // 🗄️ INSERT KE DATABASE
                    DB::table('property')->insert([
                    'id_agent' => 'AG001',
                    'vendor' => $details['penjual'] ?? null, // 🆕 isi vendor dari penjual
                    'judul' => $details['judul'] ?? 'Property Rumah Lelang',
                    'deskripsi' => $deskripsi,
                    'tipe' => $tipeProperti,
                    'harga' => $hargaInt,
                    'lokasi' => substr($details['alamat'] ?? 'Lokasi tidak diketahui', 0, 500),
                    'luas' => $details['luas_tanah_judul'], // 🔥 lebih konsisten
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

