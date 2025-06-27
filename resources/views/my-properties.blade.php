@include('template.header')

<!-- Property List Start -->
<div id="property-list-section" class="container-xxl py-5">
    <div class="container">


        <div class="tab-content">
            <div id="tab-1" class="tab-pane fade show p-0 active">
                @if ($properties->count() > 0)
                    <div class="row g-4">
                        @foreach ($properties as $property)
                            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="property-item rounded overflow-hidden">
                                    <div class="position-relative overflow-hidden">
                                        <a href="{{ route('property-detail', $property->id_listing) }}">
                                            <img class="img-fluid rounded w-100" src="{{ explode(',', $property->gambar)[0] }}" alt="Property Image" loading="lazy">
                                        </a>
                                        <style>
                                            .property-item img {
                                                width: 100%;
                                                height: 300px; /* atur tinggi sesuai selera */
                                                object-fit: cover;
                                                object-position: center;
                                            }
                                        </style>

                                        <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">{{ $property->tipe }}</div>
                                        <div class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">{{ $property->tipe }}</div>
                                    </div>
                                    <div class="p-4 pb-0">
                                        <h5 class="text-primary mb-3">{{ 'Rp ' . number_format($property->harga, 0, ',', '.') }}</h5>
                                        <a class="d-block h5 mb-2" href="{{ route('property-detail', $property->id_listing) }}">
                                            {{ \Illuminate\Support\Str::limit($property->deskripsi, 50) }}
                                        </a>
                                        <p><i class="fa fa-map-marker-alt text-primary me-2"></i>{{ $property->lokasi }}</p>
                                    </div>
                                    <div class="d-flex border-top">
                                        <small class="flex-fill text-center border-end py-2"><i class="fa fa-ruler-combined text-primary me-2"></i>{{ $property->luas_bangunan }} Sqft</small>
                                        <small class="flex-fill text-center border-end py-2"><i class="fa fa-bed text-primary me-2"></i>{{ $property->kamar_tidur }} Bed</small>
                                        <small class="flex-fill text-center py-2"><i class="fa fa-bath text-primary me-2"></i>{{ $property->kamar_mandi }} Bath</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination links -->
                    <div class="row">
                        <div class="col-12">
                            <div class="pagination d-flex justify-content-center mt-5">
                                @if ($properties->onFirstPage())
                                    <a class="rounded disabled">&laquo;</a>
                                @else
                                    <a href="{{ $properties->appends(request()->query())->previousPageUrl() }}" class="rounded">&laquo;</a>
                                @endif

                                @for ($i = 1; $i <= $properties->lastPage(); $i++)
                                    <a href="{{ $properties->appends(request()->query())->url($i) }}"
                                       class="{{ $i === $properties->currentPage() ? 'active' : '' }} rounded">{{ $i }}</a>
                                @endfor

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
