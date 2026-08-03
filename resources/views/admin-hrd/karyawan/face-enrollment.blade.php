<x-app-layout>
    <x-slot name="title">Face Enrollment — {{ $karyawan->nama_lengkap }}</x-slot>
    <x-slot name="breadcrumb">Daftarkan data wajah untuk verifikasi absensi</x-slot>

    <div class="max-w-2xl mx-auto" x-data="faceEnrollApp()" x-init="init()">
        <div class="card">
            <!-- Header -->
            <div class="flex items-center gap-4 mb-6">
                <img src="{{ $karyawan->foto_url }}" class="w-14 h-14 rounded-2xl object-cover" alt="">
                <div>
                    <h3 class="font-semibold text-slate-800">{{ $karyawan->nama_lengkap }}</h3>
                    <p class="text-slate-500 text-sm font-mono font-medium">ID: {{ $karyawan->nip }} • {{ $karyawan->jabatan?->nama_jabatan }}</p>
                    @if($karyawan->face_data)
                        <span class="badge badge-green mt-1">✓ Sudah ada data wajah terdaftar</span>
                    @else
                        <span class="badge badge-red mt-1">✗ Belum ada data wajah</span>
                    @endif
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-slate-100 mb-5">
                <button @click="activeTab = 'webcam'" :class="activeTab === 'webcam' ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500'" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors">📷 Webcam Live</button>
                <button @click="activeTab = 'upload'" :class="activeTab === 'upload' ? 'border-sky-500 text-sky-600' : 'border-transparent text-slate-500'" class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors">📁 Upload Foto</button>
            </div>

            <!-- Webcam Tab -->
            <div x-show="activeTab === 'webcam'" x-transition>
                <p class="text-slate-500 text-sm mb-4">Posisikan wajah karyawan di depan kamera. Sistem akan mengekstrak data deskriptor 128-dimensi dari wajah.</p>

                <div class="relative rounded-xl overflow-hidden bg-black mb-4" style="aspect-ratio: 4/3;">
                    <video id="enroll-video" class="w-full h-full object-cover" autoplay muted playsinline></video>
                    <canvas id="enroll-canvas" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>

                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-48 h-56 border-2 rounded-full" :class="faceDetected ? 'border-emerald-400' : 'border-white/40'"></div>
                    </div>

                    <div class="absolute top-3 left-3 right-3">
                        <div x-show="cameraStatus === 'loading'" class="bg-sky-500/90 text-white px-3 py-1.5 rounded-lg text-sm text-center backdrop-blur-sm">⏳ Memuat model AI...</div>
                        <div x-show="cameraStatus === 'ready' && faceDetected" class="bg-emerald-500/90 text-white px-3 py-1.5 rounded-lg text-sm text-center backdrop-blur-sm">✅ Wajah terdeteksi! Klik "Simpan Data Wajah"</div>
                        <div x-show="cameraStatus === 'ready' && !faceDetected" class="bg-amber-500/90 text-white px-3 py-1.5 rounded-lg text-sm text-center backdrop-blur-sm">🔍 Arahkan wajah ke kamera...</div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button @click="toggleCamera()" class="btn-secondary flex-1" x-text="cameraActive ? 'Matikan Kamera' : 'Nyalakan Kamera'"></button>
                    <button @click="saveFaceDescriptor()" :disabled="!faceDetected || saving"
                        class="btn-primary flex-1">
                        <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9"/></svg>
                        <span x-text="saving ? 'Menyimpan...' : '💾 Simpan Data Wajah'"></span>
                    </button>
                </div>
            </div>

            <!-- Upload Tab -->
            <div x-show="activeTab === 'upload'" x-transition>
                <p class="text-slate-500 text-sm mb-4">Upload foto wajah karyawan. Pastikan wajah jelas, menghadap depan, dan pencahayaan cukup.</p>

                <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center mb-4"
                     x-on:dragover.prevent="dragging = true" x-on:dragleave="dragging = false"
                     :class="dragging ? 'border-sky-400 bg-sky-50' : ''">
                    <input type="file" id="photo-upload" accept="image/*" class="hidden" @change="handlePhotoUpload($event)">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-slate-500 text-sm mb-2">Drag & drop atau</p>
                    <button @click="$refs.photoInput.click()" class="btn-secondary text-sm">Pilih Foto</button>
                    <input type="file" x-ref="photoInput" accept="image/*" class="hidden" @change="handlePhotoUpload($event)">
                    <p class="text-xs text-slate-400 mt-2">JPG, PNG — Maks. 5MB</p>
                </div>

                <!-- Preview & process -->
                <div x-show="uploadedPhoto" class="space-y-4">
                    <div class="relative rounded-xl overflow-hidden" style="max-height: 300px;">
                        <img :src="uploadedPhoto" class="w-full object-contain" alt="Preview">
                        <canvas id="upload-canvas" class="absolute inset-0 w-full h-full pointer-events-none"></canvas>
                    </div>
                    <div x-show="uploadFaceStatus">
                        <div x-show="uploadFaceStatus === 'detected'" class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">✅ Wajah terdeteksi dalam foto</div>
                        <div x-show="uploadFaceStatus === 'not-found'" class="p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">❌ Wajah tidak ditemukan dalam foto. Upload foto lain.</div>
                        <div x-show="uploadFaceStatus === 'multiple'" class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-700 text-sm">⚠️ Ditemukan lebih dari satu wajah. Gunakan foto dengan satu wajah saja.</div>
                    </div>
                    <button @click="saveFaceDescriptor()" :disabled="uploadFaceStatus !== 'detected' || saving" class="btn-primary w-full justify-center">
                        <span x-text="saving ? 'Menyimpan...' : '💾 Simpan Data Wajah'"></span>
                    </button>
                </div>
            </div>

            <!-- Result Message -->
            <div x-show="resultMessage" x-transition class="mt-4 p-4 rounded-xl" :class="resultSuccess ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : 'bg-red-50 border border-red-200 text-red-700'">
                <p class="text-sm font-medium" x-text="resultMessage"></p>
                <div x-show="resultSuccess" class="mt-3 flex gap-3">
                    <a href="{{ route('admin-hrd.karyawan.index') }}" class="btn-secondary text-sm">Kembali ke Daftar</a>
                    <a href="{{ route('admin-hrd.karyawan.show', $karyawan) }}" class="btn-primary text-sm">Lihat Detail Karyawan</a>
                </div>
            </div>
        </div>    </div>

@push('scripts')
<script>
const MODELS_URL = '/face-models';
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const STORE_URL = '{{ route('admin-hrd.karyawan.face-descriptor', $karyawan) }}';

// Menyimpan object faceapi setelah dynamic import selesai
let faceapiInstance = null;

async function getFaceApi() {
    if (!faceapiInstance) {
        faceapiInstance = await import('/js/face-api.esm.js');
    }
    return faceapiInstance;
}

const registerFaceEnrollApp = () => {
    if (window.Alpine) {
        window.Alpine.data('faceEnrollApp', () => ({
            activeTab: 'webcam',
            cameraActive: false,
            cameraStatus: 'loading',
            faceDetected: false,
            faceDescriptor: null,
            saving: false,
            resultMessage: '',
            resultSuccess: false,
            dragging: false,
            uploadedPhoto: null,
            uploadFaceStatus: null,
            detectionInterval: null,
            modelsLoaded: false,

            async init() {
                try {
                    const faceapi = await getFaceApi();
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri(MODELS_URL),
                        faceapi.nets.ssdMobilenetv1.loadFromUri(MODELS_URL),
                        faceapi.nets.faceLandmark68Net.loadFromUri(MODELS_URL),
                        faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODELS_URL),
                        faceapi.nets.faceRecognitionNet.loadFromUri(MODELS_URL),
                    ]);
                    this.modelsLoaded = true;
                    this.cameraStatus = 'ready';
                    console.log('All face models loaded successfully for enrollment');
                } catch(e) {
                    console.warn('Models load failed:', e);
                    this.cameraStatus = 'error';
                }
            },

            async toggleCamera() {
                if (this.cameraActive) {
                    this.stopCamera();
                } else {
                    await this.startCamera();
                }
            },

            async startCamera() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480, facingMode: 'user' } });
                    const video = document.getElementById('enroll-video');
                    video.srcObject = stream;
                    await video.play();
                    this.cameraActive = true;
                    this.cameraStatus = 'ready';
                    this.startDetection();
                } catch(e) {
                    this.resultMessage = 'Gagal membuka kamera: ' + e.message;
                }
            },

            stopCamera() {
                if (this.detectionInterval) {
                    clearInterval(this.detectionInterval);
                    this.detectionInterval = null;
                }
                const video = document.getElementById('enroll-video');
                if (video && video.srcObject) {
                    video.srcObject.getTracks().forEach(t => t.stop());
                    video.srcObject = null;
                }
                this.cameraActive = false;
                this.faceDetected = false;
                this.faceDescriptor = null;
            },

            async startDetection() {
                const video = document.getElementById('enroll-video');
                const canvas = document.getElementById('enroll-canvas');
                const ctx = canvas.getContext('2d');
                const faceapi = await getFaceApi();

                this.detectionInterval = setInterval(async () => {
                    try {
                        if (!video || !video.srcObject || video.paused || video.ended) return;
                        canvas.width = video.videoWidth || 640;
                        canvas.height = video.videoHeight || 480;
                        ctx.clearRect(0, 0, canvas.width, canvas.height);

                        let detections = await faceapi
                            .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.25 }))
                            .withFaceLandmarks(true)
                            .withFaceDescriptors();

                        if (detections.length === 0 && faceapi.nets.ssdMobilenetv1 && faceapi.nets.ssdMobilenetv1.isLoaded) {
                            detections = await faceapi
                                .detectAllFaces(video, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.3 }))
                                .withFaceLandmarks(true)
                                .withFaceDescriptors();
                        }

                        if (detections.length === 1) {
                            this.faceDetected = true;
                            this.faceDescriptor = Array.from(detections[0].descriptor);
                            this.faceLandmarks = detections[0].landmarks.positions.map(p => ({ x: Math.round(p.x * 10) / 10, y: Math.round(p.y * 10) / 10 }));

                            const box = detections[0].detection.box;
                            ctx.strokeStyle = '#10b981';
                            ctx.lineWidth = 3;
                            ctx.strokeRect(box.x, box.y, box.width, box.height);
                        } else {
                            this.faceDetected = false;
                            this.faceDescriptor = null;
                            this.faceLandmarks = null;
                        }
                    } catch(err) {
                        console.error('Detection error:', err);
                    }
                }, 300);
            },

            async handlePhotoUpload(event) {
                const file = event.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = async (e) => {
                    this.uploadedPhoto = e.target.result;
                    await this.$nextTick();
                    await this.processUploadedPhoto(e.target.result);
                };
                reader.readAsDataURL(file);
            },

            async processUploadedPhoto(src) {
                const img = new Image();
                img.src = src;
                await new Promise(r => img.onload = r);

                if (!this.modelsLoaded) {
                    await this.init();
                }

                const faceapi = await getFaceApi();
                let detections = await faceapi
                    .detectAllFaces(img, new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.25 }))
                    .withFaceLandmarks(true)
                    .withFaceDescriptors();

                if (detections.length === 0 && faceapi.nets.ssdMobilenetv1 && faceapi.nets.ssdMobilenetv1.isLoaded) {
                    detections = await faceapi
                        .detectAllFaces(img, new faceapi.SsdMobilenetv1Options({ minConfidence: 0.3 }))
                        .withFaceLandmarks(true)
                        .withFaceDescriptors();
                }

                if (detections.length === 0) {
                    this.uploadFaceStatus = 'not-found';
                    this.faceDescriptor = null;
                    this.faceLandmarks = null;
                } else if (detections.length > 1) {
                    this.uploadFaceStatus = 'multiple';
                    this.faceDescriptor = null;
                    this.faceLandmarks = null;
                } else {
                    this.uploadFaceStatus = 'detected';
                    this.faceDescriptor = Array.from(detections[0].descriptor);
                    this.faceLandmarks = detections[0].landmarks.positions.map(p => ({ x: Math.round(p.x * 10) / 10, y: Math.round(p.y * 10) / 10 }));
                }
            },

            async saveFaceDescriptor() {
                if (!this.faceDescriptor || this.faceDescriptor.length !== 128) {
                    this.resultMessage = 'Data deskriptor tidak valid.';
                    return;
                }

                let imageBase64 = null;
                if (this.activeTab === 'webcam') {
                    const video = document.getElementById('enroll-video');
                    if (video && video.videoWidth > 0) {
                        const snapCanvas = document.createElement('canvas');
                        snapCanvas.width = video.videoWidth;
                        snapCanvas.height = video.videoHeight;
                        const snapCtx = snapCanvas.getContext('2d');
                        snapCtx.drawImage(video, 0, 0, snapCanvas.width, snapCanvas.height);
                        imageBase64 = snapCanvas.toDataURL('image/jpeg', 0.85);
                    }
                } else if (this.activeTab === 'upload') {
                    imageBase64 = this.uploadedPhoto;
                }

                this.saving = true;
                try {
                    const response = await fetch(STORE_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                        body: JSON.stringify({
                            face_descriptor: this.faceDescriptor,
                            face_landmarks: this.faceLandmarks,
                            image: imageBase64,
                        }),
                    });

                    const data = await response.json();
                    this.resultSuccess = data.success;
                    this.resultMessage = data.message;

                    if (data.success) {
                        this.stopCamera();
                    }
                } catch(e) {
                    this.resultMessage = 'Gagal menghubungi server.';
                    this.resultSuccess = false;
                }
                this.saving = false;
            }
        }));
    }
};

if (window.Alpine) {
    registerFaceEnrollApp();
} else {
    document.addEventListener('alpine:init', registerFaceEnrollApp);
}
</script>
@endpush
</x-app-layout>
