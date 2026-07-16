<x-app-layout>
    <x-slot name="title">Dashboard Super Admin</x-slot>
    <x-slot name="breadcrumb">Selamat datang, {{ auth()->user()->nama }}</x-slot>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        <div class="stat-card" style="background: linear-gradient(135deg, #0ea5e9, #0284c7)">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-6 -mt-6"></div>
            <div class="relative">
                <div class="text-sky-100 text-sm font-medium mb-1">Total Pengguna</div>
                <div class="text-4xl font-bold">{{ $stats['total_users'] }}</div>
                <div class="text-sky-200 text-xs mt-1">{{ $stats['users_aktif'] }} aktif</div>
            </div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669)">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-6 -mt-6"></div>
            <div class="relative">
                <div class="text-emerald-100 text-sm font-medium mb-1">Total Karyawan</div>
                <div class="text-4xl font-bold">{{ $stats['total_karyawan'] }}</div>
                <div class="text-emerald-200 text-xs mt-1">{{ $stats['karyawan_aktif'] }} aktif</div>
            </div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed)">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-6 -mt-6"></div>
            <div class="relative">
                <div class="text-purple-100 text-sm font-medium mb-1">Konfigurasi Kantor</div>
                <div class="text-lg font-bold mt-2">
                    @if($konfigurasi && $konfigurasi->isKonfigured())
                        <span class="text-emerald-300">✓ Terkonfigurasi</span>
                    @else
                        <span class="text-amber-300">⚠ Belum diatur</span>
                    @endif
                </div>
                @if($konfigurasi)
                    <div class="text-purple-200 text-xs mt-1">Radius: {{ $konfigurasi->radius_meter }}m</div>
                @endif
            </div>
        </div>
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706)">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-6 -mt-6"></div>
            <div class="relative">
                <div class="text-amber-100 text-sm font-medium mb-1">Jam Kerja</div>
                @if($konfigurasi)
                    <div class="text-2xl font-bold">{{ substr($konfigurasi->jam_masuk, 0, 5) }}</div>
                    <div class="text-amber-200 text-xs mt-1">s/d {{ substr($konfigurasi->jam_keluar, 0, 5) }}, toleransi {{ $konfigurasi->toleransi_menit }} mnt</div>
                @else
                    <div class="text-2xl font-bold">—</div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        <!-- Recent Users -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Pengguna Terbaru</h3>
                <a href="{{ route('super-admin.users.index') }}" class="text-sky-500 text-sm hover:text-sky-700">Lihat semua →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentUsers as $user)
                    <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                        <img src="{{ $user->avatar }}" class="w-9 h-9 rounded-lg object-cover" alt="">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-800 truncate">{{ $user->nama }}</div>
                            <div class="text-xs text-slate-500">{{ $user->email }}</div>
                        </div>
                        <span class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                @empty
                    <p class="text-slate-400 text-sm text-center py-4">Belum ada pengguna</p>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <h3 class="font-semibold text-slate-800 mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('super-admin.users.create') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-dashed border-slate-200 hover:border-sky-300 hover:bg-sky-50 transition-all group">
                    <div class="w-10 h-10 bg-sky-100 group-hover:bg-sky-200 rounded-xl flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <span class="text-sm font-medium text-slate-700 group-hover:text-sky-700">Tambah Pengguna</span>
                </a>
                <a href="{{ route('super-admin.konfigurasi.index') }}" class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-dashed border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 transition-all group">
                    <div class="w-10 h-10 bg-emerald-100 group-hover:bg-emerald-200 rounded-xl flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-sm font-medium text-slate-700 group-hover:text-emerald-700">Atur Lokasi Kantor</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
