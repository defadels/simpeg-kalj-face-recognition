<x-app-layout>
    <x-slot name="title">Detail Verifikasi Absensi & Activity Log</x-slot>
    <x-slot name="breadcrumb">Analisis kecocokan foto absensi dengan face enrollment & riwayat login karyawan</x-slot>

    <div class="space-y-6">
        {{-- Navigation Header --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.absensi.monitor') }}" class="btn-secondary text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Monitoring
            </a>
            <div class="text-xs text-slate-400 font-mono">ID Absensi: #{{ $absensi->id }}</div>
        </div>

        {{-- Employee Header Card --}}
        <div class="card bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white shadow-xl relative overflow-hidden border-0">
            <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
                <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            </div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div class="flex items-center gap-4">
                    <img src="{{ $absensi->karyawan->foto_url }}" class="w-16 h-16 rounded-2xl object-cover ring-4 ring-white/20 shadow-md flex-shrink-0" alt="">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-bold tracking-tight text-white">{{ $absensi->karyawan->nama_lengkap }}</h2>
                            @php $badge = $absensi->status_badge; @endphp
                            <span class="badge badge-{{ $badge['color'] }} px-2.5 py-0.5 text-xs font-semibold uppercase">
                                {{ $badge['label'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-300 font-mono mt-1">NIP: {{ $absensi->karyawan->nip }} • {{ $absensi->karyawan->divisi?->nama_divisi ?? 'Tanpa Divisi' }} ({{ $absensi->karyawan->jabatan?->nama_jabatan ?? '-' }})</p>
                        <p class="text-xs text-slate-400 mt-0.5">Tanggal Absensi: <strong class="text-slate-200 font-semibold">{{ $absensi->tanggal->isoFormat('dddd, D MMMM Y') }}</strong></p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 bg-white/10 p-3 rounded-2xl backdrop-blur-md border border-white/10 text-center">
                    <div>
                        <span class="text-[10px] text-slate-300 uppercase tracking-wider font-semibold block">Absen Masuk</span>
                        <span class="text-base font-extrabold font-mono text-emerald-400">{{ $absensi->waktu_masuk ? substr($absensi->waktu_masuk, 0, 5) : '--:--' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-300 uppercase tracking-wider font-semibold block">Absen Keluar</span>
                        <span class="text-base font-extrabold font-mono text-sky-400">{{ $absensi->waktu_keluar ? substr($absensi->waktu_keluar, 0, 5) : '--:--' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-300 uppercase tracking-wider font-semibold block">Total Jam Kerja</span>
                        <span class="text-base font-extrabold font-mono text-amber-300">{{ $absensi->jam_kerja ? $absensi->jam_kerja . 'h' : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Face Verification & Photo Match Comparison Section --}}
        <div class="card space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#0056B3]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Verifikasi AI & Komparasi Visual Wajah
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Membandingkan foto terdaftar pada Face Enrollment dengan foto snapshot absensi saat rekam masuk / keluar</p>
                </div>
                <div>
                    <span class="badge {{ $absensi->status_face === 'berhasil' ? 'badge-green' : 'badge-red' }} text-xs font-bold px-3 py-1">
                        {{ $absensi->status_face === 'berhasil' ? '✓ AI Face Match Valid' : '✗ AI Face Match Gagal' }}
                    </span>
                </div>
            </div>

            {{-- Photos Side-by-Side Comparison --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Card 1: Registered Face Enrollment Photo --}}
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 flex flex-col items-center text-center space-y-3">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-sky-100 text-[#0056B3] rounded-lg text-[11px] font-bold uppercase tracking-wider">
                        📷 Foto Face Enrollment
                    </div>
                    <div class="relative w-44 h-44 rounded-2xl overflow-hidden shadow-inner ring-4 ring-sky-200/60 bg-slate-200">
                        <img src="{{ $absensi->karyawan->foto_enrollment_url ?? $absensi->karyawan->foto_url }}" class="w-full h-full object-cover" alt="Foto Enrollment">
                        <div class="absolute bottom-0 inset-x-0 bg-slate-900/70 backdrop-blur-xs text-white text-[10px] py-1 font-mono">
                            Master Enrollment
                        </div>
                    </div>
                    <div class="text-xs space-y-1 text-slate-600">
                        <p class="font-semibold text-slate-800">{{ $absensi->karyawan->nama_lengkap }}</p>
                        <p class="text-[11px]">Descriptor Status: <span class="font-semibold {{ $absensi->karyawan->face_data ? 'text-emerald-600' : 'text-amber-600' }}">{{ $absensi->karyawan->face_data ? 'Terdaftar (128-d Vector)' : 'Belum Terdaftar' }}</span></p>
                    </div>
                </div>

                {{-- Card 2: Attendance Check-in Snapshot Photo --}}
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 flex flex-col items-center text-center space-y-3">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-lg text-[11px] font-bold uppercase tracking-wider">
                        🟢 Snapshot Absen Masuk
                    </div>
                    <div class="relative w-44 h-44 rounded-2xl overflow-hidden shadow-inner ring-4 ring-emerald-200/60 bg-slate-200">
                        @if($absensi->foto_masuk_url)
                            <img src="{{ $absensi->foto_masuk_url }}" class="w-full h-full object-cover" alt="Foto Absen Masuk">
                            <div class="absolute bottom-0 inset-x-0 bg-slate-900/70 backdrop-blur-xs text-white text-[10px] py-1 font-mono">
                                Webcam Snapshot • {{ substr($absensi->waktu_masuk, 0, 5) }} WIB
                            </div>
                        @elseif($absensi->waktu_masuk)
                            <img src="{{ $absensi->karyawan->foto_enrollment_url ?? $absensi->karyawan->foto_url }}" class="w-full h-full object-cover filter brightness-95" alt="Foto Karyawan">
                            <div class="absolute bottom-0 inset-x-0 bg-slate-900/80 backdrop-blur-xs text-amber-300 text-[10px] py-1 font-mono">
                                Ref. Enrollment (Data Lama)
                            </div>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100 text-slate-400 p-4">
                                <svg class="w-10 h-10 mb-1 opacity-60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316A2.192 2.192 0 0014.482 4H9.518c-.742 0-1.42.37-1.83.991l-.861 1.184z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-[11px] font-semibold">Belum Absen Masuk</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="w-full text-xs space-y-2">
                        @if($absensi->face_distance_masuk !== null || $absensi->similarity_masuk !== null)
                            <div>
                                <div class="flex justify-between font-semibold text-[11px] mb-1">
                                    <span class="text-slate-500">Tingkat Kemiripan:</span>
                                    <span class="text-emerald-600 font-mono font-bold">{{ $absensi->similarity_masuk }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min(100, max(0, $absensi->similarity_masuk)) }}%"></div>
                                </div>
                            </div>
                            <div class="text-[11px] text-slate-500 font-mono text-center">
                                Jarak Euclidean: <strong class="text-slate-700">{{ $absensi->face_distance_masuk }}</strong> (Max: 0.60)
                            </div>
                        @else
                            <div class="text-xs text-slate-400 italic py-1">
                                {{ $absensi->waktu_masuk ? 'Verifikasi wajah terkonfirmasi valid (✓)' : 'Belum ada data absensi' }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card 3: Attendance Check-out Snapshot Photo --}}
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 flex flex-col items-center text-center space-y-3">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-sky-100 text-sky-800 rounded-lg text-[11px] font-bold uppercase tracking-wider">
                        🔵 Snapshot Absen Keluar
                    </div>
                    <div class="relative w-44 h-44 rounded-2xl overflow-hidden shadow-inner ring-4 ring-sky-200/60 bg-slate-200">
                        @if($absensi->foto_keluar_url)
                            <img src="{{ $absensi->foto_keluar_url }}" class="w-full h-full object-cover" alt="Foto Absen Keluar">
                            <div class="absolute bottom-0 inset-x-0 bg-slate-900/70 backdrop-blur-xs text-white text-[10px] py-1 font-mono">
                                Webcam Snapshot • {{ substr($absensi->waktu_keluar, 0, 5) }} WIB
                            </div>
                        @elseif($absensi->waktu_keluar)
                            <img src="{{ $absensi->karyawan->foto_enrollment_url ?? $absensi->karyawan->foto_url }}" class="w-full h-full object-cover filter brightness-95" alt="Foto Karyawan">
                            <div class="absolute bottom-0 inset-x-0 bg-slate-900/80 backdrop-blur-xs text-amber-300 text-[10px] py-1 font-mono">
                                Ref. Enrollment (Data Lama)
                            </div>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100 text-slate-400 p-4">
                                <svg class="w-10 h-10 mb-1 opacity-60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316A2.192 2.192 0 0014.482 4H9.518c-.742 0-1.42.37-1.83.991l-.861 1.184z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-[11px] font-semibold">Belum Absen Keluar</span>
                            </div>
                        @endif
                    </div>

                    <div class="w-full text-xs space-y-2">
                        @if($absensi->face_distance_keluar !== null || $absensi->similarity_keluar !== null)
                            <div>
                                <div class="flex justify-between font-semibold text-[11px] mb-1">
                                    <span class="text-slate-500">Tingkat Kemiripan:</span>
                                    <span class="text-sky-600 font-mono font-bold">{{ $absensi->similarity_keluar }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-sky-500 h-2 rounded-full" style="width: {{ min(100, max(0, $absensi->similarity_keluar)) }}%"></div>
                                </div>
                            </div>
                            <div class="text-[11px] text-slate-500 font-mono text-center">
                                Jarak Euclidean: <strong class="text-slate-700">{{ $absensi->face_distance_keluar }}</strong> (Max: 0.60)
                            </div>
                        @else
                            <div class="text-xs text-slate-400 italic py-1">
                                {{ $absensi->waktu_keluar ? 'Verifikasi wajah terkonfirmasi valid (✓)' : 'Karyawan belum absen keluar' }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- Granular Facial Component Similarity Breakdown Section --}}
        @php
            $detailMasuk = $absensi->detail_masuk_formatted;
            $detailKeluar = $absensi->detail_keluar_formatted;
        @endphp

        @if($detailMasuk || $detailKeluar)
            <div class="card space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Analisis Akurat Kemiripan Fitur Wajah (Facial Feature Match Breakdown)
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Rincian analisis persentase kemiripan struktur geometris landmark 68-titik wajah (Mata, Alis, Hidung, Mulut, Rahang)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Detail Absen Masuk --}}
                    @if($detailMasuk)
                        <div class="bg-slate-50/80 border border-slate-200/90 rounded-2xl p-5 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    Analisis Wajah Absen Masuk
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold font-mono bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Total Match: {{ $detailMasuk['overall'] ?? 0 }}%
                                </span>
                            </div>

                            <div class="space-y-3 text-xs">
                                {{-- 👁️ Mata --}}
                                <div>
                                    <div class="flex justify-between font-semibold mb-1">
                                        <span class="text-slate-600 flex items-center gap-1.5">👁️ <strong>Mata (Eyes)</strong></span>
                                        <span class="font-mono text-emerald-700 font-bold">{{ $detailMasuk['mata'] ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden shadow-inner">
                                        <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $detailMasuk['mata'] ?? 0)) }}%"></div>
                                    </div>
                                </div>

                                {{-- 🤨 Alis --}}
                                <div>
                                    <div class="flex justify-between font-semibold mb-1">
                                        <span class="text-slate-600 flex items-center gap-1.5">🤨 <strong>Alis (Eyebrows)</strong></span>
                                        <span class="font-mono text-emerald-700 font-bold">{{ $detailMasuk['alis'] ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden shadow-inner">
                                        <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $detailMasuk['alis'] ?? 0)) }}%"></div>
                                    </div>
                                </div>

                                {{-- 👃 Hidung --}}
                                <div>
                                    <div class="flex justify-between font-semibold mb-1">
                                        <span class="text-slate-600 flex items-center gap-1.5">👃 <strong>Hidung (Nose Bridge & Tip)</strong></span>
                                        <span class="font-mono text-emerald-700 font-bold">{{ $detailMasuk['hidung'] ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden shadow-inner">
                                        <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $detailMasuk['hidung'] ?? 0)) }}%"></div>
                                    </div>
                                </div>

                                {{-- 👄 Mulut --}}
                                <div>
                                    <div class="flex justify-between font-semibold mb-1">
                                        <span class="text-slate-600 flex items-center gap-1.5">👄 <strong>Mulut & Bibir (Mouth)</strong></span>
                                        <span class="font-mono text-emerald-700 font-bold">{{ $detailMasuk['mulut'] ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden shadow-inner">
                                        <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $detailMasuk['mulut'] ?? 0)) }}%"></div>
                                    </div>
                                </div>

                                {{-- 👤 Rahang --}}
                                <div>
                                    <div class="flex justify-between font-semibold mb-1">
                                        <span class="text-slate-600 flex items-center gap-1.5">👤 <strong>Rahang & Kontur Wajah (Jawline)</strong></span>
                                        <span class="font-mono text-emerald-700 font-bold">{{ $detailMasuk['rahang'] ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden shadow-inner">
                                        <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $detailMasuk['rahang'] ?? 0)) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Detail Absen Keluar --}}
                    @if($detailKeluar)
                        <div class="bg-slate-50/80 border border-slate-200/90 rounded-2xl p-5 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                                    Analisis Wajah Absen Keluar
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold font-mono bg-sky-100 text-sky-800 border border-sky-200">
                                    Total Match: {{ $detailKeluar['overall'] ?? 0 }}%
                                </span>
                            </div>

                            <div class="space-y-3 text-xs">
                                {{-- 👁️ Mata --}}
                                <div>
                                    <div class="flex justify-between font-semibold mb-1">
                                        <span class="text-slate-600 flex items-center gap-1.5">👁️ <strong>Mata (Eyes)</strong></span>
                                        <span class="font-mono text-sky-700 font-bold">{{ $detailKeluar['mata'] ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden shadow-inner">
                                        <div class="bg-gradient-to-r from-sky-500 to-indigo-400 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $detailKeluar['mata'] ?? 0)) }}%"></div>
                                    </div>
                                </div>

                                {{-- 🤨 Alis --}}
                                <div>
                                    <div class="flex justify-between font-semibold mb-1">
                                        <span class="text-slate-600 flex items-center gap-1.5">🤨 <strong>Alis (Eyebrows)</strong></span>
                                        <span class="font-mono text-sky-700 font-bold">{{ $detailKeluar['alis'] ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden shadow-inner">
                                        <div class="bg-gradient-to-r from-sky-500 to-indigo-400 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $detailKeluar['alis'] ?? 0)) }}%"></div>
                                    </div>
                                </div>

                                {{-- 👃 Hidung --}}
                                <div>
                                    <div class="flex justify-between font-semibold mb-1">
                                        <span class="text-slate-600 flex items-center gap-1.5">👃 <strong>Hidung (Nose Bridge & Tip)</strong></span>
                                        <span class="font-mono text-sky-700 font-bold">{{ $detailKeluar['hidung'] ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden shadow-inner">
                                        <div class="bg-gradient-to-r from-sky-500 to-indigo-400 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $detailKeluar['hidung'] ?? 0)) }}%"></div>
                                    </div>
                                </div>

                                {{-- 👄 Mulut --}}
                                <div>
                                    <div class="flex justify-between font-semibold mb-1">
                                        <span class="text-slate-600 flex items-center gap-1.5">👄 <strong>Mulut & Bibir (Mouth)</strong></span>
                                        <span class="font-mono text-sky-700 font-bold">{{ $detailKeluar['mulut'] ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden shadow-inner">
                                        <div class="bg-gradient-to-r from-sky-500 to-indigo-400 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $detailKeluar['mulut'] ?? 0)) }}%"></div>
                                    </div>
                                </div>

                                {{-- 👤 Rahang --}}
                                <div>
                                    <div class="flex justify-between font-semibold mb-1">
                                        <span class="text-slate-600 flex items-center gap-1.5">👤 <strong>Rahang & Kontur Wajah (Jawline)</strong></span>
                                        <span class="font-mono text-sky-700 font-bold">{{ $detailKeluar['rahang'] ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden shadow-inner">
                                        <div class="bg-gradient-to-r from-sky-500 to-indigo-400 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, $detailKeluar['rahang'] ?? 0)) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- GPS Location Details --}}
        <div class="card">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Validasi GPS Lokasi Absensi
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Lokasi Masuk --}}
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-xs font-bold text-slate-700 uppercase">📍 Titik Lokasi Absen Masuk</span>
                        <span class="badge {{ $absensi->status_lokasi === 'valid' ? 'badge-green' : 'badge-red' }} text-[11px]">
                            {{ ucfirst($absensi->status_lokasi ?? 'N/A') }}
                        </span>
                    </div>

                    @if($absensi->lat_masuk && $absensi->lng_masuk)
                        <div class="text-xs space-y-1 font-mono text-slate-600">
                            <div>Latitude: <strong class="text-slate-800">{{ $absensi->lat_masuk }}</strong></div>
                            <div>Longitude: <strong class="text-slate-800">{{ $absensi->lng_masuk }}</strong></div>
                            @if($jarakMasuk !== null)
                                <div class="text-[11px] text-slate-500 font-sans mt-1">
                                    Jarak dari Kantor: <strong class="text-[#0056B3] font-mono font-bold">{{ $jarakMasuk }} meter</strong> (Batas Radius: {{ $konfigurasi->radius_meter ?? 100 }}m)
                                </div>
                            @endif
                        </div>
                        <a href="https://www.google.com/maps?q={{ $absensi->lat_masuk }},{{ $absensi->lng_masuk }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs text-sky-600 hover:text-sky-800 font-semibold hover:underline">
                            🗺️ Buka di Google Maps →
                        </a>
                    @else
                        <p class="text-xs text-slate-400 italic">Data koordinat GPS absen masuk belum tersedia.</p>
                    @endif
                </div>

                {{-- Lokasi Keluar --}}
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-xs font-bold text-slate-700 uppercase">📍 Titik Lokasi Absen Keluar</span>
                        <span class="badge {{ $absensi->waktu_keluar ? 'badge-blue' : 'badge-gray' }} text-[11px]">
                            {{ $absensi->waktu_keluar ? 'Terekam' : 'Belum Absen' }}
                        </span>
                    </div>

                    @if($absensi->lat_keluar && $absensi->lng_keluar)
                        <div class="text-xs space-y-1 font-mono text-slate-600">
                            <div>Latitude: <strong class="text-slate-800">{{ $absensi->lat_keluar }}</strong></div>
                            <div>Longitude: <strong class="text-slate-800">{{ $absensi->lng_keluar }}</strong></div>
                            @if($jarakKeluar !== null)
                                <div class="text-[11px] text-slate-500 font-sans mt-1">
                                    Jarak dari Kantor: <strong class="text-[#0056B3] font-mono font-bold">{{ $jarakKeluar }} meter</strong> (Batas Radius: {{ $konfigurasi->radius_meter ?? 100 }}m)
                                </div>
                            @endif
                        </div>
                        <a href="https://www.google.com/maps?q={{ $absensi->lat_keluar }},{{ $absensi->lng_keluar }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs text-sky-600 hover:text-sky-800 font-semibold hover:underline">
                            🗺️ Buka di Google Maps →
                        </a>
                    @else
                        <p class="text-xs text-slate-400 italic">Data koordinat GPS absen keluar belum tersedia.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Employee Login Activity Section --}}
        <div class="card space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-3 gap-2">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Riwayat Aktivitas Login Karyawan
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan log otentikasi login akun milik <strong>{{ $absensi->karyawan->nama_lengkap }}</strong> ({{ $absensi->karyawan->user?->email ?? '-' }})</p>
                </div>
                <div class="text-xs text-slate-500">
                    Total Log Terekam: <span class="font-bold text-slate-800 font-mono">{{ count($loginLogs) }} sesi</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-500 text-xs font-semibold uppercase tracking-wider">
                            <th class="text-left py-2.5 px-3">Waktu Login</th>
                            <th class="text-left py-2.5 px-3">Alamat IP</th>
                            <th class="text-left py-2.5 px-3">Perangkat / Browser</th>
                            <th class="text-left py-2.5 px-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loginLogs as $log)
                            <tr class="table-row">
                                <td class="py-2.5 px-3 font-mono text-xs text-slate-800 font-semibold">
                                    {{ $log->logged_at ? $log->logged_at->isoFormat('D MMM Y, HH:mm:ss') . ' WIB' : '-' }}
                                </td>
                                <td class="py-2.5 px-3 font-mono text-xs text-slate-600">
                                    <span class="inline-block px-2 py-0.5 bg-slate-100 rounded text-slate-700">{{ $log->ip_address ?? '127.0.0.1' }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-xs text-slate-700">
                                    <div class="font-semibold">{{ $log->device_formatted }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono truncate max-w-xs" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent }}
                                    </div>
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="badge badge-green text-[11px]">✓ Berhasil Login</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400 text-xs">
                                    Belum ada catatan aktivitas login tersimpan untuk akun ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
