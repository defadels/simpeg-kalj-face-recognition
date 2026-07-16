<x-app-layout>
    <x-slot name="title">Absensi Mandiri</x-slot>
    <x-slot name="breadcrumb">Rekam kehadiran harian Anda dengan validasi lokasi dan verifikasi wajah</x-slot>

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Status Kehadiran Hari Ini --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Kehadiran Hari Ini</h3>
                <span class="text-xs font-bold text-slate-400">{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl {{ $sudahMasuk ? 'bg-emerald-50/50 border border-emerald-100' : 'bg-slate-50 border border-slate-100' }} transition-colors">
                    <div class="flex items-center gap-2 mb-1.5">
                        <div class="w-2.5 h-2.5 rounded-full {{ $sudahMasuk ? 'bg-emerald-500' : 'bg-slate-300' }}"></div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Absen Masuk</span>
                    </div>
                    <div class="text-3xl font-extrabold tracking-tight {{ $sudahMasuk ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ $sudahMasuk ? substr($absensiHariIni->waktu_masuk, 0, 5) : '--:--' }}
                    </div>
                    @if($sudahMasuk && $absensiHariIni->status_kehadiran)
                        <span class="badge {{ $absensiHariIni->status_kehadiran === 'hadir' ? 'badge-green' : 'badge-yellow' }} mt-2">
                            {{ $absensiHariIni->status_kehadiran }}
                        </span>
                    @endif
                </div>

                <div class="p-4 rounded-2xl {{ $sudahKeluar ? 'bg-[#0056B3]/5 border border-[#0056B3]/10' : 'bg-slate-50 border border-slate-100' }} transition-colors">
                    <div class="flex items-center gap-2 mb-1.5">
                        <div class="w-2.5 h-2.5 rounded-full {{ $sudahKeluar ? 'bg-[#0056B3]' : 'bg-slate-300' }}"></div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Absen Keluar</span>
                    </div>
                    <div class="text-3xl font-extrabold tracking-tight {{ $sudahKeluar ? 'text-[#0056B3]' : 'text-slate-400' }}">
                        {{ $sudahKeluar ? substr($absensiHariIni->waktu_keluar, 0, 5) : '--:--' }}
                    </div>
                    @if($sudahKeluar && $absensiHariIni->jam_kerja)
                        <span class="badge badge-blue mt-2">Jam Kerja: {{ $absensiHariIni->jam_kerja }} jam</span>
                    @endif
                </div>
            </div>
        </div>

        @if(!$konfigurasi || !$konfigurasi->isKonfigured())
            <div class="card bg-amber-50 border border-amber-200">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="font-bold text-amber-800 text-sm">Lokasi Kantor Belum Dikonfigurasi</p>
                        <p class="text-amber-700 text-xs mt-0.5">Sistem absensi belum dapat digunakan sebelum Super Admin mengatur koordinat resmi kantor.</p>
                    </div>
                </div>
            </div>
        @elseif(!$karyawan->face_data)
            <div class="card bg-amber-50 border border-amber-200">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="font-bold text-amber-800 text-sm">Data Wajah Belum Terdaftar</p>
                        <p class="text-amber-700 text-xs mt-0.5">Wajah Anda belum terdaftar di sistem kepegawaian. Silakan hubungi Admin HRD untuk melakukan perekaman wajah (*face enrollment*).</p>
                    </div>
                </div>
            </div>
        @elseif($sudahMasuk && $sudahKeluar)
            <div class="card text-center py-10 bg-emerald-50/50 border border-emerald-100">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-extrabold text-emerald-800">Absensi Hari Ini Lengkap</h3>
                <p class="text-emerald-700 text-sm mt-1">Terima kasih, Anda telah merekam absensi masuk dan keluar hari ini.</p>
                <div class="mt-6 flex justify-center gap-3">
                    <a href="{{ route('karyawan.absensi.riwayat') }}" class="btn-primary">Lihat Riwayat</a>
                </div>
            </div>
        @else
            {{-- Modul Rekam Absen --}}
            <div class="card" x-data="absensiApp()" x-init="init()">
                <div class="flex items-center gap-2 mb-5 border-b border-slate-100 pb-3 justify-between">
                    <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">
                        {{ !$sudahMasuk ? 'Perekaman Absen Masuk' : 'Perekaman Absen Keluar' }}
                    </h3>
                </div>

                {{-- Progress Steps --}}
                <div class="grid grid-cols-3 gap-2 mb-6">
                    <div class="flex flex-col items-center p-3 rounded-xl border text-center transition-all"
                         :class="step >= 1 ? 'border-[#0056B3]/30 bg-[#0056B3]/5 text-[#0056B3]' : 'border-slate-100 text-slate-400'">
                        <span class="text-xs font-bold uppercase tracking-wider">Langkah 1</span>
                        <span class="text-[10px] font-semibold mt-0.5">Validasi GPS</span>
                    </div>
                    <div class="flex flex-col items-center p-3 rounded-xl border text-center transition-all"
                         :class="step >= 2 ? 'border-[#0056B3]/30 bg-[#0056B3]/5 text-[#0056B3]' : 'border-slate-100 text-slate-400'">
                        <span class="text-xs font-bold uppercase tracking-wider">Langkah 2</span>
                        <span class="text-[10px] font-semibold mt-0.5">Deteksi Wajah</span>
                    </div>
                    <div class="flex flex-col items-center p-3 rounded-xl border text-center transition-all"
                         :class="step >= 3 ? 'border-emerald-200 bg-emerald-50/50 text-emerald-700' : 'border-slate-100 text-slate-400'">
                        <span class="text-xs font-bold uppercase tracking-wider">Langkah 3</span>
                        <span class="text-[10px] font-semibold mt-0.5">Selesai</span>
                    </div>
                </div>

                {{-- Step 1: Lokasi --}}
                <div x-show="step === 1" x-transition class="space-y-4">
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6 text-center">
                        <div x-show="!lokasiStatus" class="space-y-4 py-4">
                            <div class="w-14 h-14 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto">
                                <svg class="w-7 h-7 text-[#0056B3]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-700 text-sm">Verifikasi Lokasi Anda</p>
                                <p class="text-slate-400 text-xs mt-1">Sistem akan memeriksa jarak koordinat GPS Anda dengan titik pusat kantor.</p>
                            </div>
                            <button @click="checkLokasi()" :disabled="loading" class="btn-primary mx-auto">
                                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" class="opacity-25"/><path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="currentColor" class="opacity-75"/></svg>
                                <span x-text="loading ? 'Memeriksa GPS...' : 'Mulai Pengecekan Lokasi'"></span>
                            </button>
                        </div>

                        <!-- Lokasi Valid -->
                        <div x-show="lokasiStatus === 'valid'" class="space-y-4 py-4">
                            <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto">
                                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-emerald-800 text-sm">Lokasi Sesuai Radius!</p>
                                <p class="text-emerald-700 text-xs mt-1" x-text="lokasiMessage"></p>
                            </div>
                            <button @click="step = 2; startCamera()" class="btn-success mx-auto">
                                Lanjut Verifikasi Wajah →
                            </button>
                        </div>

                        <!-- Lokasi Invalid -->
                        <div x-show="lokasiStatus === 'invalid'" class="space-y-4 py-4">
                            <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center mx-auto">
                                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-red-800 text-sm">Di Luar Radius Kantor</p>
                                <p class="text-red-700 text-xs mt-1" x-text="lokasiMessage"></p>
                            </div>
                            <button @click="lokasiStatus = null; checkLokasi()" class="btn-secondary mx-auto">Coba Lagi</button>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Kamera & Face Recognition --}}
                <div x-show="step === 2" x-transition class="space-y-4">
                    <!-- Live Camera Feed -->
                    <div class="relative rounded-2xl overflow-hidden bg-black border-2 border-slate-200 shadow-inner" style="aspect-ratio: 4/3;">
                        <video id="video" class="w-full h-full object-cover" autoplay muted playsinline></video>
                        <canvas id="overlay" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                        <!-- Floating Status overlay -->
                        <div class="absolute bottom-4 left-4 right-4 z-20">
                            <div x-show="faceStatus === 'loading'" class="bg-[#0056B3] text-white px-4 py-2.5 rounded-xl text-xs font-bold text-center shadow-lg backdrop-blur-sm bg-opacity-90">
                                ⏳ Memuat AI Face Recognition...
                            </div>
                            <div x-show="faceStatus === 'detecting'" class="bg-amber-500 text-white px-4 py-2.5 rounded-xl text-xs font-bold text-center shadow-lg backdrop-blur-sm bg-opacity-90">
                                🔍 Mendeteksi wajah Anda...
                            </div>
                            <div x-show="faceStatus === 'detected'" class="bg-emerald-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold text-center shadow-lg backdrop-blur-sm bg-opacity-90">
                                ✅ Wajah teridentifikasi! Klik tombol rekam.
                            </div>
                            <div x-show="faceStatus === 'no-face'" class="bg-red-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold text-center shadow-lg backdrop-blur-sm bg-opacity-90">
                                ⚠️ Posisikan wajah Anda tepat di dalam lingkaran.
                            </div>
                        </div>

                        <!-- Face Overlay Circle Guide -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                            <div class="w-48 h-48 sm:w-56 sm:h-56 border-4 rounded-full border-dashed transition-colors duration-300"
                                 :class="faceStatus === 'detected' ? 'border-emerald-500 bg-emerald-500/5' : 'border-white/40'"></div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button @click="step = 1; stopCamera()" class="btn-secondary flex-1">← Batal</button>
                        <button @click="rekamAbsen()" :disabled="faceStatus !== 'detected' || loading" class="btn-primary flex-1">
                            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" class="opacity-25"/><path d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" fill="currentColor" class="opacity-75"/></svg>
                            <span x-text="loading ? 'Mencatat...' : '✓ Rekam Absensi Sekarang'"></span>
                        </button>
                    </div>
                </div>

                {{-- Step 3: Selesai --}}
                <div x-show="step === 3" x-transition class="text-center py-6">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h4 class="text-lg font-bold text-emerald-800">Absensi Berhasil Dicatat</h4>
                    <p class="text-slate-500 text-xs mt-1" x-text="hasilMessage"></p>
                    
                    <div class="mt-6 flex justify-center gap-3">
                        <a href="{{ route('karyawan.dashboard') }}" class="btn-secondary">Dashboard</a>
                        <a href="{{ route('karyawan.absensi.riwayat') }}" class="btn-primary">Riwayat Kehadiran</a>
                    </div>
                </div>

                {{-- Error Message Box --}}
                <div x-show="errorMessage" x-transition class="mt-4 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <p class="text-red-800 text-xs font-semibold" x-text="errorMessage"></p>
                </div>
            </div>
        @endif
    </div>

@push('scripts')
<script type="module">
import * as faceapi from '/node_modules/@vladmandic/face-api/dist/face-api.esm.js';

const MODELS_URL = '/face-models';
const JENIS = '{{ !$sudahMasuk ? 'masuk' : 'keluar' }}';
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

document.addEventListener('alpine:init', () => {
    Alpine.data('absensiApp', () => ({
        step: 1,
        loading: false,
        lokasiStatus: null,
        lokasiMessage: '',
        faceStatus: 'loading',
        errorMessage: '',
        hasilMessage: '',
        lat: null,
        lng: null,
        faceDescriptor: null,
        faceDetectionInterval: null,
        modelsLoaded: false,

        async init() {
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODELS_URL),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODELS_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODELS_URL),
                ]);
                this.modelsLoaded = true;
                console.log('Face-API models loaded successfully');
            } catch(e) {
                console.warn('AI models failed to load pre-emptively, will retry on step 2', e);
            }
        },

        async checkLokasi() {
            this.loading = true;
            this.errorMessage = '';
            this.lokasiStatus = null;

            if (!navigator.geolocation) {
                this.lokasiStatus = 'invalid';
                this.lokasiMessage = 'Perangkat / browser Anda tidak mendukung akses lokasi GPS.';
                this.loading = false;
                return;
            }

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    this.lat = position.coords.latitude;
                    this.lng = position.coords.longitude;

                    try {
                        const response = await fetch('{{ route('karyawan.absensi.check-lokasi') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CSRF,
                            },
                            body: JSON.stringify({
                                latitude: this.lat,
                                longitude: this.lng,
                            }),
                        });

                        const data = await response.json();
                        this.lokasiStatus = data.valid ? 'valid' : 'invalid';
                        this.lokasiMessage = data.message;
                    } catch(e) {
                        this.lokasiStatus = 'invalid';
                        this.lokasiMessage = 'Koneksi ke server gagal. Coba beberapa saat lagi.';
                    }
                    this.loading = false;
                },
                (error) => {
                    this.lokasiStatus = 'invalid';
                    this.lokasiMessage = 'Akses lokasi ditolak. Aktifkan GPS dan izinkan browser mengakses lokasi.';
                    this.loading = false;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        },

        async startCamera() {
            this.faceStatus = 'loading';
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { width: 640, height: 480, facingMode: 'user' }
                });
                const video = document.getElementById('video');
                video.srcObject = stream;
                await video.play();

                if (!this.modelsLoaded) {
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri(MODELS_URL),
                        faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODELS_URL),
                        faceapi.nets.faceRecognitionNet.loadFromUri(MODELS_URL),
                    ]);
                    this.modelsLoaded = true;
                }

                this.faceStatus = 'detecting';
                this.startFaceDetection();
            } catch(e) {
                this.errorMessage = 'Gagal mengakses kamera: ' + e.message;
                this.faceStatus = 'no-face';
            }
        },

        startFaceDetection() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('overlay');
            const ctx = canvas.getContext('2d');

            this.faceDetectionInterval = setInterval(async () => {
                if (video.paused || video.ended) return;

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                const detections = await faceapi
                    .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224 }))
                    .withFaceLandmarks(true)
                    .withFaceDescriptors();

                if (detections.length === 1) {
                    this.faceStatus = 'detected';
                    this.faceDescriptor = Array.from(detections[0].descriptor);

                    // Draw green matching square around detected face
                    const box = detections[0].detection.box;
                    ctx.strokeStyle = '#10b981';
                    ctx.lineWidth = 3;
                    ctx.strokeRect(box.x, box.y, box.width, box.height);
                } else if (detections.length === 0) {
                    this.faceStatus = 'no-face';
                    this.faceDescriptor = null;
                } else {
                    this.faceStatus = 'no-face';
                    this.faceDescriptor = null;
                    
                    // Warning for multiple faces detected
                    ctx.fillStyle = 'rgba(200, 16, 46, 0.85)';
                    ctx.fillRect(10, canvas.height - 40, canvas.width - 20, 30);
                    ctx.fillStyle = 'white';
                    ctx.font = 'bold 12px Plus Jakarta Sans';
                    ctx.fillText('HANYA BOLEH SATU WAJAH DI DEPAN KAMERA!', 20, canvas.height - 20);
                }
            }, 400);
        },

        stopCamera() {
            if (this.faceDetectionInterval) {
                clearInterval(this.faceDetectionInterval);
                this.faceDetectionInterval = null;
            }
            const video = document.getElementById('video');
            if (video && video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
                video.srcObject = null;
            }
            this.faceStatus = 'detecting';
            this.faceDescriptor = null;
        },

        async rekamAbsen() {
            if (!this.faceDescriptor || this.faceDescriptor.length !== 128) {
                this.errorMessage = 'Gagal memproses wajah. Pastikan wajah terdeteksi dengan jelas di kamera.';
                return;
            }

            this.loading = true;
            this.errorMessage = '';

            try {
                const response = await fetch('{{ route('karyawan.absensi.proses') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({
                        latitude: this.lat,
                        longitude: this.lng,
                        face_descriptor: this.faceDescriptor,
                        jenis: JENIS,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    this.stopCamera();
                    this.hasilMessage = data.message;
                    this.step = 3;
                } else {
                    this.errorMessage = data.message;
                }
            } catch(e) {
                this.errorMessage = 'Terjadi kesalahan sistem saat menyimpan absen. Coba lagi.';
            }

            this.loading = false;
        }
    }));
});
</script>
@endpush
</x-app-layout>
