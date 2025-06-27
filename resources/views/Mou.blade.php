<!-- MAKE AN APPOINTMENT -->
@include('template.header')

<section id="appointment" data-stellar-background-ratio="3" style="margin-top: 20px;">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-6">
                <img src="images/appointment-image.jpg" class="img-responsive" alt="">
            </div>

            <div class="col-md-6 col-sm-6">
                <!-- CONTACT FORM HERE -->
                <form action="{{ route('generate.mou') }}" method="POST">
                    @csrf
                    @method('POST') <!-- Tambahkan ini agar bisa update data -->

                    <div class="section-title wow fadeInUp" data-wow-delay="0.4s">
                        <h2>Buat MOU</h2>

                    </div>

                    {{-- @if ($user) --}}
                        <div class="wow fadeInUp" data-wow-delay="0.8s">
                            <div class="col-md-6 col-sm-6">
                                <label for="nama">Nama pihak pertama</label>
                                <input type="text" class="form-control" id="nama" name="nama">
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email ?? '') }}">
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $user->tanggal_lahir ?? '') }}">
                            </div>

                            <div class="col-md-6 col-sm-6">
                                <label for="tempat_tinggal">Tempat Tinggal</label>
                                <input type="text" class="form-control" id="tempat_tinggal" name="tempat_tinggal" value="{{ old('tempat_tinggal', $user->tempat_tinggal ?? '') }}">
                            </div>

                            <div class="col-md-12 col-sm-12">
                                <label for="nomor_telepon">Phone Number</label>
                                <input type="tel" class="form-control" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon', $user->nomor_telepon ?? '') }}">

                                <label for="message">Additional Message</label>
                                <textarea class="form-control" rows="5" id="message" name="message">{{ old('message') }}</textarea>

                                <button type="submit" class="form-control" id="cf-submit" name="submit">Edit</button>
                            </div>
                        </div>
                    {{-- @else
                        <p>No user data available.</p>
                    @endif --}}
                </form>


            </div>
        </div>
    </div>
</section>

@include('template.footer')
