<x-app-layout>
    <x-slot name="title">Riwayat Absensi</x-slot>

    <div class="card">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-slate-800">Riwayat Kehadiran</h3>
            <form method="GET" class="flex gap-2">
                <select name="bulan" class="form-input text-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('bulan', now()->month) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
                <select name="tahun" class="form-input text-sm">
                    @for($y = now()->year; $y >= now()->year - 2; $y--)
                        <option value="{{ $y }}" {{ request('tahun', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn-primary text-sm">Filter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Tanggal</th>
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Masuk</th>
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Keluar</th>
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Jam Kerja</th>
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-3 font-semibold text-slate-500 text-xs uppercase tracking-wider">Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absensi as $row)
                        @php $badge = $row->status_badge; @endphp
                        <tr class="table-row">
                            <td class="py-3 px-3">
                                <div class="font-medium text-slate-800">{{ $row->tanggal->isoFormat('ddd, D MMM Y') }}</div>
                            </td>
                            <td class="py-3 px-3 font-mono text-slate-700">{{ $row->waktu_masuk ? substr($row->waktu_masuk, 0, 5) : '-' }}</td>
                            <td class="py-3 px-3 font-mono text-slate-700">{{ $row->waktu_keluar ? substr($row->waktu_keluar, 0, 5) : '-' }}</td>
                            <td class="py-3 px-3 text-slate-600">{{ $row->jam_kerja ? $row->jam_kerja . ' jam' : '-' }}</td>
                            <td class="py-3 px-3">
                                <span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
                            </td>
                            <td class="py-3 px-3">
                                @if($row->status_lokasi)
                                    <span class="badge {{ $row->status_lokasi === 'valid' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($row->status_lokasi) }}</span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-12 text-center text-slate-400">Tidak ada data absensi untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $absensi->links() }}</div>
    </div>
</x-app-layout>
