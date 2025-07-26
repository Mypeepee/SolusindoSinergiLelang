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

    <div class="container-fluid px-4 my-4">
        <div class="row">
            <!-- LEFT: Detail Lot Lelang -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0 rounded mb-4">
                    <!-- Header -->
                    <div class="card-header text-white fw-bold" style="background-color: #f15b2a;">
                        <i class="bi bi-house-door-fill me-2"></i> Detail Lot Lelang
                    </div>

                    <div class="card-body">
                        <h4 class="fw-semibold mb-3">{{ $property->judul }}</h4>

                        <div class="mb-2">
                            <strong>Lokasi:</strong>
                            <span>{{ $property->lokasi }}</span>
                        </div>

                        <div class="mb-2">
                            <strong>Vendor:</strong>
                            <span>{{ $property->vendor }}</span>
                        </div>

                        <div class="mb-2">
                            <strong>Sertifikat:</strong>
                            <span>{{ $property->sertifikat }}</span>
                        </div>

                        <!-- BOXES + FOTO -->
                        <div class="d-flex flex-wrap align-items-stretch mt-4 gap-3">
                            <!-- LEFT BOXES -->
                            <div class="d-flex flex-column justify-content-between gap-2" style="flex: 1;">
                                <div class="small-box bg-light border rounded p-2 text-center">
                                    <div class="text-muted small">Tipe</div>
                                    <div class="fw-bold">{{ ucwords($property->tipe) }}</div>
                                </div>
                                <div class="small-box bg-light border rounded p-2 text-center">
                                    <div class="text-muted small">Luas</div>
                                    <div class="fw-bold">{{ $property->luas ?? '-' }} m²</div>
                                </div>
                                <div class="small-box bg-light border rounded p-2 text-center">
                                    <div class="text-muted small">Tanggal Lelang</div>
                                    <div class="fw-bold">{{ \Carbon\Carbon::parse($property->batas_akhir_penawaran)->format('d M Y') }}</div>
                                </div>
                                <div class="small-box bg-light border rounded p-2 text-center">
                                    <div class="text-muted small">Harga Deal</div>
                                    <div class="fw-bold text-danger">Rp {{ number_format($property->harga, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <!-- RIGHT FOTO -->
                            @php
                                $fotoArray = explode(',', $property->gambar);
                                $fotoUtama = $fotoArray[0] ?? 'default.jpg';
                            @endphp
                            <div class="flex-grow-1">
                                <img src="{{ $fotoUtama }}" 
                                    alt="Foto Properti" 
                                    class="img-fluid rounded shadow-sm w-100" 
                                    style="max-height: 300px; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Detail Client + Update Status -->
            <div class="col-md-4">
                <!-- Client Detail -->
                @if ($client)
                <div class="card shadow-sm rounded mb-3">
                    <div class="card-header text-white fw-bold" style="background-color: #f4511e;">
                        <i class="bi bi-person-fill me-2"></i> Detail Client
                    </div>
                    <div class="card-body">
                        <p><strong>Nama:</strong> {{ $client->nama }}</p>
                        <p><strong>Nomor Telepon:</strong> +62{{ ltrim($client->nomor_telepon, '0') }}</p>

                        <div class="d-flex gap-2 mt-3">
                            @if ($client->gambar_ktp)
                            <a href="{{ asset('storage/ktp/' . $client->gambar_ktp) }}" 
                            class="btn btn-sm btn-primary rounded-pill shadow-sm w-50" 
                            download>
                                Download KTP
                            </a>
                            @endif

                            @if ($client->gambar_npwp)
                            <a href="{{ asset('storage/npwp/' . $client->gambar_npwp) }}" 
                            class="btn btn-sm btn-primary rounded-pill shadow-sm w-50" 
                            download>
                                Download NPWP
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Update Status -->
                <div class="card shadow-sm rounded">
                    <div class="card-header text-white fw-bold" style="background-color: #f4511e;">
                        <i class="bi bi-pencil-square"></i> Update Status
                    </div>
                    <div class="card-body">
                        <form action="{{ route('dashboard.updateOwner', ['id_listing' => $property->id_listing, 'id_account' => $client->id_account]) }}" method="POST">
                            @csrf

                            <!-- Status Saat Ini -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status Saat Ini:</label>
                                <a>
                                    {{ ucwords($statusTransaksi) }}
                                </a>
                            </div>

                            <!-- Dropdown Status -->
                            <div class="mb-3">
                            <label for="status-select" class="form-label fw-semibold">Update Status:</label>
                            <select id="status-select" name="status" class="form-select">
                                @if ($progressType === 'agent')
                                    <option value="Pending" {{ $statusTransaksi === 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="FollowUp" {{ $statusTransaksi === 'FollowUp' ? 'selected' : '' }}>Follow Up</option>
                                    <option value="BuyerMeeting" {{ $statusTransaksi === 'BuyerMeeting' ? 'selected' : '' }}>Buyer Meeting</option>
                                    <option value="Gagal" {{ $statusTransaksi === 'Gagal' ? 'selected' : '' }}>Gagal</option>
                                    <option value="Closing" {{ $statusTransaksi === 'Closing' ? 'selected' : '' }}>Closing</option>
                                @endif

                                @if ($progressType === 'register')
                                    <option value="Closing" {{ $statusTransaksi === 'Closing' ? 'selected' : '' }}>Closing</option>
                                    <option value="Kuitansi" {{ $statusTransaksi === 'Kuitansi' ? 'selected' : '' }}>Kuitansi</option>
                                    <option value="Kode Billing" {{ $statusTransaksi === 'Kode Billing' ? 'selected' : '' }}>Kode Billing</option>
                                    <option value="Kutipan Risalah Lelang" {{ $statusTransaksi === 'Kutipan Risalah Lelang' ? 'selected' : '' }}>Kutipan Risalah Lelang</option>
                                    <option value="Akte Grosse" {{ $statusTransaksi === 'Akte Grosse' ? 'selected' : '' }}>Akte Grosse</option>
                                    <option value="Balik Nama" {{ $statusTransaksi === 'Balik Nama' ? 'selected' : '' }}>Balik Nama</option>
                                @endif

                                @if ($progressType === 'pengosongan')
                                    <option value="Balik Nama" {{ $statusTransaksi === 'Balik Nama' ? 'selected' : '' }}>Balik Nama</option>
                                    <option value="Eksekusi Pengosongan" {{ $statusTransaksi === 'Eksekusi Pengosongan' ? 'selected' : '' }}>Eksekusi Pengosongan</option>
                                    <option value="Selesai" {{ $statusTransaksi === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                @endif
                            </select>
                        </div>

                        <!-- Dynamic Section -->
                        <div id="dynamic-section"></div>

                            @if ($progressType === 'register' || $progressType === 'pengosongan')
                                <!-- Notes -->
                                <div class="mb-3">
                                    <label for="message-box" class="form-label fw-semibold">Catatan Admin:</label>
                                    <textarea id="message-box" name="comment" class="form-control" rows="4">Perkiraan __ hari lagi selesai</textarea>
                                </div>
                            @endif


                            <!-- Submit Button -->
                            <button type="submit" class="btn text-white w-100" style="background-color: #f4511e;">
                                <i class="bi bi-upload me-1"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>

                <!-- History Catatan -->
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
                @endif


            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status-select');
        const dynamicSection = document.getElementById('dynamic-section');
        const hargaDeal = {{ $property->harga }};

        statusSelect.addEventListener('change', function() {
            const selected = this.value;
            dynamicSection.innerHTML = ''; // clear section

            if (selected === 'FollowUp') {
                dynamicSection.innerHTML = `
                    <div class="mb-3">
                        <a href="https://wa.me/62{{ ltrim($client->nomor_telepon, '0') }}?text={{ urlencode("Halo {$client->nama}, saya ingin memastikan apakah ada informasi yang bisa saya bantu terkait rumah di {$property->lokasi}?") }}"
                        target="_blank" class="btn btn-sm btn-success w-100">
                            <i class="bi bi-whatsapp me-1"></i> Contact Client
                        </a>
                    </div>
                `;
            }

            if (selected === 'BuyerMeeting') {
                dynamicSection.innerHTML = `
                    <div class="mb-3">
                        <a href="https://wa.me/62{!! ltrim($client->nomor_telepon, '0') !!}?text={!! urlencode("📅 Reminder Buyer Meeting\nObyek: {$property->lokasi}\nHari: Senin\nTanggal: ".date('Y-m-d')."\nPukul: 13:00\n\n📍 Lokasi: Solitaire Property\nJusticia Law Firm\nSantorini Town Square\nJl. Ronggolawe No.2A, DR. Soetomo\nKec. Tegalsari, Surabaya, Jawa Timur 60160\n\n🌐 GMAP: https://maps.app.goo.gl/6gR4s3xDtEaeEya26?g_st=awb") !!}"
                        target="_blank" class="btn btn-sm btn-primary w-100 mb-2">
                            <i class="bi bi-whatsapp me-1"></i> Contact Client
                        </a>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal & Waktu Buyer Meeting:</label>
                        <input type="datetime-local" name="buyer_meeting_datetime" class="form-control" required>
                    </div>
                `;
            }

            if (selected === 'Closing') {
                dynamicSection.innerHTML = `
                    <!-- Simulasi Komisi Agent -->
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="fa fa-calculator me-2"></i>
                        <div>
                            <strong>Proyeksi Komisi Agent:</strong>
                            <span id="proyeksi_komisi" class="fw-bold">Rp 0</span>
                        </div>
                    </div>

                    <!-- Form Harga Bidding -->
                    <div class="mb-3">
                        <label for="harga_bidding" class="form-label fw-semibold">
                            <i class="fa fa-gavel me-1 text-secondary"></i> Harga Bidding
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text" class="form-control" id="harga_bidding" name="harga_bidding" placeholder="Masukkan harga bidding" required>
                        </div>
                    </div>
                `;

                const inputBidding = document.getElementById('harga_bidding');
                const proyeksiKomisi = document.getElementById('proyeksi_komisi');

                inputBidding.addEventListener('input', function () {
                    let value = this.value.replace(/\D/g, ''); // hanya angka
                    this.value = formatRupiah(value);

                    const bidding = parseInt(value) || 0;
                    const sisa = hargaDeal - bidding;
                    const komisi = sisa > 0 ? sisa * 0.4 : 0;
                    proyeksiKomisi.textContent = 'Rp ' + formatRupiah(komisi.toString());
                });

                function formatRupiah(angka) {
                    return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            }

            if (['Kuitansi', 'Kutipan Risalah Lelang'].includes(selected)) {
                dynamicSection.innerHTML += `
                    <div class="mb-3">
                        <a href="https://docs.google.com/forms/d/e/1FAIpQLSedVS9P5oePrsoGub64dx0sH9kT5eYFUk22RlHrtYKWE3jYbQ/viewform" 
                        target="_blank" 
                        class="btn btn-outline-primary w-100">
                            <i class="bi bi-journal-text me-1"></i> Input ${selected}
                        </a>
                    </div>
                `;
            }
        });
    });
    </script>
