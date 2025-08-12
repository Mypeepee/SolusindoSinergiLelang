// bot.js (CommonJS, TANPA top-level await)
const { create } = require('@open-wa/wa-automate');
const axios = require('axios');

const API_BASE = 'http://127.0.0.1:8000'; // ganti kalau perlu

create().then(start).catch(console.error);

function start(client) {
  client.onMessage(async (message) => {
    try {
      const text = (message.body || '').toLowerCase().replace(/\s+/g, ' ').trim();
      if (!text.startsWith('update')) return;

      const m = text.match(/update\W*stok\W*(\d+)/i);
      if (!m) {
        await client.sendText(message.from, 'Format salah. Contoh: update stok 61151');
        return;
      }

      const id = m[1];
      console.log('ID diterima:', id);

      try {
        const res = await axios.get(`${API_BASE}/api/property/${id}`, {
          timeout: 10000,
          validateStatus: (s) => s >= 200 && s < 300,
        });

        const p = res.data;
        if (!p || typeof p !== 'object' || !('id_listing' in p)) {
          console.error('Unexpected response:', p);
          await client.sendText(message.from, `Gagal membaca data ID ${id}.`);
          return;
        }

        const reply = `${p.id_listing}: ${p.lokasi}
Tanggal Lelang: ${p.batas_akhir_penawaran}
Vendor : ${p.vendor}`;

        // === kirim teks (ALUR ASLI) ===
        await client.sendText(message.from, reply);

        // === TAMBAHAN: kirim 1 foto pertama sebagai caption yang sama ===
        try {
          const firstImageUrl = pickFirstImageUrl(p.gambar);
          if (firstImageUrl) {
            const { data, headers } = await axios.get(firstImageUrl, {
              responseType: 'arraybuffer',
              timeout: 20000, // sebagian server gambar agak lambat
              maxContentLength: 20 * 1024 * 1024,
              maxBodyLength: 20 * 1024 * 1024,
              headers: {
                'User-Agent':
                  'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120 Safari/537.36',
                'Accept': 'image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
              },
            });

            let mime = (headers['content-type'] || '').split(';')[0].toLowerCase();
            if (!mime || !/^image\//.test(mime)) {
              const ext = (firstImageUrl.split('.').pop() || '').toLowerCase();
              mime =
                ext === 'png' ? 'image/png' :
                ext === 'webp' ? 'image/webp' :
                'image/jpeg';
            }

            const b64 = Buffer.from(data).toString('base64');
            const dataUrl = `data:${mime};base64,${b64}`;

            await client.sendImage(message.from, dataUrl, 'foto.jpg', reply);
          }
        } catch (imgErr) {
          console.error('Download/kirim gambar gagal:', imgErr.code || imgErr.message || imgErr);
          // biarkan, minimal teks sudah terkirim
        }

      } catch (e) {
        console.error('API error:', e.response?.status || e.code || e.message, e.response?.data || '');
        if (e.response?.status === 404) {
          await client.sendText(message.from, `Maaf, properti dengan ID ${id} tidak ditemukan.`);
        } else if (e.code === 'ECONNREFUSED') {
          await client.sendText(message.from, `Tidak bisa konek ke API (${API_BASE}). Pastikan server Laravel jalan.`);
        } else {
          await client.sendText(message.from, `Gagal ambil data ID ${id}. Coba lagi nanti.`);
        }
      }
    } catch (err) {
      console.error('Handler error:', err);
    }
  });
}

/**
 * Ambil URL pertama dari kolom "gambar".
 * - Bisa berformat: "url1,url2, url3 ..."
 * - Bisa dipisah spasi/newline
 * - Kalau bentuk JSON array string, juga dicoba parse.
 */
function pickFirstImageUrl(gambarField) {
  if (!gambarField) return null;

  // Jika JSON array
  if (typeof gambarField === 'string' && gambarField.trim().startsWith('[')) {
    try {
      const arr = JSON.parse(gambarField);
      if (Array.isArray(arr)) {
        const first = arr.map(String).map(s => s.trim()).find(isValidUrl);
        if (first) return first;
      }
    } catch (_) {}
  }

  // Split dengan koma/spasi/newline
  const candidates = String(gambarField)
    .split(/[,\n\r\t ]+/)
    .map(s => s.trim())
    .filter(Boolean);

  const first = candidates.find(isValidUrl);
  return first || null;
}

function isValidUrl(u) {
  try { new URL(u); return true; } catch { return false; }
}
