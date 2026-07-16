<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="breadcrumb">Selamat datang, {{ $karyawan->nama_lengkap }}</x-slot>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #0ea5e9, #0284c7)">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-4 -mt-4"></div>
            <div class="relative">
                <div class="text-sky-100 text-xs mb-1">Hadir Bulan Ini</div>
                <div class="text-3xl font-bold">{{ $statsbulan['hadir'] }}</div>
                <div class="text-sky-200 text-xs mt-1">hari tepat waktu</div>
            </div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706)">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-4 -mt-4"></div>
            <div class="relative">
                <div class="text-amber-100 text-xs mb-1">Terlambat</div>
                <div class="text-3xl font-bold">{{ $statsbulan['terlambat'] }}</div>
                <div class="text-amber-200 text-xs mt-1">hari</div>
            </div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669)">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-4 -mt-4"></div>
            <div class="relative">
                <div class="text-emerald-100 text-xs mb-1">Saldo Cuti</div>
                <div class="text-3xl font-bold">{{ $karyawan->saldo_cuti }}</div>
                <div class="text-emerald-200 text-xs mt-1">hari tersisa</div>
            </div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed)">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-4 -mt-4"></div>
            <div class="relative">
                <div class="text-purple-100 text-xs mb-1">Cuti Pending</div>
                <div class="text-3xl font-bold">{{ $cutiPending }}</div>
                <div class="text-purple-200 text-xs mt-1">menunggu persetujuan</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        <!-- Absensi Hari Ini -->
        <div class="card xl:col-span-1">
            <h3 class="font-semibold text-slate-800 mb-4">Kehadiran Hari Ini</h3>
            @if($absensiHariIni)
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-emerald-50 rounded-lg">
                        <span class="text-sm text-slate-600">Masuk</span>
                        <span class="font-bold text-emerald-600">{{ $absensiHariIni->waktu_masuk ? substr($absensiHariIni->waktu_masuk, 0, 5) : '--:--' }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-sky-50 rounded-lg">
                        <span class="text-sm text-slate-600">Keluar</span>
                        <span class="font-bold text-sky-600">{{ $absensiHariIni->waktu_keluar ? substr($absensiHariIni->waktu_keluar, 0, 5) : 'Belum' }}</span>
                    </div>
                    @php $badge = $absensiHariIni->status_badge; @endphp
                    <div class="flex justify-between items-center p-3 bg-slate-50 rounded-lg">
                        <span class="text-sm text-slate-600">Status</span>
                        <span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
                    </div>
                </div>
            @else
                <div class="text-center py-6">
                    <p class="text-slate-400 text-sm mb-3">Belum absen hari ini</p>
                </div>
            @endif
            <a href="{{ route('karyawan.absensi.index') }}" class="btn-primary w-full justify-center mt-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $absensiHariIni && $absensiHariIni->waktu_masuk && !$absensiHariIni->waktu_keluar ? 'Absen Keluar' : ($absensiHariIni && $absensiHariIni->waktu_keluar ? 'Lihat Detail' : 'Absen Sekarang') }}
            </a>
        </div>

        <!-- Riwayat 7 Hari Terakhir -->
        <div class="card xl:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">7 Hari Terakhir</h3>
                <a href="{{ route('karyawan.absensi.riwayat') }}" class="text-sky-500 text-sm hover:text-sky-700">Lihat semua →</a>
            </div>
            <div class="space-y-2">
                @forelse($absensiTerakhir as $row)
                    @php $badge = $row->status_badge; @endphp
                    <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-{{ $badge['color'] === 'green' ? 'emerald' : ($badge['color'] === 'yellow' ? 'amber' : ($badge['color'] === 'red' ? 'red' : 'sky')) }}-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-{{ $badge['color'] === 'green' ? 'emerald' : ($badge['color'] === 'yellow' ? 'amber' : ($badge['color'] === 'red' ? 'red' : 'sky')) }}-600">
                                {{ $row->tanggal->format('d') }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-700">{{ $row->tanggal->isoFormat('ddd, D MMM') }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $row->waktu_masuk ? substr($row->waktu_masuk, 0, 5) : '-' }} —
                                {{ $row->waktu_keluar ? substr($row->waktu_keluar, 0, 5) : '-' }}
                                @if($row->jam_kerja) <span class="ml-1 text-slate-400">{{ $row->jam_kerja }} jam</span> @endif
                            </div>
                        </div>
                        <span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-6">Belum ada riwayat absensi</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
