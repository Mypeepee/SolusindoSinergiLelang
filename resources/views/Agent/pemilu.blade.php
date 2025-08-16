@include('template.header')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow-lg z-3" role="alert" style="min-width: 300px;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-success alert-danger fade show position-fixed top-0 end-0 m-3 shadow-lg z-3" role="alert" style="min-width: 300px;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- <div class="container-fluid px-4 my-0">
<div class="row mb-2">
    <div class="col-auto">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
</div> --}}

<div class="container-fluid px-4 my-4">
    <div class="row">
        <!-- LEFT: List Property -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded mb-4">
                <!-- Header -->
                <div class="card-header text-white fw-bold" style="background-color: #f15b2a;">
                    <i class="bi bi-house-door-fill me-2"></i> Daftar Property
                </div>

                <div class="card-body">
                    <!-- Search -->
                    <form method="GET" action="{{ route('pemilu.index') }}" class="mb-3 d-flex gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            class="form-control" placeholder="Cari ID Listing">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Listing</th>
                                    <th>Lokasi</th>
                                    <th>Luas (m²)</th>
                                    <th>Harga</th>
                                    <th>Gambar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($properties as $property)
                                    <tr>
                                        <td>{{ $property->id_listing }}</td>
                                        <td>{{ $property->lokasi }}</td>
                                        <td>{{ $property->luas ?? '-' }}</td>
                                        <td>Rp {{ number_format($property->harga, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $fotoArray = explode(',', $property->gambar);
                                                $fotoUtama = $fotoArray[0] ?? 'default.jpg';
                                            @endphp
                                            <img src="{{ $fotoUtama }}"
                                                alt="Foto Properti"
                                                class="img-thumbnail" style="max-width: 80px; max-height: 80px;">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="col-12">
                        <div class="pagination d-flex justify-content-center mt-4 gap-1 overflow-auto">
                            {{-- Previous --}}
                            @if ($properties->onFirstPage())
                                <a class="btn btn-sm btn-light rounded disabled">&laquo;</a>
                            @else
                                <a href="{{ $properties->appends(request()->query())->previousPageUrl() }}"
                                class="btn btn-sm btn-light rounded">&laquo;</a>
                            @endif

                            {{-- Pages --}}
                            @php
                                $currentPage = $properties->currentPage();
                                $lastPage = $properties->lastPage();
                                $start = max($currentPage - 2, 1);
                                $end = min($currentPage + 2, $lastPage);
                            @endphp

                            @if ($start > 1)
                                <a href="{{ $properties->appends(request()->query())->url(1) }}"
                                class="btn btn-sm btn-light rounded">1</a>
                                @if ($start > 2)
                                    <span class="btn btn-sm btn-light rounded disabled">...</span>
                                @endif
                            @endif

                            @for ($i = $start; $i <= $end; $i++)
                                <a href="{{ $properties->appends(request()->query())->url($i) }}"
                                class="btn btn-sm rounded {{ $i === $currentPage ? 'btn-primary text-white' : 'btn-light' }}">
                                    {{ $i }}
                                </a>
                            @endfor

                            @if ($end < $lastPage)
                                @if ($end < $lastPage - 1)
                                    <span class="btn btn-sm btn-light rounded disabled">...</span>
                                @endif
                                <a href="{{ $properties->appends(request()->query())->url($lastPage) }}"
                                class="btn btn-sm btn-light rounded">{{ $lastPage }}</a>
                            @endif

                            {{-- Next --}}
                            @if ($properties->hasMorePages())
                                <a href="{{ $properties->appends(request()->query())->nextPageUrl() }}"
                                class="btn btn-sm btn-light rounded">&raquo;</a>
                            @else
                                <a class="btn btn-sm btn-light rounded disabled">&raquo;</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- RIGHT: Detail Client + Update Status -->
        <div class="col-md-4">
            {{-- Giliran Pemilu --}}
            <div class="card shadow-sm rounded mb-3">
                <div class="card-header text-white fw-bold" style="background-color: #3949ab;">
                <i class="bi bi-people-fill me-2"></i> Antrian Giliran
                </div>
                <div class="card-body">
                {{-- Tombol Join --}}
                {{-- <form method="POST" action="{{ route('pemilu.join', $event->id_event) }}" class="mb-3">
                    @csrf
                    <button class="btn btn-sm btn-success rounded-pill shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Join Giliran
                    </button>
                </form> --}}

                {{-- Info yang sedang berjalan --}}
                <div class="alert alert-info py-2">
                    <div class="d-flex align-items-center">
                    <i class="bi bi-lightning-charge-fill me-2"></i>
                    {{-- <div>
                        <div><strong>Sedang Giliran:</strong> {{ $current->id_account ?? '-' }}</div>
                        @if($current?->mulai_giliran && $current?->selesai_giliran)
                        <div class="small text-muted">
                            {{ \Carbon\Carbon::parse($current->mulai_giliran)->format('H:i') }}
                            – {{ \Carbon\Carbon::parse($current->selesai_giliran)->format('H:i') }}
                        </div>
                        @endif
                    </div> --}}
                    </div>
                </div>

               {{-- Tabel antrian --}}
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Urutan</th>
                                <th>Username</th>
                                <th>Waktu Tersisa</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        {{-- <tbody>
                            @forelse ($invites as $row)
                                <tr @class(['table-primary' => isset($current) && $current->id_invite === $row->id_invite])>
                                    <td class="fw-bold">{{ $row->urutan }}</td>
                                    <td>{{ $row->username }}</td>
                                    <td>
                                        @if($row->waktu_tersisa > 0)
                                            {{ gmdate('H:i:s', $row->waktu_tersisa) }}
                                        @else
                                            <span class="text-danger">Waktu Habis</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($row->status_giliran === 'Berjalan') bg-success
                                            @elseif($row->status_giliran === 'Menunggu') bg-secondary
                                            @else bg-light text-dark @endif">
                                            {{ $row->status_giliran }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4"><em>Belum ada yang join.</em></td></tr>
                            @endforelse
                        </tbody> --}}
                    </table>
                </div>
            </div>
        </div>
        {{-- Pengumuman / Transaction Log --}}
        <div class="card shadow-sm rounded mb-3">
            <div class="card-header text-white fw-bold" style="background-color: #3949ab;">
                <i class="bi bi-file-earmark-text-fill me-2"></i> Pengumuman Pilihan
            </div>
            <div class="card-body">
                {{-- List pengumuman --}}
                <div class="list-group">
                    {{-- @foreach ($logs as $log)
                        <div class="list-group-item">
                            <i class="bi bi-bell-fill me-2 text-primary"></i>
                            {{ json_decode($log->meta)->message ?? 'Tidak ada detail.' }}
                            <span class="text-muted small">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
                        </div>
                    @endforeach --}}
                </div>
            </div>
        </div>
    </div>



            {{-- <!-- History Catatan -->
            @if(count($transactionNotes))
            <div class="card shadow-sm rounded mt-3">
                <div class="card-header text-white fw-bold" style="background-color: #f4511e;">
                    <i class="bi bi-clock-history me-1"></i> Transaction Notes
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <ul class="list-group list-group-flush small">
                        @foreach($transactionNotes as $note)
                            <li class="list-group-item">
                                <div class="text-muted small">{{ \Carbon\Carbon::parse($note->tanggal_dibuat)->format('M d, Y h:i A') }}</div>
                                <div class="fw-semibold">
                                    <span class="text-secondary">{{ $note->status_transaksi }}</span> - {{ $note->catatan }}
                                    <span class="text-muted">({{ $note->account_name }})</span>
                                </div>

                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif --}}

        </div>
    </div>
</div>

  {{-- Polling ringan: per 10 detik update "Sedang Giliran" --}}
  {{-- <script>
    const stateUrl = "{{ route('pemilu.state', $event->id_event) }}";
    async function refreshTurn() {
      try {
        const res = await fetch(stateUrl, { cache: 'no-store' });
        const data = await res.json();
        // Kamu bisa pilih: reload halaman kecil atau update spot tertentu via DOM.
        // Versi simpel: reload jika current berubah.
        // (Implementasi DOM-only silakan lanjutkan sesuai struktur tabel di atas)
      } catch (e) { console.error(e); }
    }
    setInterval(refreshTurn, 10000);
  </script> --}}
<style>
/* Tambahkan ini pada file CSS kamu */
@media (max-width: 576px) {
    .pagination {
        display: flex;
        overflow-x: auto;
        white-space: nowrap;
    }
    .pagination .btn {
        min-width: 30px;
        margin: 0 5px;
    }
}
</style>
