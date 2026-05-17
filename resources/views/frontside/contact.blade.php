@extends('frontside.layouts.app')

@section('content')
    <!-- Contact Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <h1 class="mb-3">Kontak Tim Dabelyuland</h1>
                <p>Hubungi kami untuk pertanyaan seputar properti, konsultasi pembelian, atau kerja sama bisnis. Kami siap
                    melayani Anda dengan cepat & ramah!</p>
            </div>

            <div class="row g-4">
                @forelse ($contacts as $item)
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="bg-white border shadow-sm rounded p-4 h-100 text-center contact-card">
                            <!-- Foto Profil -->
                            <img src="{{ $item->foto ? asset('storage/' . $item->foto) : asset('frontside/img/default-user.png') }}"
                                alt="Foto {{ $item->nama }}" class="rounded-circle mb-3" width="80" height="80">

                            <!-- Nama -->
                            <h5 class="mb-1">{{ $item->nama }}</h5>

                            <!-- Jabatan -->
                            <p class="text-muted small mb-2">{{ $item->jabatan }}</p>

                            <!-- Email -->
                            <p class="mb-1">
                                <i class="fa fa-envelope text-hijau me-2"></i>{{ $item->email }}
                            </p>

                            <!-- WhatsApp Button -->
                            @if ($item->nowa)
                                <a href="https://wa.me/{{ $item->nowa }}" target="_blank"
                                    class="btn btn-sm btn-primary mt-2 w-100">
                                    <i class="fab fa-whatsapp me-1"></i> Chat via WhatsApp
                                </a>
                            @else
                                <span class="text-muted small">WhatsApp tidak tersedia</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">Belum ada kontak tersedia.</p>
                    </div>
                @endforelse
            </div>

            <!-- Map Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                        <h4 class="mb-3">Lokasi Kantor Pusat Dabelyuland Indonesia</h4>
                        <p>Hubungi kami untuk pertanyaan seputar properti, konsultasi pembelian, atau kerja sama bisnis.
                            Kami siap melayani Anda dengan cepat & ramah!</p>
                    </div>
                    <div class="rounded overflow-hidden">
                       <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5103.739503454573!2d112.21215587500258!3d-7.545576592467945!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7841e10327e889%3A0xf1b2800cd023ff94!2sDabelyuland%20Indonesia%20%7C%20Jasa%20Desain%20Interior%20Bagunan%20Renovasi%20Rumah%20Build%20%7C%20dijual%20Tanah%20kavling%20%7C%20di%20jombang%20jawa%20timur!5e1!3m2!1sid!2sid!4v1746006013746!5m2!1sid!2sid" 
                            width="100%" 
                            height="400" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->
@endsection
