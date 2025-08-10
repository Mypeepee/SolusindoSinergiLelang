const text = message.body?.toLowerCase()?.replace(/\s+/g, ' ').trim() || '';

if (text.startsWith("update stok")) {
  const match = text.match(/update\W*stok\W*(\d+)/i);
  if (!match) {
    await client.sendText(message.from, 'Format salah. Contoh: update stok 61151');
    return;
  }

  const id = match[1];

  try {
    const res = await axios.get(`http://localhost:8000/api/property/${id}`);
    const data = res.data;

    const reply =
    `${data.id_listing}: ${data.lokasi}
    Tanggal Lelang: ${data.batas_akhir_penawaran}
    Tunggu Update Stok Please`;

    await client.sendText(message.from, reply);
  } catch (err) {
    console.error(err.message);
    await client.sendText(message.from, `Data untuk ID ${id} tidak ditemukan.`);
  }
}
