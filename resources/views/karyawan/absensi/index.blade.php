<x-app-layout>
    <x-slot name="title">Absensi</x-slot>
    <x-slot name="breadcrumb">Rekam kehadiran dengan verifikasi lokasi dan wajah</x-slot>

    <div class="max-w-2xl mx-auto space-y-5">

        {{-- Status Hari Ini --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Status Kehadiran Hari Ini</h3>
                <span class="text-sm text-slate-500">{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 rounded-xl {{ $sudahMasuk ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-200' }}">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-3 h-3 rounded-full {{ $sudahMasuk ? 'bg-emerald-400' : 'bg-slate-300' }}"></div>
                        <span class="text-sm font-medium {{ $sudahMasuk ? 'text-emerald-700' : 'text-slate-500' }}">Masuk</span>
                    </div>
                    <div class="text-2xl font-bold {{ $sudahMasuk ? 'text-emerald-600' : 'text-slate-400' }}">
                        {{ $sudahMasuk ? substr($absensiHariIni->waktu_masuk, 0, 5) : '--:--' }}
                    </div>
                    @if($sudahMasuk && $absensiHariIni->status_kehadiran)
                        <span class="badge {{ $absensiHariIni->status_kehadiran === 'hadir' ? 'badge-green' : 'badge-yellow' }} mt-1">
                            {{ ucfirst($absensiHariIni->status_kehadiran) }}
                        </span>
                    @endif
                </div>
                <div class="p-4 rounded-xl {{ $sudahKeluar ? 'bg-sky-50 border border-sky-200' : 'bg-slate-50 border border-slate-200' }}">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-3 h-3 rounded-full {{ $sudahKeluar ? 'bg-sky-400' : 'bg-slate-300' }}"></div>
                        <span class="text-sm font-medium {{ $sudahKeluar ? 'text-sky-700' : 'text-slate-500' }}">Keluar</span>
                    </div>
                    <div class="text-2xl font-bold {{ $sudahKeluar ? 'text-sky-600' : 'text-slate-400' }}">
                        {{ $sudahKeluar ? substr($absensiHariIni->waktu_keluar, 0, 5) : '--:--' }}
                    </div>
                    @if($sudahKeluar && $absensiHariIni->jam_kerja)
                        <span class="badge badge-blue mt-1">{{ $absensiHariIni->jam_kerja }} jam</span>
                    @endif
                </div>
            </div>
        </div>

        @if(!$konfigurasi || !$konfigurasi->isKonfigured())
            <div class="card bg-amber-50 border border-amber-200">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="font-medium text-amber-800">Konfigurasi Belum Lengkap</p>
                        <p class="text-amber-600 text-sm">Lokasi kantor belum diatur oleh administrator. Hubungi Super Admin.</p>
                    </div>
                </div>
            </div>
        @elseif(!$karyawan->face_data)
            <div class="card bg-amber-50 border border-amber-200">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <div>
                        <p class="font-medium text-amber-800">Wajah Belum Terdaftar</p>
                        <p class="text-amber-600 text-sm">Hubungi Admin HRD untuk melakukan face enrollment terlebih dahulu.</p>
                    </div>
                </div>
            </div>
        @elseif($sudahMasuk && $sudahKeluar)
            <div class="card bg-emerald-50 border border-emerald-200 text-center py-8">
                <svg class="w-16 h-16 text-emerald-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="text-lg font-semibold text-emerald-700">Absensi Hari Ini Selesai</h3>
                <p class="text-emerald-600 text-sm mt-1">Jam kerja: <strong>{{ $absensiHariIni->jam_kerja }} jam</strong></p>
                <a href="{{ route('karyawan.absensi.riwayat') }}" class="btn-secondary mt-4">Lihat Riwayat</a>
            </div>
        @else
            {{-- Modul Absensi Dual Auth --}}
            <div class="card" x-data="absensiApp()" x-init="init()">
                <h3 class="font-semibold text-slate-800 mb-1">
                    {{ !$sudahMasuk ? 'Rekam Absen Masuk' : 'Rekam Absen Keluar' }}
                </h3>
                <p class="text-slate-500 text-sm mb-5">Ikuti langkah-langkah berikut untuk merekam kehadiran</p>

                {{-- Progress Steps --}}
                <div class="flex items-center mb-6">
                    <div class="flex items-center gap-2" :class="step >= 1 ? 'text-sky-600' : 'text-slate-400'">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                             :class="step > 1 ? 'bg-emerald-500 text-white' : step === 1 ? 'bg-sky-500 text-white' : 'bg-slate-200'">
                            <span x-show="step <= 1">1</span>
                            <svg x-show="step > 1" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </div>
                        <span class="text-sm font-medium">Lokasi</span>
                    </div>
                    <div class="flex-1 h-0.5 mx-3" :class="step > 1 ? 'bg-emerald-400' : 'bg-slate-200'"></div>
                    <div class="flex items-center gap-2" :class="step >= 2 ? 'text-sky-600' : 'text-slate-400'">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                             :class="step > 2 ? 'bg-emerald-500 text-white' : step === 2 ? 'bg-sky-500 text-white' : 'bg-slate-200'">
                            <span x-show="step <= 2">2</span>
                            <svg x-show="step > 2" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </div>
                        <span class="text-sm font-medium">Wajah</span>
                    </div>
                    <div class="flex-1 h-0.5 mx-3" :class="step > 2 ? 'bg-emerald-400' : 'bg-slate-200'"></div>
                    <div class="flex items-center gap-2" :class="step >= 3 ? 'text-emerald-600' : 'text-slate-400'">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                             :class="step === 3 ? 'bg-emerald-500 text-white' : 'bg-slate-200'">3</div>
                        <span class="text-sm font-medium">Selesai</span>
                    </div>
                </div>

                {{-- Step 1: Lokasi --}}
                <div x-show="step === 1" x-transition>
                    <div class="bg-slate-50 rounded-xl p-5 text-center mb-4">
                        <div x-show="!lokasiStatus" class="space-y-3">
                            <div class="w-16 h-16 bg-sky-100 rounded-2xl flex items-center justify-center mx-auto">
                                <svg class="w-8 h-8 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="font-medium text-slate-700">Verifikasi Lokasi</p>
                                <p class="text-slate-500 text-sm mt-1">Sistem akan memeriksa apakah Anda berada di dalam radius kantor ({{ $konfigurasi?->radius_meter }}m)</p>
                            </div>
                            <button @click="checkLokasi()" :disabled="loading"
                                class="btn-primary mx-auto">
                                <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span x-text="loading ? 'Memeriksa lokasi...' : 'Periksa Lokasi Saya'"></span>
                            </button>
                        </div>

                        <div x-show="lokasiStatus === 'valid'" class="space-y-2">
                            <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto">
                                <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="font-medium text-emerald-700">Lokasi Valid!</p>
                            <p class="text-emerald-600 text-sm" x-text="lokasiMessage"></p>
                            <button @click="step = 2; startCamera()" class="btn-success mx-auto">
                                Lanjut ke Verifikasi Wajah →
                            </button>
                        </div>

                        <div x-show="lokasiStatus === 'invalid'" class="space-y-2">
                            <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mx-auto">
                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="font-medium text-red-700">Di Luar Radius Kantor</p>
                            <p class="text-red-600 text-sm" x-text="lokasiMessage"></p>
                            <button @click="lokasiStatus = null; checkLokasi()" class="btn-secondary mx-auto">Coba Lagi</button>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Face Recognition --}}
                <div x-show="step === 2" x-transition>
                    <div class="space-y-4">
                        <div class="text-center">
                            <p class="text-slate-600 text-sm mb-3">Posisikan wajah Anda di dalam frame kamera, pastikan pencahayaan cukup</p>
                        </div>

                        <!-- Camera Feed -->
                        <div class="relative rounded-xl overflow-hidden bg-black" style="aspect-ratio: 4/3;">
                            <video id="video" class="w-full h-full object-cover" autoplay muted playsinline></video>
                            <canvas id="overlay" class="absolute inset-0 w-full h-full"></canvas>

                            <!-- Face status indicator -->
                            <div class="absolute top-3 left-3 right-3">
                                <div x-show="faceStatus === 'detecting'" class="bg-amber-500/90 text-white px-3 py-1.5 rounded-lg text-sm text-center backdrop-blur-sm">
                                    🔍 Mendeteksi wajah...
                                </div>
                                <div x-show="faceStatus === 'detected'" class="bg-emerald-500/90 text-white px-3 py-1.5 rounded-lg text-sm text-center backdrop-blur-sm">
                                    ✅ Wajah terdeteksi! Klik "Rekam Absen"
                                </div>
                                <div x-show="faceStatus === 'no-face'" class="bg-red-500/90 text-white px-3 py-1.5 rounded-lg text-sm text-center backdrop-blur-sm">
                                    ❌ Wajah tidak terdeteksi, adjust posisi
                                </div>
                                <div x-show="faceStatus === 'loading'" class="bg-sky-500/90 text-white px-3 py-1.5 rounded-lg text-sm text-center backdrop-blur-sm">
                                    ⏳ Memuat model AI...
                                </div>
                            </div>

                            <!-- Overlay guide -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="w-48 h-56 border-2 rounded-full" :class="faceStatus === 'detected' ? 'border-emerald-400' : 'border-white/50'"></div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button @click="step = 1; stopCamera()" class="btn-secondary flex-1">← Kembali</button>
                            <button @click="rekamAbsen()" :disabled="faceStatus !== 'detected' || loading" class="btn-primary flex-1">
                                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span x-text="loading ? 'Memproses...' : '✓ Rekam Absen {{ !$sudahMasuk ? 'Masuk' : 'Keluar' }}'"></span>
                            </button>
                        </div>

                        <p class="text-xs text-slate-400 text-center">Gambar kamera tidak dikirim ke server — hanya data deskriptor wajah yang diproses</p>
                    </div>
                </div>

                {{-- Step 3: Selesai --}}
                <div x-show="step === 3" x-transition class="text-center py-4">
                    <div class="w-20 h-20 bg-emerald-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h4 class="text-lg font-semibold text-emerald-700">Absensi Berhasil!</h4>
                    <p class="text-slate-500 text-sm mt-1" x-text="hasilMessage"></p>
                    <div class="mt-4 flex gap-3 justify-center">
                        <a href="{{ route('karyawan.dashboard') }}" class="btn-secondary">← Dashboard</a>
                        <a href="{{ route('karyawan.absensi.riwayat') }}" class="btn-primary">Lihat Riwayat</a>
                    </div>
                </div>

                {{-- Error message --}}
                <div x-show="errorMessage" x-transition class="mt-3 p-3 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-red-700 text-sm" x-text="errorMessage"></p>
                </div>
            </div>
        @endif

        {{-- Info Konfigurasi --}}
        @if($konfigurasi)
        <div class="card">
            <h4 class="font-medium text-slate-700 mb-3 text-sm">Ketentuan Absensi</h4>
            <div class="grid grid-cols-3 gap-3 text-center text-xs">
                <div class="p-3 bg-slate-50 rounded-lg">
                    <div class="font-bold text-slate-700">{{ substr($konfigurasi->jam_masuk, 0, 5) }}</div>
                    <div class="text-slate-500">Jam Masuk</div>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg">
                    <div class="font-bold text-slate-700">{{ substr($konfigurasi->jam_keluar, 0, 5) }}</div>
                    <div class="text-slate-500">Jam Keluar</div>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg">
                    <div class="font-bold text-slate-700">{{ $konfigurasi->toleransi_menit }} mnt</div>
                    <div class="text-slate-500">Toleransi</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>

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
            // Preload face-api models
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODELS_URL),
                    faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODELS_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODELS_URL),
                ]);
                this.modelsLoaded = true;
                console.log('Face-API models loaded');
            } catch(e) {
                console.warn('Models not found at /face-models, will retry on camera start', e);
            }
        },

        async checkLokasi() {
            this.loading = true;
            this.errorMessage = '';
            this.lokasiStatus = null;

            if (!navigator.geolocation) {
                this.lokasiStatus = 'invalid';
                this.lokasiMessage = 'Browser Anda tidak mendukung geolocation.';
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
                        this.lokasiMessage = 'Gagal menghubungi server. Periksa koneksi internet.';
                    }
                    this.loading = false;
                },
                (error) => {
                    this.lokasiStatus = 'invalid';
                    this.lokasiMessage = 'Izin lokasi ditolak atau tidak tersedia. Pastikan GPS aktif.';
                    this.loading = false;
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        },

        async startCamera() {
            this.faceStatus = 'loading';
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480, facingMode: 'user' } });
                const video = document.getElementById('video');
                video.srcObject = stream;
                await video.play();

                // Load models jika belum
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
                this.errorMessage = 'Gagal membuka kamera: ' + e.message;
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

                const detections = await faceapi
                    .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 320 }))
                    .withFaceLandmarks(true)
                    .withFaceDescriptors();

                ctx.clearRect(0, 0, canvas.width, canvas.height);

                if (detections.length === 1) {
                    this.faceStatus = 'detected';
                    this.faceDescriptor = Array.from(detections[0].descriptor);

                    // Draw face box
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
                    // Show warning for multiple faces
                    ctx.fillStyle = 'rgba(239, 68, 68, 0.7)';
                    ctx.fillRect(10, canvas.height - 40, canvas.width - 20, 30);
                    ctx.fillStyle = 'white';
                    ctx.font = '14px Inter';
                    ctx.fillText('Hanya satu wajah yang diperbolehkan!', 15, canvas.height - 18);
                }
            }, 500);
        },

        stopCamera() {
            if (this.faceDetectionInterval) {
                clearInterval(this.faceDetectionInterval);
                this.faceDetectionInterval = null;
            }
            const video = document.getElementById('video');
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(t => t.stop());
                video.srcObject = null;
            }
            this.faceStatus = 'detecting';
            this.faceDescriptor = null;
        },

        async rekamAbsen() {
            if (!this.faceDescriptor || this.faceDescriptor.length !== 128) {
                this.errorMessage = 'Deskriptor wajah tidak valid. Pastikan wajah terdeteksi.';
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
                this.errorMessage = 'Terjadi kesalahan koneksi. Coba lagi.';
            }

            this.loading = false;
        }
    }));
});
</script>
@endpush
