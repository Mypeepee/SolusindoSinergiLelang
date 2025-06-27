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
                        <a class="btn btn-outline-primary" data-bs-toggle="pill" href="#tab-2">Menang</a>
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
                            <div class="position-relative">
                                <a href="{{ route('cart.detail', $property->id_listing) }}" class="text-decoration-none text-dark">
                                    <div class="property-item rounded overflow-hidden">
                                        <div class="position-relative overflow-hidden">
                                            <img class="img-fluid rounded w-100" src="{{ explode(',', $property->gambar)[0] }}" alt="Property Image" loading="lazy">
                                            <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">{{ $property->tipe }}</div>
                                            <div class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">{{ $property->tipe }}</div>
                                        </div>
                                        <div class="p-4 pb-0">
                                            <h5 class="text-primary mb-3">{{ 'Rp ' . number_format($property->harga, 0, ',', '.') }}</h5>
                                            <div class="d-block h5 mb-2">{{ \Illuminate\Support\Str::limit($property->deskripsi, 60) }}</div>

                                            <p><i class="fa fa-map-marker-alt text-primary me-2"></i>{{ $property->lokasi }}</p>
                                        </div>
                                        <div class="d-flex border-top">
                                            <small class="flex-fill text-center border-end py-2">
                                                <i class="fa fa-ruler-combined text-primary me-2"></i>{{ $property->luas_bangunan }} m²
                                            </small>
                                            <small class="flex-fill text-center border-end py-2">
                                                <i class="fa fa-bed text-primary me-2"></i>{{ $property->kamar_tidur }} Bed
                                            </small>
                                            <small class="flex-fill text-center py-2">
                                                <i class="fa fa-bath text-primary me-2"></i>{{ $property->kamar_mandi }} Bath
                                            </small>
                                        </div>
                                    </div>
                                </a>

                                <div class="p-3">
                                    <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $property->id_listing }}">
                                        <i class="fa fa-trash me-1"></i> Hapus dari Keranjang
                                    </button>

                                    <div class="modal fade" id="deleteModal{{ $property->id_listing }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $property->id_listing }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $property->id_listing }}">Konfirmasi Hapus</h5>
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

            <div id="tab-2" class="tab-pane fade show p-0">
                <div class="row g-4">
                    @foreach ($menangProperties as $property)
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
                                    <div class="d-flex border-top">
                                        <small class="flex-fill text-center border-end py-2">
                                            <i class="fa fa-ruler-combined text-primary me-2"></i>{{ $property->luas_bangunan }} m²
                                        </small>
                                        <small class="flex-fill text-center border-end py-2">
                                            <i class="fa fa-bed text-primary me-2"></i>{{ $property->kamar_tidur }} Bed
                                        </small>
                                        <small class="flex-fill text-center py-2">
                                            <i class="fa fa-bath text-primary me-2"></i>{{ $property->kamar_mandi }} Bath
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
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
                                    <div class="d-flex border-top">
                                        <small class="flex-fill text-center border-end py-2">
                                            <i class="fa fa-ruler-combined text-primary me-2"></i>{{ $property->luas_bangunan }} m²
                                        </small>
                                        <small class="flex-fill text-center border-end py-2">
                                            <i class="fa fa-bed text-primary me-2"></i>{{ $property->kamar_tidur }} Bed
                                        </small>
                                        <small class="flex-fill text-center py-2">
                                            <i class="fa fa-bath text-primary me-2"></i>{{ $property->kamar_mandi }} Bath
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
