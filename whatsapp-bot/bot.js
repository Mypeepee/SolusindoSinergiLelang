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

        await client.sendText(message.from, reply);
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
