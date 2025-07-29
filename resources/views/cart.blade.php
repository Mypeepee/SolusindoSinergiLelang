@include('template.header')

<section class="container my-5">
    <h2 class="mb-4">Keranjang Lelang Saya</h2>

    @if ($properties->count() > 0)
        <div class="row g-0 gx-5 align-items-end">
            <div class="col-lg-6">
                <div class="text-start mx-auto mb-5 wow slideInLeft" data-wow-delay="0.1s">
                    <p>Temukan properti paling diminati di pasaran saat ini!. Jangan lewatkan kesempatan untuk mendapatkan properti terbaik dengan harga terbaik. Bertindaklah cepat dan jadikan salah satu listing istimewa ini milik Anda!</p>
                </div>
            </div>
            <div class="col-lg-6 text-start text-lg-end wow slideInRight" data-wow-delay="0.1s">
                <ul class="nav nav-pills d-inline-flex justify-content-end mb-5">
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-primary active" data-bs-toggle="pill" href="#tab-1">Semua</a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-primary" data-bs-toggle="pill" href="#tab-3">Dalam Proses</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                <div class="row g-4">
                    @foreach ($properties as $property)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="d-flex flex-column h-100 shadow-sm rounded overflow-hidden">
                            <!-- Gambar & Tag -->
                            <a href="{{ route('cart.detail', $property->id_listing) }}" class="text-decoration-none text-dark flex-grow-1 d-flex flex-column">
                                <div class="property-item overflow-hidden flex-grow-1 d-flex flex-column">
                                    <div class="position-relative">
                                        <img class="img-fluid w-100" style="height: 220px; object-fit: cover;"
                                             src="{{ explode(',', $property->gambar)[0] }}" alt="Property Image" loading="lazy">
                                        <div class="bg-primary text-white position-absolute start-0 top-0 m-2 px-2 py-1 rounded small">
                                            {{ $property->tipe }}
                                        </div>
                                    </div>

                                    <div class="p-3 flex-grow-1 d-flex flex-column">
                                        <h5 class="text-orange fw-bold">{{ 'Rp ' . number_format($property->harga, 0, ',', '.') }}</h5>
                                        <div class="fw-semibold text-dark mb-2" style="min-height: 48px;">
                                            {{ \Illuminate\Support\Str::limit($property->deskripsi, 55) }}
                                        </div>
                                        <div class="mb-3 small text-muted">
                                            <i class="fa fa-map-marker-alt text-danger me-2"></i> {{ $property->lokasi }}
                                        </div>

                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between text-center border-top border-orange pt-2 small">
                                                <div class="flex-fill border-end">
                                                    <i class="fa fa-vector-square text-danger me-1"></i>{{ $property->luas }} m²
                                                </div>
                                                <div class="flex-fill border-end">
                                                    <i class="fa fa-map-marker-alt text-danger me-1"></i>{{ $property->kota }}
                                                </div>
                                                <div class="flex-fill">
                                                    <i class="fa fa-calendar-alt text-danger me-1"></i>
                                                    {{ \Carbon\Carbon::parse($property->batas_akhir_penawaran)->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            <!-- Tombol Hapus -->
                            <div class="p-3 border-top">
                                @php
                                    $nonRemovableStatuses = ['Closing', 'Kutipan Risalah Lelang', 'Akte Grosse', 'Balik Nama'];
                                @endphp

                                @if (!in_array($property->interest_status, $nonRemovableStatuses))
                                    <button type="button" class="btn btn-outline-danger w-100"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $property->id_listing }}">
                                        <i class="fa fa-trash me-1"></i> Hapus dari Keranjang
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="deleteModal{{ $property->id_listing }}" tabindex="-1"
                                         aria-labelledby="deleteModalLabel{{ $property->id_listing }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $property->id_listing }}">
                                                        Konfirmasi Hapus
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah kamu yakin ingin menghapus properti ini dari keranjang?
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="{{ route('cart.delete', $property->id_listing) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                <div class="text-center text-success small fst-italic">
                                    <strong>Selamat!</strong> Anda memenangkan properti ini.
                                </div>

                                @endif
                            </div>
                        </div>
                    </div>

                    @endforeach

                    <div class="row">
                        <div class="col-12">
                            <div class="pagination d-flex justify-content-center mt-5">
                                @if ($properties->onFirstPage())
                                    <a class="rounded disabled">&laquo;</a>
                                @else
                                    <a href="{{ $properties->previousPageUrl() }}" class="rounded">&laquo;</a>
                                @endif

                                @for ($i = 1; $i <= $properties->lastPage(); $i++)
                                    <a href="{{ $properties->url($i) }}"
                                       class="{{ $i === $properties->currentPage() ? 'active' : '' }} rounded">{{ $i }}</a>
                                @endfor

                                @if ($properties->hasMorePages())
                                    <a href="{{ $properties->nextPageUrl() }}" class="rounded">&raquo;</a>
                                @else
                                    <a class="rounded disabled">&raquo;</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div id="tab-3" class="tab-pane fade show p-0">
                <div class="row g-4">
                    @foreach ($closingProperties as $property)
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('cart.detail', $property->id_listing) }}" class="text-decoration-none text-dark">
                                <div class="property-item rounded overflow-hidden shadow-sm">
                                    <div class="position-relative overflow-hidden">
                                        <img class="img-fluid rounded w-100" src="{{ explode(',', $property->gambar)[0] }}" alt="Property Image" loading="lazy">
                                        <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">{{ $property->tipe }}</div>
                                        <div class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">{{ $property->tipe }}</div>
                                    </div>
                                    <div class="p-4 pb-0">
                                        <h5 class="text-primary mb-3">{{ 'Rp ' . number_format($property->harga, 0, ',', '.') }}</h5>
                                        <div class="d-block h5 mb-2">{{ $property->deskripsi }}</div>
                                        <p><i class="fa fa-map-marker-alt text-primary me-2"></i>{{ $property->lokasi }}</p>
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
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    @else
        <p class="text-center">Belum ada lot yang ditambahkan ke keranjang.</p>
    @endif
</section>

@include('template.footer')
