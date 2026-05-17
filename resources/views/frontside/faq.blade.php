@extends('frontside.layouts.app')

@section('content')
    <!-- Help & Support Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="fw-bold">Bantuan & Dukungan</h1>
                <p class="text-muted">Jawaban atas pertanyaan paling umum yang sering diajukan oleh pengunjung terkait pembelian dan penyewaan properti di platform kami.</p>
            </div>

            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="accordion" id="faqAccordion">
                        <!-- FAQ 1 -->
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="faq1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1" style="color: #0f636d;">
                                    Bagaimana cara membeli properti di Dabelyuland?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Anda dapat mencari properti melalui fitur pencarian, lalu klik pada properti yang Anda minati. Gunakan tombol "Hubungi via WhatsApp" atau "Buat Janji" untuk menghubungi agen secara langsung dan mengatur pertemuan.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2" style="color: #0f636d;">
                                    Apakah harga properti bisa dinegosiasikan?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Tentu. Harga pada listing adalah harga penawaran. Anda dapat berdiskusi langsung dengan agen properti untuk melakukan negosiasi sesuai kondisi pasar dan kesepakatan kedua pihak.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3" style="color: #0f636d;">
                                    Bagaimana jika saya ingin menyewa bukan membeli?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Kami memiliki listing properti dengan status "Disewakan". Gunakan filter pencarian untuk memilih properti sewaan. Anda bisa menghubungi agen properti seperti biasa untuk detail harga, durasi sewa, dan persyaratan lainnya.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="faq4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4" style="color: #0f636d;">
                                    Apakah semua properti sudah bersertifikat dan legal?
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Ya, semua properti yang terdaftar di Dabelyuland telah diverifikasi oleh tim legal kami dan memiliki dokumen legal yang sah seperti SHM, SHGB, atau IMB. Namun, Anda tetap dapat meminta salinan dokumen dari agen untuk pemeriksaan lebih lanjut.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="faq5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5" style="color: #0f636d;">
                                    Bagaimana saya bisa menghubungi customer support?
                                </button>
                            </h2>
                            <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="faq5" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Anda dapat menghubungi tim layanan pelanggan kami melalui WhatsApp di <strong style="color: #0f636d;">+62 821-2727-7747</strong> atau email ke <strong>support@dabelyuland.com</strong>. Kami siap membantu Anda setiap hari pukul 09.00 - 17.00 WIB.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6 -->
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="faq6">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6" style="color: #0f636d;">
                                    Apakah saya bisa mengajukan KPR melalui Dabelyuland?
                                </button>
                            </h2>
                            <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="faq6" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Ya, kami bekerja sama dengan berbagai bank terpercaya untuk memfasilitasi pengajuan KPR. Tim kami akan membantu Anda dalam proses simulasi, pengumpulan dokumen, dan pengajuan KPR dengan mudah.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 7 -->
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="faq7">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7" style="color: #0f636d;">
                                    Apakah saya bisa melihat properti secara virtual?
                                </button>
                            </h2>
                            <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="faq7" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Kami menyediakan tur virtual untuk beberapa properti unggulan. Cari properti dengan ikon "360° Virtual Tour" dan klik untuk melihat properti secara interaktif sebelum kunjungan langsung.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Help & Support End -->
@endsection