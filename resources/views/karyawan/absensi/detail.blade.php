<x-app-layout>
    <x-slot name="title">Detail Absensi Saya</x-slot>
    <x-slot name="breadcrumb">Informasi detail rekam kehadiran, verifikasi wajah, dan titik lokasi GPS</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Navigation Header --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('karyawan.absensi.riwayat') }}" class="btn-secondary text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Riwayat
            </a>
            <div class="text-xs text-slate-400 font-mono">{{ $absensi->tanggal->isoFormat('dddd, D MMMM Y') }}</div>
        </div>

        {{-- Attendance Summary Banner --}}
        <div class="card bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white shadow-xl relative overflow-hidden border-0">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center border border-white/20 flex-shrink-0">
                        <svg class="w-7 h-7 text-sky-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-bold text-white">Kehadiran {{ $absensi->tanggal->isoFormat('D MMMM Y') }}</h2>
                            @php $badge = $absensi->status_badge; @endphp
                            <span class="badge badge-{{ $badge['color'] }} px-2.5 py-0.5 text-xs font-semibold uppercase">
                                {{ $badge['label'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-300 mt-1">
                            Divisi {{ $absensi->karyawan->divisi?->nama_divisi ?? '-' }} • Jam Masuk: {{ $absensi->karyawan->divisi?->jam_kerja_formatted ?? '-' }}
                        </p>
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
                        <span class="text-[10px] text-slate-300 uppercase tracking-wider font-semibold block">Jam Kerja</span>
                        <span class="text-base font-extrabold font-mono text-amber-300">{{ $absensi->jam_kerja ? $absensi->jam_kerja . 'h' : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Face Verification Section --}}
        <div class="card space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#0056B3]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Hasil Verifikasi Wajah AI
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Komparasi foto terdaftar Anda dengan foto snapshot webcam saat melakukan absensi</p>
                </div>
                <div>
                    <span class="badge {{ $absensi->status_face === 'berhasil' ? 'badge-green' : 'badge-red' }} text-xs font-bold px-3 py-1">
                        {{ $absensi->status_face === 'berhasil' ? '✓ Wajah Cocok' : '✗ Wajah Tidak Cocok' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Card 1: Foto Enrollment --}}
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 flex flex-col items-center text-center space-y-3">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-sky-100 text-[#0056B3] rounded-lg text-[11px] font-bold uppercase tracking-wider">
                        📷 Wajah Terdaftar (Enrollment)
                    </div>
                    <div class="relative w-44 h-44 rounded-2xl overflow-hidden shadow-inner ring-4 ring-sky-200/60 bg-slate-200">
                        <img src="{{ $absensi->karyawan->foto_url }}" class="w-full h-full object-cover" alt="Foto Master">
                        <div class="absolute bottom-0 inset-x-0 bg-slate-900/70 backdrop-blur-xs text-white text-[10px] py-1 font-mono">
                            Face Enrollment Master
                        </div>
                    </div>
                    <div class="text-xs text-slate-600">
                        <p class="font-semibold text-slate-800">{{ $absensi->karyawan->nama_lengkap }}</p>
                        <p class="text-[11px] text-slate-400">NIP: {{ $absensi->karyawan->nip }}</p>
                    </div>
                </div>

                {{-- Card 2: Foto Absen Masuk --}}
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
                            <img src="{{ $absensi->karyawan->foto_url }}" class="w-full h-full object-cover filter brightness-95" alt="Foto Karyawan">
                            <div class="absolute bottom-0 inset-x-0 bg-slate-900/80 backdrop-blur-xs text-amber-300 text-[10px] py-1 font-mono">
                                Ref. Enrollment (Data Terdahulu)
                            </div>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100 text-slate-400 p-4">
                                <svg class="w-10 h-10 mb-1 opacity-60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316A2.192 2.192 0 0014.482 4H9.518c-.742 0-1.42.37-1.83.991l-.861 1.184z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-[11px] font-semibold">Belum Absen Masuk</span>
                            </div>
                        @endif
                    </div>

                    <div class="w-full text-xs space-y-2">
                        @if($absensi->similarity_masuk !== null)
                            <div>
                                <div class="flex justify-between font-semibold text-[11px] mb-1">
                                    <span class="text-slate-500">Tingkat Kemiripan:</span>
                                    <span class="text-emerald-600 font-mono font-bold">{{ $absensi->similarity_masuk }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ min(100, max(0, $absensi->similarity_masuk)) }}%"></div>
                                </div>
                            </div>
                        @else
                            <div class="text-xs text-slate-400 italic py-1">
                                {{ $absensi->waktu_masuk ? 'Wajah Terverifikasi Valid (✓)' : 'Belum ada data absensi' }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card 3: Foto Absen Keluar --}}
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
                            <img src="{{ $absensi->karyawan->foto_url }}" class="w-full h-full object-cover filter brightness-95" alt="Foto Karyawan">
                            <div class="absolute bottom-0 inset-x-0 bg-slate-900/80 backdrop-blur-xs text-amber-300 text-[10px] py-1 font-mono">
                                Ref. Enrollment (Data Terdahulu)
                            </div>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100 text-slate-400 p-4">
                                <svg class="w-10 h-10 mb-1 opacity-60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316A2.192 2.192 0 0014.482 4H9.518c-.742 0-1.42.37-1.83.991l-.861 1.184z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-[11px] font-semibold">Belum Absen Keluar</span>
                            </div>
                        @endif
                    </div>

                    <div class="w-full text-xs space-y-2">
                        @if($absensi->similarity_keluar !== null)
                            <div>
                                <div class="flex justify-between font-semibold text-[11px] mb-1">
                                    <span class="text-slate-500">Tingkat Kemiripan:</span>
                                    <span class="text-sky-600 font-mono font-bold">{{ $absensi->similarity_keluar }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-sky-500 h-2 rounded-full" style="width: {{ min(100, max(0, $absensi->similarity_keluar)) }}%"></div>
                                </div>
                            </div>
                        @else
                            <div class="text-xs text-slate-400 italic py-1">
                                {{ $absensi->waktu_keluar ? 'Wajah Terverifikasi Valid (✓)' : 'Belum absen keluar' }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- GPS Location Details Section --}}
        <div class="card space-y-4">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Detail Lokasi Absensi (GPS)
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Lokasi Masuk --}}
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-xs font-bold text-slate-700 uppercase">📍 Lokasi Absen Masuk</span>
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
                                    Jarak ke Kantor: <strong class="text-[#0056B3] font-mono font-bold">{{ $jarakMasuk }} meter</strong>
                                </div>
                            @endif
                        </div>
                        <a href="https://www.google.com/maps?q={{ $absensi->lat_masuk }},{{ $absensi->lng_masuk }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs text-sky-600 hover:text-sky-800 font-semibold hover:underline">
                            🗺️ Buka Peta Google Maps →
                        </a>
                    @else
                        <p class="text-xs text-slate-400 italic">Koordinat GPS absen masuk tidak terekam.</p>
                    @endif
                </div>

                {{-- Lokasi Keluar --}}
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-xs font-bold text-slate-700 uppercase">📍 Lokasi Absen Keluar</span>
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
                                    Jarak ke Kantor: <strong class="text-[#0056B3] font-mono font-bold">{{ $jarakKeluar }} meter</strong>
                                </div>
                            @endif
                        </div>
                        <a href="https://www.google.com/maps?q={{ $absensi->lat_keluar }},{{ $absensi->lng_keluar }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs text-sky-600 hover:text-sky-800 font-semibold hover:underline">
                            🗺️ Buka Peta Google Maps →
                        </a>
                    @else
                        <p class="text-xs text-slate-400 italic">Koordinat GPS absen keluar tidak terekam.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
