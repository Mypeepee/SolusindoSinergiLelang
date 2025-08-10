const { create } = require('@open-wa/wa-automate');
const axios = require('axios');

// Jalankan client
create().then(start).catch(console.error);

function start(client) {
  client.onMessage(async (message) => {
    try {
      // Normalisasi teks
      const text = (message.body || '')
        .toLowerCase()
        .replace(/\s+/g, ' ')
        .trim();

      // Hanya tanggapi perintah "update stok <id>"
      if (!text.startsWith('update')) return;

      const match = text.match(/update\W*stok\W*(\d+)/i);
      if (!match) {
        await client.sendText(
          message.from,
          'Format salah. Contoh: update stok 61151'
        );
        return;
      }

      const id = match[1];
      console.log('ID diterima:', id);

      // Panggil API Laravel kamu
      try {
        const res = await axios.get(
          `http://localhost:8000/api/property/${id}`,
          { timeout: 10000 }
        );
        const p = res.data || {};

        const reply = `${p.id_listing}: ${p.lokasi}
Tanggal Lelang: ${p.batas_akhir_penawaran} jam 09:00 WIB
Tunggu Update Stok Please`;

        await client.sendText(message.from, reply);
      } catch (e) {
        console.error('API error:', e.message);
        await client.sendText(message.from, `Data untuk ID ${id} tidak ditemukan.`);
      }
    } catch (err) {
      console.error('Handler error:', err);
    }
  });
}
