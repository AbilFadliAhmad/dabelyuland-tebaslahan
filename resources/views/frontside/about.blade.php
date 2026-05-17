@extends('layouts.index')

@section('content')
    <!-- Tentang Kami dan Call to Action -->
    <div class="container-xxl py-5">
        <div class="container">
            <!-- Header Section -->
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1 class="fw-bold mb-3">Tentang Kami</h1>
                <p class="text-muted">Membangun Masa Depan Melalui Properti</p>
            </div>

            <!-- Tentang Kami Section -->
            <div class="row g-5 align-items-center">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <img class="img-fluid rounded w-100" src="{{ asset('frontside/img/owner.jpg') }}" alt="Founder & CEO">
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.3s">
                    <h2 class="mb-3">Hardi Widyanto</h2>
                    <h5 class="text-muted mb-4">Founder & CEO Dabelyuland</h5>
                    <p class="text-muted">
                        Dengan visi membangun properti yang berkualitas dan mudah diakses oleh semua kalangan, Hardi Widyanto memimpin Dabelyuland dengan semangat profesionalisme, kepercayaan, dan inovasi. Beliau percaya bahwa rumah bukan hanya tempat tinggal, tetapi juga bagian dari masa depan yang lebih baik.
                    </p>
                    <div class="d-flex align-items-center mt-4">
                        <a href="tel:+6281334160084" class="btn btn-primary py-3 px-4 me-3"><i class="fa fa-phone-alt me-2"></i>Hubungi Sekarang</a>
                        {{-- <a href="{{ route('contact') }}" class="btn btn-outline-dark py-3 px-4"><i class="fa fa-calendar-alt me-2"></i>Buat Janji</a> --}}
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="my-5 text-center">
                <hr class="w-50 mx-auto" style="border-top: 2px solid #ddd;">
            </div>

            <!-- Visi dan Misi Section -->
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="fw-bold mb-4">Visi dan Misi Kami</h2>
                    <p class="text-muted">
                        Dabelyuland Indonesia didirikan pada tahun 2024 dengan misi menyediakan properti berkualitas untuk masyarakat Indonesia. Kami percaya bahwa rumah bukan hanya tempat tinggal, tetapi fondasi kehidupan yang aman dan nyaman.
                    </p>
                    <p class="text-muted">
                        Dengan tim yang berpengalaman di bidang desain, konstruksi, dan pemasaran, kami telah membantu ratusan pelanggan menemukan hunian impian mereka. Fokus utama kami adalah transparansi, kepercayaan, dan kualitas dalam setiap proyek properti yang kami bangun dan pasarkan.
                    </p>
                    <p class="text-muted">
                        Kami terus berinovasi dalam mengembangkan teknologi e-commerce properti agar proses jual beli properti menjadi lebih mudah, cepat, dan aman untuk semua kalangan masyarakat.
                    </p>
                    <p class="text-muted">
                        Bergabunglah dengan kami dalam perjalanan membangun masa depan yang lebih baik melalui properti. Temukan rumah impian Anda bersama Dabelyuland Indonesia.
                    </p>
                </div>
            </div>

            <!-- Social Media Area -->
            <section class="container my-5">
                <div class="text-center mb-5">
                    <h2 class="fw-bold">Media Sosial Terupdate</h2>
                    <p class="text-muted">
                        Dapatkan informasi terbaru di media sosial Dabelyuland. Kami ingin berbagi info terupdate dengan Anda. Simak informasi terbaru dari kami.
                    </p>
                </div>

                <div class="swiper socialSwiper">
                    <div class="swiper-wrapper">
                        <!-- Facebook -->
                        <div class="swiper-slide">
                            <div class="card social-card">
                                <div class="card-body text-center">
                                    <a href="https://www.facebook.com/profile.php?id=61562823042702" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('frontside/img/media/profile-fb.png') }}" alt="Facebook" class="social-icon" />
                                        <h5 class="mt-3">@Hardi Widyanto</h5>
                                        <p class="text-muted">
                                            <i class="bi bi-facebook me-1"></i> Facebook
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Instagram -->
                        <div class="swiper-slide">
                            <div class="card social-card">
                                <div class="card-body text-center">
                                    <a href="https://www.instagram.com/hardi.widyanto/" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('frontside/img/media/profile-ig.png') }}" alt="Instagram" class="social-icon" />
                                        <h5 class="mt-3">@hardiwidyanto</h5>
                                        <p class="text-muted">
                                            <i class="bi bi-instagram me-1"></i> Instagram
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Youtube -->
                        <div class="swiper-slide">
                            <div class="card social-card">
                                <div class="card-body text-center">
                                    <a href="https://www.youtube.com/@hardiwidyanto" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ asset('frontside/img/media/profile-yt.png') }}" alt="YouTube" class="social-icon" />
                                        <h5 class="mt-3">@hardi widyanto</h5>
                                        <p class="text-muted">
                                            <i class="bi bi-youtube me-1"></i> YouTube
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- TikTok -->
                        <div class="swiper-slide">
                            <div class="card social-card">
                                <div class="card-body text-center">
                                    <a href="https://www.tiktok.com/@dabelyuland.indonesia?_t=ZS-8w6S74mJLGh&_r=1" target="_blank" rel="noopener noreferrer">
                                        <img src="https://dabelyuland-indonesia.vercel.app/assets/tiktok.png?imwidth=1080" alt="TikTok" class="social-icon" />
                                        <h5 class="mt-3">@hardi.widyanto</h5>
                                        <p class="text-muted">
                                            <i class="fab fa-tiktok me-1"></i> TikTok
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigasi -->
                    <div class="swiper-pagination mt-3"></div>
                </div>
            </section>

            <!-- Call to Action Section -->
            <div class="row g-5 align-items-center mt-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <img class="img-fluid rounded w-100" src="{{ asset('frontside/img/icon/logo-green.svg') }}" alt="Logo Dabelyuland">
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.3s">
                    <h2 class="fw-bold mb-4">Hubungi Kami</h2>
                    <p class="text-muted">
                        Kami siap membantu Anda menemukan properti impian Anda. Hubungi kami sekarang untuk mendapatkan informasi lebih lanjut atau buat janji untuk konsultasi langsung dengan tim kami.
                    </p>
                    <div class="d-flex align-items-center mt-4">
                        <a href="tel:+6282127277747" class="btn btn-primary py-3 px-4 me-3"><i class="fa fa-phone-alt me-2"></i>Hubungi Sekarang</a>
                        {{-- <a href="{{ route('contact') }}" class="btn btn-outline-dark py-3 px-4"><i class="fa fa-calendar-alt me-2"></i>Buat Janji</a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection