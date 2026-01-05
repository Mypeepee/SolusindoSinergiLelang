{{-- resources/views/owner/analitik/partials/agent/_transactions.blade.php --}}
@php
  $rupiah = fn($n) => 'Rp ' . number_format((float)$n, 0, ',', '.');
@endphp

<div class="table-responsive">
  <table class="table table-sm align-middle">
    <thead class="table-light">
      <tr class="small text-muted">
        <th>Tanggal</th>
        <th>ID Trx</th>
        <th>ID Listing</th>
        <th>Status</th>
        <th>Skema</th>
        <th class="text-end">Harga Deal</th>
        <th class="text-end">Basis Pendapatan</th>
        <th class="text-end">Kenaikan</th>
        <th class="text-end">Cobroke</th>
        <th class="text-end">Royalty</th>
      </tr>
    </thead>
    <tbody>
    @forelse($rows as $r)
      <tr class="small">
        <td>{{ \Carbon\Carbon::parse($r->tanggal_transaksi)->format('Y-m-d') }}</td>
        <td class="fw-semibold">{{ $r->id_transaction }}</td>
        <td>{{ $r->id_listing }}</td>
        <td><span class="badge bg-light text-dark border">{{ $r->status_transaksi }}</span></td>
        <td>{{ $r->skema_komisi }}</td>
        <td class="text-end">{{ $rupiah($r->harga_deal) }}</td>
        <td class="text-end">{{ $rupiah($r->basis_pendapatan) }}</td>
        <td class="text-end">{{ number_format((float)($r->kenaikan_dari_limit ?? 0), 1, ',', '.') }}%</td>
        <td class="text-end">{{ $rupiah($r->cobroke_fee ?? 0) }}</td>
        <td class="text-end">{{ $rupiah($r->royalty_fee ?? 0) }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="10" class="text-center text-muted small">Tidak ada transaksi pada periode ini.</td>
      </tr>
    @endforelse
    </tbody>
  </table>
</div>

<div class="small text-muted mt-2">
  Menampilkan 50 terbaru dalam range: {{ $start->toDateString() }} — {{ $end->toDateString() }}
</div>
