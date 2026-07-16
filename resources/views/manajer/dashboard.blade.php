<x-app-layout>
    <x-slot name="title">Dashboard Manajer</x-slot>

    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #0ea5e9, #0284c7)">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-4 -mt-4"></div>
            <div class="relative"><div class="text-sky-100 text-xs mb-1">Total Tim</div><div class="text-3xl font-bold">{{ $stats['total_tim'] }}</div></div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669)">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-4 -mt-4"></div>
            <div class="relative"><div class="text-emerald-100 text-xs mb-1">Hadir Hari Ini</div><div class="text-3xl font-bold">{{ $stats['hadir_hari_ini'] }}</div></div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706)">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-4 -mt-4"></div>
            <div class="relative"><div class="text-amber-100 text-xs mb-1">Cuti Pending</div><div class="text-3xl font-bold">{{ $stats['cuti_pending'] }}</div></div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-slate-800">Kehadiran Tim — {{ $divisi?->nama_divisi ?? 'Divisi Anda' }}</h3>
                <p class="text-slate-500 text-sm">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <a href="{{ route('manajer.absensi.monitor') }}" class="btn-secondary text-sm">Monitor Lengkap →</a>
        </div>
        <div class="space-y-2">
            @forelse($absensiTim as $row)
                @php $badge = $row->status_badge; @endphp
                <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50">
                    <img src="{{ $row->karyawan->foto_url }}" class="w-9 h-9 rounded-lg object-cover flex-shrink-0" alt="">
                    <div class="flex-1">
                        <div class="text-sm font-medium">{{ $row->karyawan->nama_lengkap }}</div>
                        <div class="text-xs text-slate-500">
                            Masuk: {{ $row->waktu_masuk ? substr($row->waktu_masuk, 0, 5) : '-' }} •
                            Keluar: {{ $row->waktu_keluar ? substr($row->waktu_keluar, 0, 5) : '-' }}
                        </div>
                    </div>
                    <span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
                </div>
            @empty
                <p class="text-slate-400 text-sm text-center py-8">Belum ada data kehadiran hari ini</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
