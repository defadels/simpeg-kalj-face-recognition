<x-app-layout>
    <x-slot name="title">Cuti & Izin</x-slot>

    <div class="space-y-5">
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-slate-800">Pengajuan Cuti & Izin</h3>
                    <p class="text-slate-500 text-sm">Saldo cuti tersisa: <strong class="text-emerald-600">{{ $karyawan->saldo_cuti }} hari</strong></p>
                </div>
                <a href="{{ route('karyawan.cuti-izin.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Ajukan Cuti/Izin
                </a>
            </div>

            <form method="GET" class="flex gap-3 mb-5">
                <select name="status" class="form-input w-40">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <button type="submit" class="btn-primary">Filter</button>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Jenis</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Periode</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Hari</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Catatan</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Diajukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cutiIzin as $row)
                            @php $badge = $row->status_badge; @endphp
                            <tr class="table-row">
                                <td class="py-3 px-3"><span class="badge {{ $row->jenis === 'cuti' ? 'badge-purple' : ($row->jenis === 'sakit' ? 'badge-orange' : 'badge-blue') }}">{{ ucfirst($row->jenis) }}</span></td>
                                <td class="py-3 px-3 text-xs text-slate-600">{{ $row->tanggal_mulai->format('d M') }} — {{ $row->tanggal_selesai->format('d M Y') }}</td>
                                <td class="py-3 px-3 font-bold text-sky-600">{{ $row->jumlah_hari }}</td>
                                <td class="py-3 px-3"><span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span></td>
                                <td class="py-3 px-3 text-xs text-slate-500">{{ $row->catatan_prosesor ?? '-' }}</td>
                                <td class="py-3 px-3 text-xs text-slate-400">{{ $row->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-10 text-center text-slate-400">Belum ada pengajuan cuti/izin.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $cutiIzin->links() }}</div>
        </div>
    </div>
</x-app-layout>
