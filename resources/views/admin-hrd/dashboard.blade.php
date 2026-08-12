<x-app-layout>
    <x-slot name="title">Dashboard Admin HRD</x-slot>

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #0ea5e9, #0284c7)">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-4 -mt-4"></div>
            <div class="relative"><div class="text-sky-100 text-xs mb-1">Karyawan Aktif</div><div class="text-3xl font-bold">{{ $stats['total_karyawan'] }}</div></div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669)">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-4 -mt-4"></div>
            <div class="relative"><div class="text-emerald-100 text-xs mb-1">Hadir Hari Ini</div><div class="text-3xl font-bold">{{ $stats['hadir_hari_ini'] }}</div></div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706)">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-4 -mt-4"></div>
            <div class="relative"><div class="text-amber-100 text-xs mb-1">Cuti Pending</div><div class="text-3xl font-bold">{{ $stats['cuti_pending'] }}</div></div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #ef4444, #dc2626)">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-4 -mt-4"></div>
            <div class="relative"><div class="text-red-100 text-xs mb-1">Belum Absen</div><div class="text-3xl font-bold">{{ max(0, $stats['alpha_hari_ini']) }}</div></div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Absensi Terbaru</h3>
                <a href="{{ route('admin-hrd.absensi.monitor') }}" class="text-sky-500 text-sm">Lihat semua →</a>
            </div>
            <div class="space-y-2">
                @forelse($absensiHariIni as $row)
                    @php $badge = $row->status_badge; @endphp
                    <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50">
                        <img src="{{ $row->karyawan->foto_url }}" class="w-8 h-8 rounded-lg object-cover flex-shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium truncate">{{ $row->karyawan->nama_lengkap }}</div>
                            <div class="text-xs text-slate-500">{{ $row->karyawan->divisi?->nama_divisi }}</div>
                        </div>
                        <div class="text-right text-xs">
                            <div class="font-mono text-slate-700">{{ $row->waktu_masuk ? substr($row->waktu_masuk, 0, 5) : '-' }}</div>
                            <span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-4">Belum ada absensi hari ini</p>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Pengajuan Cuti/Izin Pending</h3>
                <a href="{{ route('admin-hrd.cuti-izin.index') }}" class="text-sky-500 text-sm">Lihat semua →</a>
            </div>
            <div class="space-y-2">
                @forelse($cutiPending as $row)
                    <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50">
                        <img src="{{ $row->karyawan->foto_url }}" class="w-8 h-8 rounded-lg object-cover flex-shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium truncate">{{ $row->karyawan->nama_lengkap }}</div>
                            <div class="text-xs text-slate-500">{{ ucfirst($row->jenis) }} • {{ $row->jumlah_hari }} hari</div>
                        </div>
                        <a href="{{ route('approval.cuti-izin.show', $row) }}" class="btn-primary text-xs px-2 py-1">Review</a>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-4">Tidak ada pengajuan pending</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Karyawan Belum Absen Hari Ini --}}
    <div class="mt-5 card">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-800">Karyawan Belum Absen Hari Ini</h3>
                @if($karyawanLupaAbsen->count() > 0)
                    <span class="px-2 py-0.5 bg-rose-100 text-rose-600 text-xs font-bold rounded-full">{{ $karyawanLupaAbsen->count() }}</span>
                @endif
            </div>
            <a href="{{ route('admin-hrd.absensi.monitor') }}" class="text-sky-500 text-sm">Lihat Monitoring →</a>
        </div>

        @if($karyawanLupaAbsen->isEmpty())
            <p class="text-slate-400 text-sm text-center py-4">
                ✓ Semua karyawan aktif sudah melakukan absensi hari ini.
            </p>
        @else
            <div class="space-y-2">
                @foreach($karyawanLupaAbsen as $k)
                    <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50">
                        <img src="{{ $k->foto_url }}" class="w-8 h-8 rounded-lg object-cover flex-shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium truncate">{{ $k->nama_lengkap }}</div>
                            <div class="text-xs text-slate-500">{{ $k->divisi?->nama_divisi ?? '-' }} • {{ $k->jabatan?->nama_jabatan ?? '-' }}</div>
                        </div>
                        <span class="px-2 py-1 bg-rose-50 text-rose-600 rounded-md text-[10px] font-bold flex-shrink-0">Belum Absen</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
