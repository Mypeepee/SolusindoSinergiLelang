// bot.js (CommonJS)
'use strict';

const { create } = require('@open-wa/wa-automate');
const axios = require('axios');

const API_BASE = 'http://127.0.0.1:8000'; // sesuaikan jika perlu

// create().then(start).catch(console.error);
create({
  sessionId: 'solusindo-bot',
  headless: true,        // tetap nggak buka jendela Chrome
  useChrome: true,       // pakai Chrome lokal (bukan Chromium bawaan puppeteer)
  multiDevice: true,
  authTimeout: 0,        // jangan auto-mati auth
  qrTimeout: 0,          // QR nggak bikin proses mati
  disableSpins: true,
  logConsole: true,
  browserArgs: [
    '--no-sandbox',
    '--disable-setuid-sandbox',
    '--disable-dev-shm-usage',
    '--disable-extensions',
    '--disable-gpu'
  ],
}).then(start).catch(console.error);

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

        // --- ambil nama agent (lihat helper di bawah) ---
        const agentName = await getAgentName(p).catch(() => '-');

        const caption = `${p.id_listing}: ${p.lokasi}
Tanggal Lelang: ${p.batas_akhir_penawaran ?? '-'}
Vendor : ${p.vendor ?? '-'}
Agent : ${agentName}`;

        // ——— KIRIM 1 FOTO SAJA ———
        const firstImageUrl = pickFirstImageUrl(p.gambar_first || p.gambar || null);

        if (firstImageUrl) {
          try {
            const { data, headers } = await axios.get(firstImageUrl, {
              responseType: 'arraybuffer',
              timeout: 20000,
              maxContentLength: 20 * 1024 * 1024,
              maxBodyLength: 20 * 1024 * 1024,
              headers: {
                'User-Agent':
                  'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120 Safari/537.36',
                Accept: 'image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
              },
              validateStatus: (s) => s >= 200 && s < 400, // izinkan 3xx (redirect)
            });

            let mime = (headers['content-type'] || '').split(';')[0].toLowerCase();
            if (!mime || !/^image\//.test(mime)) {
              const ext = (firstImageUrl.split('.').pop() || '').toLowerCase();
              mime = ext === 'png' ? 'image/png' : ext === 'webp' ? 'image/webp' : 'image/jpeg';
            }

            const b64 = Buffer.from(data).toString('base64');
            const dataUrl = `data:${mime};base64,${b64}`;

            await client.sendImage(message.from, dataUrl, 'foto.jpg', caption);
          } catch (imgErr) {
            console.error('Download/kirim gambar gagal:', imgErr.code || imgErr.message || imgErr);
            await client.sendText(message.from, caption);
          }
        } else {
          await client.sendText(message.from, caption);
        }
      } catch (e) {
        console.error('API error:', e.response?.status || e.code || e.message, e.response?.data || '');
        if (e.response?.status === 404) {
          await client.sendText(message.from, `Maaf, properti dengan ID ${id} tidak ditemukan.`);
        } else if (e.code === 'ECONNREFUSED') {
          await client.sendText(message.from, `Tidak bisa konek ke API (${API_BASE}). Pastikan server Laravel jalan.`);
        } else if (e.code === 'ETIMEDOUT' || e.message?.includes('timeout')) {
          await client.sendText(message.from, `Permintaan ke API timeout. Coba lagi sebentar.`);
        } else {
          await client.sendText(message.from, `Gagal ambil data ID ${id}. Coba lagi nanti.`);
        }
      }
    } catch (err) {
      console.error('Handler error:', err);
    }
  });
}

/** ========================= Helpers ========================= **/

/**
 * Cari nama agent dari respons property, atau fallback panggil endpoint agent.
 * Prioritas:
 * 1) p.agent_nama / p.nama_agent / p.agent?.nama
 * 2) GET /api/agent/{id_agent} atau /api/agents/{id_agent}
 * 3) '-'
 */
async function getAgentName(p) {
  const direct =
    p.agent_nama ??
    p.nama_agent ??
    (p.agent && (p.agent.nama || p.agent.name)) ??
    null;

  if (direct && String(direct).trim()) return String(direct).trim();

  const idAgent = p.id_agent || p.agent_id || null;
  if (!idAgent) return '-';

  const candidates = [
    `${API_BASE}/api/agent/${idAgent}`,
    `${API_BASE}/api/agents/${idAgent}`,
  ];

  for (const url of candidates) {
    try {
      const r = await axios.get(url, {
        timeout: 8000,
        validateStatus: (s) => s >= 200 && s < 300,
      });
      const d = r.data || {};
      const name =
        d.nama ??
        d.name ??
        d.agent?.nama ??
        d.data?.nama ??
        d.data?.name ??
        null;
      if (name && String(name).trim()) return String(name).trim();
    } catch {
      // lanjut ke kandidat berikutnya
    }
  }
  return '-';
}

/**
 * Ambil URL gambar pertama yang valid dari berbagai format.
 */
function pickFirstImageUrl(gambarField) {
  if (!gambarField) return null;

  if (typeof gambarField === 'string' && isValidUrl(cleanCandidate(gambarField))) {
    return cleanCandidate(gambarField);
  }

  if (Array.isArray(gambarField)) {
    const first = gambarField.map(String).map(cleanCandidate).find(isValidUrl);
    if (first) return first;
  }

  const asString = String(gambarField).trim();

  if (asString.startsWith('[')) {
    try {
      const arr = JSON.parse(asString);
      if (Array.isArray(arr)) {
        const first = arr.map(String).map(cleanCandidate).find(isValidUrl);
        if (first) return first;
      }
    } catch (_) {}
  }

  const candidates = asString.split(/[,\n\r\t ;|]+/).map(cleanCandidate).filter(Boolean);
  const first = candidates.find(isValidUrl);
  return first || null;
}

function cleanCandidate(s) {
  if (!s) return '';
  return s.trim().replace(/^['"(]+|[)'",.]+$/g, '');
}

function isValidUrl(u) {
  try {
    const url = new URL(u);
    return url.protocol === 'http:' || url.protocol === 'https:';
  } catch {
    return false;
  }
}
