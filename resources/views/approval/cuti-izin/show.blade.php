<x-app-layout>
    <x-slot name="title">Detail Pengajuan Cuti/Izin</x-slot>

    <div class="max-w-2xl">
        <div class="card">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-semibold text-slate-800">Detail Pengajuan</h3>
                @php $badge = $cutiIzin->status_badge; @endphp
                <span class="badge badge-{{ $badge['color'] }} text-sm px-3 py-1">{{ $badge['label'] }}</span>
            </div>

            <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl mb-5">
                <img src="{{ $cutiIzin->karyawan->foto_url }}" class="w-14 h-14 rounded-xl object-cover" alt="">
                <div>
                    <div class="font-semibold text-slate-800 text-lg">{{ $cutiIzin->karyawan->nama_lengkap }}</div>
                    <div class="text-slate-500 text-sm">{{ $cutiIzin->karyawan->jabatan?->nama_jabatan }} — {{ $cutiIzin->karyawan->divisi?->nama_divisi }}</div>
                    <div class="text-slate-400 text-xs mt-0.5">Saldo cuti tersisa: <strong class="text-emerald-600">{{ $cutiIzin->karyawan->saldo_cuti }} hari</strong></div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-5">
                <div class="p-3 bg-slate-50 rounded-lg"><div class="text-xs text-slate-500">Jenis</div><div class="font-semibold mt-1">{{ ucfirst($cutiIzin->jenis) }}</div></div>
                <div class="p-3 bg-slate-50 rounded-lg"><div class="text-xs text-slate-500">Jumlah Hari</div><div class="font-semibold mt-1 text-sky-600">{{ $cutiIzin->jumlah_hari }} hari kerja</div></div>
                <div class="p-3 bg-slate-50 rounded-lg"><div class="text-xs text-slate-500">Tanggal Mulai</div><div class="font-semibold mt-1">{{ $cutiIzin->tanggal_mulai->isoFormat('D MMMM Y') }}</div></div>
                <div class="p-3 bg-slate-50 rounded-lg"><div class="text-xs text-slate-500">Tanggal Selesai</div><div class="font-semibold mt-1">{{ $cutiIzin->tanggal_selesai->isoFormat('D MMMM Y') }}</div></div>
            </div>

            <div class="mb-5">
                <div class="text-xs text-slate-500 mb-1">Alasan</div>
                <p class="text-slate-700 bg-slate-50 p-3 rounded-lg text-sm">{{ $cutiIzin->alasan }}</p>
            </div>

            @if($cutiIzin->lampiran)
                <div class="mb-5">
                    <div class="text-xs text-slate-500 mb-1">Lampiran</div>
                    <a href="{{ asset('storage/' . $cutiIzin->lampiran) }}" target="_blank" class="btn-secondary text-sm">Lihat Lampiran</a>
                </div>
            @endif

            @if($cutiIzin->status !== 'pending')
                <div class="p-4 bg-slate-50 rounded-xl mb-5">
                    <div class="text-xs text-slate-500 mb-1">Diproses oleh {{ $cutiIzin->prosesor?->nama }} — {{ $cutiIzin->tanggal_proses?->isoFormat('D MMM Y HH:mm') }}</div>
                    @if($cutiIzin->catatan_prosesor)
                        <p class="text-slate-700 text-sm mt-1">{{ $cutiIzin->catatan_prosesor }}</p>
                    @endif
                </div>
            @endif

            {{-- Action Buttons --}}
            @if($cutiIzin->status === 'pending' && in_array(auth()->user()->role, ['admin_hrd', 'manajer']))
                <div class="grid grid-cols-2 gap-4" x-data="{ showReject: false }">
                    <form method="POST" action="{{ route('approval.cuti-izin.approve', $cutiIzin) }}" class="space-y-2">
                        @csrf @method('PATCH')
                        <textarea name="catatan_prosesor" placeholder="Catatan (opsional)..." rows="2" class="form-input text-sm"></textarea>
                        <button type="submit" class="btn-success w-full justify-center" onclick="return confirm('Setujui pengajuan ini?')">
                            ✓ Setujui
                        </button>
                    </form>
                    <form method="POST" action="{{ route('approval.cuti-izin.reject', $cutiIzin) }}" class="space-y-2">
                        @csrf @method('PATCH')
                        <textarea name="catatan_prosesor" placeholder="Alasan penolakan (wajib)..." rows="2" class="form-input text-sm" required></textarea>
                        <button type="submit" class="btn-danger w-full justify-center" onclick="return confirm('Tolak pengajuan ini?')">
                            ✗ Tolak
                        </button>
                    </form>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="btn-secondary text-sm">← Kembali</a>
            </div>
        </div>
    </div>
</x-app-layout>
