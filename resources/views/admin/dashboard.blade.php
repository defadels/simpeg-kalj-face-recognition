<x-app-layout>
    <x-slot name="title">Dashboard Admin</x-slot>
    <x-slot name="breadcrumb">Ikhtisar & Statistik Sistem Kepegawaian</x-slot>

    <!-- Ringkasan Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Total Users & Karyawan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Karyawan</div>
                <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total_karyawan'] }}</div>
                <div class="text-[11px] text-slate-500 mt-0.5">{{ $stats['users_aktif'] }} akun aktif dari {{ $stats['total_users'] }} total user</div>
            </div>
        </div>

        <!-- Hadir Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Hadir Hari Ini</div>
                <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['hadir_hari_ini'] }}</div>
                <div class="text-[11px] text-emerald-600 font-medium mt-0.5">Telah absen masuk</div>
            </div>
        </div>

        <!-- Cuti Pending -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Permohonan Cuti</div>
                <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['cuti_pending'] }}</div>
                <div class="text-[11px] text-amber-600 font-medium mt-0.5">Menunggu persetujuan</div>
            </div>
        </div>

        <!-- Alpha / Belum Absen -->
        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Belum Absen / Alpha</div>
                <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['alpha_hari_ini'] }}</div>
                <div class="text-[11px] text-rose-600 font-medium mt-0.5">Hari ini</div>
            </div>
        </div>
    </div>

    <!-- Quick Access Navigation Grid -->
    <div class="mb-8">
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Navigasi Pengelolaan Administrator</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

            <a href="{{ route('admin.karyawan.index') }}" class="p-4 bg-white border border-slate-100 rounded-xl shadow-sm hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 mx-auto rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="block text-xs font-bold text-slate-700 mt-2">Data Karyawan</span>
            </a>

            <a href="{{ route('admin.jabatan.index') }}" class="p-4 bg-white border border-slate-100 rounded-xl shadow-sm hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 mx-auto rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <span class="block text-xs font-bold text-slate-700 mt-2">Jabatan</span>
            </a>

            <a href="{{ route('admin.divisi.index') }}" class="p-4 bg-white border border-slate-100 rounded-xl shadow-sm hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 mx-auto rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <span class="block text-xs font-bold text-slate-700 mt-2">Divisi</span>
            </a>

            <a href="{{ route('admin.absensi.monitor') }}" class="p-4 bg-white border border-slate-100 rounded-xl shadow-sm hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 mx-auto rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <span class="block text-xs font-bold text-slate-700 mt-2">Monitor Absensi</span>
            </a>

            <a href="{{ route('admin.cuti-izin.index') }}" class="p-4 bg-white border border-slate-100 rounded-xl shadow-sm hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 mx-auto rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="block text-xs font-bold text-slate-700 mt-2">Approval Cuti</span>
            </a>

            <a href="{{ route('admin.konfigurasi.index') }}" class="p-4 bg-white border border-slate-100 rounded-xl shadow-sm hover:shadow-md transition-all text-center group">
                <div class="w-10 h-10 mx-auto rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="block text-xs font-bold text-slate-700 mt-2">Konfigurasi</span>
            </a>
        </div>
    </div>

    <!-- Tabel Data Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Absensi Terbaru Hari Ini -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 text-sm">Absensi Terbaru Hari Ini</h3>
                <a href="{{ route('admin.absensi.monitor') }}" class="text-xs text-blue-600 hover:underline font-semibold">Lihat Semua →</a>
            </div>

            @if($absensiHariIni->isEmpty())
                <div class="text-center py-8 text-slate-400 text-xs font-medium">Belum ada aktivitas absensi hari ini.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-100">
                                <th class="pb-3 font-semibold">Karyawan</th>
                                <th class="pb-3 font-semibold">Masuk</th>
                                <th class="pb-3 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($absensiHariIni as $absen)
                                <tr>
                                    <td class="py-3 font-semibold text-slate-800">
                                        {{ $absen->karyawan->nama_lengkap ?? 'N/A' }}
                                        <span class="block text-[10px] text-slate-400 font-normal">{{ $absen->karyawan->divisi->nama_divisi ?? '-' }}</span>
                                    </td>
                                    <td class="py-3 text-slate-600 font-medium">{{ $absen->waktu_masuk ?? '-' }}</td>
                                    <td class="py-3">
                                        @if($absen->status_kehadiran === 'hadir')
                                            <span class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded-md text-[10px] font-bold">Hadir</span>
                                        @elseif($absen->status_kehadiran === 'terlambat')
                                            <span class="px-2 py-1 bg-amber-50 text-amber-600 rounded-md text-[10px] font-bold">Terlambat</span>
                                        @else
                                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-md text-[10px] font-bold">{{ ucfirst($absen->status_kehadiran) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Cuti Pending Terbaru -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 text-sm">Permohonan Cuti & Izin Pending</h3>
                <a href="{{ route('admin.cuti-izin.index') }}" class="text-xs text-blue-600 hover:underline font-semibold">Proses →</a>
            </div>

            @if($cutiPending->isEmpty())
                <div class="text-center py-8 text-slate-400 text-xs font-medium">Tidak ada permohonan cuti yang perlu diproses.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-100">
                                <th class="pb-3 font-semibold">Karyawan</th>
                                <th class="pb-3 font-semibold">Jenis</th>
                                <th class="pb-3 font-semibold">Durasi</th>
                                <th class="pb-3 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($cutiPending as $cuti)
                                <tr>
                                    <td class="py-3 font-semibold text-slate-800">
                                        {{ $cuti->karyawan->nama_lengkap ?? 'N/A' }}
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $cuti->jenis === 'cuti' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }}">
                                            {{ $cuti->jenis }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-slate-600 font-medium">{{ $cuti->jumlah_hari }} Hari</td>
                                    <td class="py-3">
                                        <a href="{{ route('admin.cuti-izin.show', $cuti->id) }}" class="px-3 py-1 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-semibold rounded-lg">Review</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
