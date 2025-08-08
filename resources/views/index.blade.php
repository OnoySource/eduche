<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>educheck</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
    <header>
        <h1> <img src="{{asset('icons/logo-educhek-v2.png')}}" alt="">educheck</h1>
        <div class="tagline">
            <h2>Layanan Cek Turnitin & Parafrase Profesional</h2>
        </div>
        <div id="hamburger" onclick="showLinks()" class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </header>
        <div id="nav-links">
            <ul>
                <li>
                    <a href="#layanan">Layanan</a>
                </li>
                <li>
                    <a href="#step">Cara kerja</a>
                </li>
                <li>
                    <a href="">Kontak</a>
                </li>
                <li>
                    <a href="">Tentang kami</a>
                </li>
            </ul>
        </div>
<br>
    <!-------------------------------------------------->
   <section>
       <div class="slider">
           <div class="slides">
               <img src="{{ asset('images/image-slider-1.png') }}" alt="">
               <img src="{{ asset('images/image-slider-2.png') }}" alt="">
               <img src="{{ asset('images/image-slider-3.png') }}" alt="">
               <img src="{{ asset('images/image-slider-4.png') }}" alt="">
               <img src="{{ asset('images/image-1.png') }}" alt="">

        </div>
        </div>
    </section>
    <br> <br>
    <!-------------------------------------------------->
    <div id="container">
        <div class="box">
            <h2>Cek Turnitin</h2>
            <p>Layanan pengecekan tingkat kemiripan dokumen Anda dengan database akademik global.</p>
            <button id="layananBtn">
                <a href="#">Rp 5.000 / file</a>
            </button>
        </div>
        <div class="box">
            <h2>Parafrase</h2>
            <p>Layanan mengubah struktur kalimat tanpa mengubah makna untuk mengurangi tingkat plagiarisme.</p>
            <div class="list-button">
                <button id="layananBtn">
                    <a href="#">Skripsi/Jurnal: Rp 100.000 / file</a>
                </button>
                <button id="layananBtn">
                    <a href="#">Tesis/Buku: Rp 150.000 / file</a>
                </button>
            </div>
        </div>
    </div>
    <br> <br>
    <!-------------------------------------------------->
    <div id="container">
        @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 5000
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 5000
            });
        </script>
    @endif


    <form action="{{ route('proses.form') }}" method="POST" id="formPemesanan" enctype="multipart/form-data">
        @csrf
        <h2>Pemesanan</h2>
        <hr>
        <h3>Jenis Layanan</h3>
            <div id="checkboxId">
                <label for="layanan_turnitin" class="checkbox">
                <input type="radio" name="layanan" id="layanan_turnitin" value="turnitin" class="custom"> Cek Turnitin (Rp 5000)</label>
                <br>
                <label for="layanan_parafrase" class="checkbox">
                <input type="radio" name="layanan" id="layanan_parafrase" value="parafrase" class="custom"> Parafrase </label>
            </div>           
            <br>
        <div id="data-user">
            <div class="box-text" >
                <label for="nama" class="text">Nama Lengkap </label>
                <input type="text" name="nama" id="nama" autocomplete="name">
                @error('nama')
                    <p style="color:red;">{{ $message }}</p>
                @enderror

                <label for="email" class="text">Email</label>
                <input type="email" name="email" id="email" autocomplete="email">
                 @error('email')
                    <p style="color:red;">{{ $message }}</p>
                @enderror

                <label for="no_hp" class="text">Nomor WhatsApp</label>
                <input type="tel" name="no_hp" id="no_hp" autocomplete="tel">
                 @error('no_hp')
                    <p style="color:red;">{{ $message }}</p>
                @enderror

                <label for="univ" class="text">Instuisi/Universitas</label>
                <input type="text" name="univ" id="univ" autocomplete="organization">
                 @error('univ')
                    <p style="color:red;">{{ $message }}</p>
                @enderror
            </div>
                
            <div class="image">
                <img src="images/image-input.jpeg" alt="">
            </div>
        </div>
        <br> <br> <br> <br>

    
                <!-- Upload Dokumen -->
        <div class="box-file">
            <strong>Upload File (Dokumen yang akan dicek/diparafrase)</strong><br><br>
            <div id="drop-area-dokumen" class="drop-area">
                <img src="{{ asset('icons/circle-arrow-up-solid-full.svg') }}" alt=""> 
                <p>Pilih atau seret file ke sini</p>
                <input type="file" name="dokumen" id="fileInputDokumen" hidden>

                <button type="button" id="fileBtnDokumen">Pilih File</button>
                <p style="color: #3e5c76; font-size: 11px;">Format: DOC, DOCX, PDF (Maks. 10MB)</p>
                @error('dokumen')
                    <p style="color:red;">{{ $message }}</p>
                @enderror
            </div>
            <div id="fileListDokumen" class="file-list"></div>
        </div>
        <br><br>
        <!-- Upload Bukti -->
        <div class="box-bukti">
            <strong>Upload Bukti Transfer</strong><br><br>
             <div class="payment">
                <p><strong>Transfer ke Dana</strong> : 082377179728 (Edi Sulaiman)</p>
                <div class="qris">
                    <img src="{{asset('icons/educhek-qris.png')}}" alt="">
                </div>
            </div>
            <br><br>

            <div id="drop-area-bukti" class="drop-area">
                <img src="{{ asset('icons/images-solid-full.svg') }}" alt=""> 
                <p>Pilih atau seret file ke sini</p>

                <input type="file" name="bukti" id="fileInputBukti" hidden>
                <button type="button" id="fileBtnBukti">Pilih File</button>

                <p style="color: #3e5c76; font-size: 11px;">Format: JPG, PNG (Maks. 5MB)</p>
                @error('bukti')
                    <p style="color:red;">{{ $message }}</p>
                @enderror
            </div>
            <div id="fileListBukti" class="file-list"></div>
        </div>

    <br>
    <button type="submit" style="font-weight:bold;" id="pemesananBtn">Kirim Pesanan</button>
    <!-------------------------------------------------->
    </form>

    </div>
    <br> <br>
    <!-------------------------------------------------->

    <div id="container">
        <div class="step">
            <h3>Cara Kerja</h3>
            <div class="step-list">
                <div class="step-box">
                    <h4>1</h4>
                    <p><strong>Pilih Layanan</strong></p>
                    <small>Pilih layanan dan jenis dokumen yang Anda butuhkan</small>
                </div>
                <div class="step-box">
                    <h4>2</h4>
                    <p><strong>Upload File</strong></p>
                    <small>Upload dokumen yang ingin dicek atau diparafrase</small>
                </div>
                <div class="step-box">
                    <h4>3</h4>
                    <p><strong>Lakukan Pembayaran</strong></p>
                    <small>Transfer sesuai biaya dan upload bukti pembayaran</small>
                </div>
                <div class="step-box">
                    <h4>4</h4>
                    <p><strong>Terima Hasil</strong></p>
                    <small>Hasil akan dikirim ke WhatsApp Anda</small>
                </div>
            </div>
        </div>
        <br>
    </div>
    <br> <br>
    <!-------------------------------------------------->
    <div id="container">
        <div class="contact">
            <h3>Kontak Kami</h3>
            <div id="contact-person">
                <div class="contact-box">
                    <div class="icons-contact">
                        <img src="{{ asset('icons/phone-solid-full.svg') }}" alt=""> 
                    </div>
                    <p>082377179728</p>
                </div>
                <div class="contact-box">
                    <div class="icons-contact">
                        <img src="{{ asset('icons/envelope-solid-full.svg') }}" alt=""> 
                </div>
                <p>sulaimanedi14@gmail.com</p>
            </div>
            <div class="contact-box">
                <div class="icons-contact">
                    <img src="{{ asset('icons/clock-regular-full.svg') }}" alt=""> 
                </div>
                <p>Buka Setiap hari, 08.00 - 21.00 WIB</p>
            </div>
        </div>
    </div>
    </div>
    <br> <br>
    <!-------------------------------------------------->
    <footer>
        <p>© 2023 Educheck. Semua hak dilindungi.</p>
        <p>Layanan Cek Turnitin & Parafrase Profesional </p>
    </footer>


    
    <!-------------------------------------------------->
    <script src="{{ asset('js/main.js') }}"></script>
    <!-------------------------------------------------->
</body>
</html>
