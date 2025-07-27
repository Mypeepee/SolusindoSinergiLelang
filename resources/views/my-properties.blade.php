@include('template.header')

<!-- Property List Start -->
<div id="property-list-section" class="container-xxl py-5">
    <div class="container">


        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                @if ($properties->count() > 0)
                    <div class="row g-4">
                        @foreach ($properties as $property)
                            <div class="col-lg-4 col-md-6 d-flex align-items-stretch">
                                <div class="property-item rounded overflow-hidden flex-fill d-flex flex-column">
                                    <div class="position-relative overflow-hidden">
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
                                        <a href="{{ route('property-detail', $property->id_listing) }}">
                                            <img class="img-fluid rounded w-100 property-img-square" src="{{ explode(',', $property->gambar)[0] }}" alt="Property Image" loading="lazy">
                                        </a>
                                        <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">{{ $property->tipe }}</div>
                                        <div class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">{{ $property->tipe }}</div>
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
                                    <div class="d-flex border-top border-2 border-dashed border-orange">
                                        <!-- Luas Properti -->
                                        <small class="flex-fill text-center border-end border-dashed py-2">
                                            <i class="fa fa-vector-square text-danger me-2"></i>
                                            <span class="text-dark">{{ $property->luas }} m²</span>
                                        </small>

                                        <!-- Kota -->
                                        <small class="flex-fill text-center border-end border-dashed py-2">
                                            <i class="fa fa-map-marker-alt text-danger me-2"></i>
                                            <span class="text-dark text-uppercase">{{ $property->kota }}</span>
                                        </small>

                                        <!-- Batas Penawaran -->
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

@include('template.footer')
