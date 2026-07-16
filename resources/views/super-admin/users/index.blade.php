<x-app-layout>
    <x-slot name="title">Kelola Pengguna</x-slot>

    <div class="card">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-semibold text-slate-800">Daftar Pengguna</h3>
                <p class="text-slate-500 text-sm">Total {{ $users->total() }} pengguna</p>
            </div>
            <a href="{{ route('super-admin.users.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Pengguna
            </a>
        </div>

        <!-- Filter -->
        <form method="GET" class="flex gap-3 mb-5">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..." class="form-input max-w-xs">
            <select name="role" class="form-input max-w-xs">
                <option value="">Semua Role</option>
                <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="admin_hrd" {{ request('role') === 'admin_hrd' ? 'selected' : '' }}>Admin HRD</option>
                <option value="manajer" {{ request('role') === 'manajer' ? 'selected' : '' }}>Manajer</option>
                <option value="karyawan" {{ request('role') === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
            </select>
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('super-admin.users.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Pengguna</th>
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Role</th>
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Dibuat</th>
                        <th class="text-right py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="table-row">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar }}" class="w-8 h-8 rounded-lg object-cover" alt="">
                                    <div>
                                        <div class="font-medium text-slate-800">{{ $user->nama }}</div>
                                        <div class="text-slate-500 text-xs">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="badge {{ match($user->role) {
                                    'super_admin' => 'badge-purple',
                                    'admin_hrd' => 'badge-blue',
                                    'manajer' => 'badge-yellow',
                                    default => 'badge-green'
                                } }}">{{ str_replace('_', ' ', ucwords($user->role, '_')) }}</span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-slate-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="py-3 px-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('super-admin.users.edit', $user) }}" class="text-sky-600 hover:text-sky-800 font-medium text-xs">Edit</a>
                                    <form method="POST" action="{{ route('super-admin.users.toggle-status', $user) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-{{ $user->is_active ? 'amber' : 'emerald' }}-600 hover:text-{{ $user->is_active ? 'amber' : 'emerald' }}-800 font-medium text-xs">
                                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('super-admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Yakin hapus pengguna ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-12 text-center text-slate-400">Tidak ada pengguna ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-app-layout>
