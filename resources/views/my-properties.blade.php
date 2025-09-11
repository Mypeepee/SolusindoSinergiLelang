@include('template.header')

<!-- Property List Start -->
<div id="property-list-section" class="container-xxl py-5">
    <div class="container">


        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                @if ($properties->count() > 0)
                    <div class="row g-4">
                        @foreach ($properties as $property)
<div class="col-lg-4 col-md-6 col-sm-6 d-flex align-items-stretch">
  <div class="property-item rounded overflow-hidden flex-fill d-flex flex-column">
    <div class="position-relative overflow-hidden property-image-wrapper">
      <a href="{{ route('property-detail', $property->id_listing) }}">
        <img src="{{ explode(',', $property->gambar)[0] }}" alt="Property Image" loading="lazy" class="w-100 h-auto">
      </a>

      <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-2 py-1 px-3 text-capitalize">
        {{ $property->tipe }}
      </div>
      <div class="bg-primary rounded text-white position-absolute end-0 top-0 m-2 py-1 px-3">
        ID: {{ $property->id_listing }}
      </div>

      {{-- Fade putih tipis di bawah gambar biar nyatu ke area konten --}}
      <div class="img-bottom-fade"></div>


      {{-- CHIP AGENT: kanan-bawah, ukuran terkunci 26×26 --}}
@if(!empty($property->agent_nama) || !empty($property->agent_picture))
@php
  $fileId   = $property->agent_picture;
  $agentImg = $fileId
    ? 'https://drive.google.com/thumbnail?id='.$fileId.'&sz=w64'   // endpoint yang kamu pakai di halaman agent
    : asset('images/default-profile.png');
  $agentAlt = $fileId
    ? 'https://drive.google.com/uc?export=view&id='.$fileId        // fallback kedua
    : asset('images/default-profile.png');
@endphp

<div class="position-absolute end-0 bottom-0 m-2 agent-chip-wrap">
  <div class="d-flex align-items-center shadow-sm rounded-pill px-2 py-1 agent-chip">
    <div class="agent-avatar rounded-circle overflow-hidden me-2">
      <img
        src="{{ $agentImg }}"
        alt="{{ $property->agent_nama ?? 'Agent' }}"
        class="w-100 h-100"
        style="object-fit:cover;"
        referrerpolicy="no-referrer"
        onerror="if(this.dataset.step!=='1'){this.dataset.step='1';this.src='{{ $agentAlt }}';}else{this.onerror=null;this.src='{{ asset('images/default-profile.png') }}';}"
      >
    </div>
    <span class="small fw-semibold text-dark agent-chip-name">
      {{ \Illuminate\Support\Str::limit($property->agent_nama ?? '—', 18) }}
    </span>
  </div>
</div>
@endif

    </div>

    <div class="p-4 pb-0">
      <h5 class="text-primary mb-3">{{ 'Rp ' . number_format($property->harga, 0, ',', '.') }}</h5>
      <a class="d-block h5 mb-2" href="{{ route('property-detail', $property->id_listing) }}">
        {{ \Illuminate\Support\Str::limit($property->deskripsi, 50, '...') }}
      </a>
      <p>
        <i class="fa fa-map-marker-alt text-primary me-2"></i>
        {{ \Illuminate\Support\Str::limit($property->lokasi, 70, '...') }}
      </p>
    </div>

    <div class="d-flex border-top border-2 border-dashed border-orange mt-auto">
      <small class="flex-fill text-center border-end border-dashed py-2">
        <i class="fa fa-vector-square text-danger me-2"></i>
        <span class="text-dark">{{ $property->luas }} m²</span>
      </small>
      <small class="flex-fill text-center border-end border-dashed py-2">
        <i class="fa fa-map-marker-alt text-danger me-2"></i>
        <span class="text-dark text-uppercase">{{ $property->kota }}</span>
      </small>
      <small class="flex-fill text-center py-2">
        <i class="fa fa-calendar-alt text-danger me-2"></i>
        <span class="text-dark">
          {{ \Carbon\Carbon::parse($property->batas_akhir_penawaran)->format('d M Y') }}
        </span>
      </small>
    </div>
  </div>
</div>
@endforeach
                    </div>

                    <!-- Pagination links -->
                    <div class="row">
                        <div class="col-12">
                            <div class="pagination d-flex justify-content-center mt-5">
                                {{-- Previous Page Link --}}
                                @if ($properties->onFirstPage())
                                    <a class="rounded disabled">&laquo;</a>
                                @else
                                    <a href="{{ $properties->appends(request()->query())->previousPageUrl() }}" class="rounded">&laquo;</a>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                    $currentPage = $properties->currentPage();
                                    $lastPage = $properties->lastPage();
                                    $start = max($currentPage - 3, 1);
                                    $end = min($currentPage + 3, $lastPage);
                                @endphp

                                {{-- First Page Link --}}
                                @if ($start > 1)
                                    <a href="{{ $properties->appends(request()->query())->url(1) }}" class="rounded">1</a>
                                    @if ($start > 2)
                                        <span class="rounded disabled">...</span>
                                    @endif
                                @endif

                                {{-- Page Number Links --}}
                                @for ($i = $start; $i <= $end; $i++)
                                    <a href="{{ $properties->appends(request()->query())->url($i) }}"
                                    class="rounded {{ $i === $currentPage ? 'active' : '' }}">{{ $i }}</a>
                                @endfor

                                {{-- Last Page Link --}}
                                @if ($end < $lastPage)
                                    @if ($end < $lastPage - 1)
                                        <span class="rounded disabled">...</span>
                                    @endif
                                    <a href="{{ $properties->appends(request()->query())->url($lastPage) }}" class="rounded">{{ $lastPage }}</a>
                                @endif

                                {{-- Next Page Link --}}
                                @if ($properties->hasMorePages())
                                    <a href="{{ $properties->appends(request()->query())->nextPageUrl() }}" class="rounded">&raquo;</a>
                                @else
                                    <a class="rounded disabled">&raquo;</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center my-5">
                        <h4>Kamu belum menambahkan properti apapun.</h4>
                        <p>Mulailah menambahkan properti dan tampilkan di listing agar lebih mudah ditemukan calon pembeli atau penyewa.</p>
                        <a href="{{ route('property.create') }}" class="btn btn-primary mt-3">+ Tambah Properti</a>

                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Property List End -->
<style>
    .property-item img {
        width: 100%;
        height: 300px; /* fix tinggi gambar */
        object-fit: cover;
        object-position: center;
    }
    .property-item {
        display: flex;
        flex-direction: column;
    }
    .property-item .p-4 {
        flex-grow: 1; /* supaya bagian tengah stretch */
    }
</style>
<style>
    /* sudah ada */
.img-bottom-fade{
  position:absolute;left:0;right:0;bottom:0;height:44px;
  background:linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,.85) 70%, #fff 100%);
  pointer-events:none;
}
.agent-chip{ background:rgba(255,255,255,.95); backdrop-filter:blur(2px); }

/* >>> fix ukuran avatar supaya gak kena rule img global */
.property-image-wrapper .agent-avatar{ width:26px; height:26px; flex:0 0 26px; }
.property-image-wrapper .agent-chip-name{
  max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}

/* jaga-jaga jika ada rule .property-image-wrapper img { width:100%!important } */
.property-image-wrapper .agent-avatar img{
  width:100% !important; height:100% !important; object-fit:cover !important;
  border-radius:50% !important; display:block;
}
.img-bottom-fade{
  position:absolute;left:0;right:0;bottom:0;height:44px;
  background:linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,.85) 70%, #fff 100%);
  pointer-events:none;
}
.agent-chip{
  background:rgba(255,255,255,.95);
  backdrop-filter:blur(2px);
}
/* Biar nama agent rapi kalau kepanjangan */
.agent-chip-name{
  max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
</style>
<style>
    .property-item {
        display: flex;
        flex-direction: column;
    }

    .property-item img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        object-position: center;
    }

    .property-item .p-4 {
        flex-grow: 1;
    }

    @media (max-width: 576px) {
        .property-item img {
            height: 220px;
        }

        .property-item h5, .property-item a.d-block {
            font-size: 1rem;
        }

        .property-item .text-primary.mb-3 {
            font-size: 1rem;
        }
    }
    .property-image-wrapper img {
        width: 100%;
        aspect-ratio: 4 / 3;
        object-fit: cover;
        border-radius: 8px;
    }
</style>
@include('template.footer')
