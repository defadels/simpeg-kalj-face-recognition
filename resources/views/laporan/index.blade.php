<x-app-layout>
    <x-slot name="title">Laporan Kehadiran</x-slot>

    <div class="space-y-5">
        <!-- Filter -->
        <div class="card">
            <h3 class="font-semibold text-slate-800 mb-4">Filter Laporan</h3>
            <form method="GET" class="flex flex-wrap gap-3">
                <div>
                    <label class="form-label text-xs">Periode Awal</label>
                    <input type="date" name="periode_awal" value="{{ $periodeAwal }}" class="form-input">
                </div>
                <div>
                    <label class="form-label text-xs">Periode Akhir</label>
                    <input type="date" name="periode_akhir" value="{{ $periodeAkhir }}" class="form-input">
                </div>
                @if(auth()->user()->role !== 'manajer')
                <div>
                    <label class="form-label text-xs">Divisi</label>
                    <select name="divisi_id" class="form-input max-w-xs">
                        <option value="">Semua Divisi</option>
                        @foreach($divisi as $d)
                            <option value="{{ $d->id }}" {{ $divisiId == $d->id ? 'selected' : '' }}>{{ $d->nama_divisi }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary">Generate Laporan</button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            @foreach([
                ['label' => 'Hadir', 'value' => $summary['total_hadir'], 'color' => 'emerald'],
                ['label' => 'Terlambat', 'value' => $summary['total_terlambat'], 'color' => 'amber'],
                ['label' => 'Alpha', 'value' => $summary['total_alpha'], 'color' => 'red'],
                ['label' => 'Cuti', 'value' => $summary['total_cuti'], 'color' => 'purple'],
                ['label' => 'Izin', 'value' => $summary['total_izin'], 'color' => 'sky'],
            ] as $s)
            <div class="p-4 bg-{{ $s['color'] }}-50 border border-{{ $s['color'] }}-100 rounded-xl text-center">
                <div class="text-2xl font-bold text-{{ $s['color'] }}-600">{{ $s['value'] }}</div>
                <div class="text-xs text-{{ $s['color'] }}-500 mt-0.5">{{ $s['label'] }}</div>
            </div>
            @endforeach
        </div>

        <!-- Export Buttons -->
        <div class="flex gap-3">
            <a href="{{ route(auth()->user()->role === 'manajer' ? 'manajer.laporan.export-pdf' : 'admin-hrd.laporan.export-pdf', request()->query()) }}"
               target="_blank" class="btn-danger">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Export PDF
            </a>
            <a href="{{ route(auth()->user()->role === 'manajer' ? 'manajer.laporan.export-excel' : 'admin-hrd.laporan.export-excel', request()->query()) }}"
               class="btn-success">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </a>
        </div>

        <!-- Data Table -->
        <div class="card">
            <h4 class="font-semibold text-slate-700 mb-4">Rekap Kehadiran</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-3 px-2 font-semibold text-slate-500 uppercase">Karyawan</th>
                            <th class="text-left py-3 px-2 font-semibold text-slate-500 uppercase">Divisi</th>
                            <th class="text-left py-3 px-2 font-semibold text-slate-500 uppercase">Tanggal</th>
                            <th class="text-left py-3 px-2 font-semibold text-slate-500 uppercase">Masuk</th>
                            <th class="text-left py-3 px-2 font-semibold text-slate-500 uppercase">Keluar</th>
                            <th class="text-left py-3 px-2 font-semibold text-slate-500 uppercase">Jam</th>
                            <th class="text-left py-3 px-2 font-semibold text-slate-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensi->take(50) as $row)
                            @php $badge = $row->status_badge; @endphp
                            <tr class="table-row">
                                <td class="py-2 px-2 font-medium">{{ $row->karyawan->nama_lengkap }}</td>
                                <td class="py-2 px-2 text-slate-500">{{ $row->karyawan->divisi?->nama_divisi ?? '-' }}</td>
                                <td class="py-2 px-2 text-slate-600">{{ $row->tanggal->format('d/m/Y') }}</td>
                                <td class="py-2 px-2 font-mono">{{ $row->waktu_masuk ? substr($row->waktu_masuk, 0, 5) : '-' }}</td>
                                <td class="py-2 px-2 font-mono">{{ $row->waktu_keluar ? substr($row->waktu_keluar, 0, 5) : '-' }}</td>
                                <td class="py-2 px-2">{{ $row->jam_kerja ?? '-' }}</td>
                                <td class="py-2 px-2"><span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data untuk periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($absensi->count() > 50)
                <p class="text-xs text-slate-400 mt-3 text-center">Menampilkan 50 dari {{ $absensi->count() }} record. Export PDF/Excel untuk data lengkap.</p>
            @endif
        </div>
    </div>
</x-app-layout>
