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
                    <div class="text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wider">Dokumen / Lampiran Pendukung</div>
                    @php
                        $ext = strtolower(pathinfo($cutiIzin->lampiran, PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                        $fileUrl = asset('storage/' . $cutiIzin->lampiran);
                    @endphp

                    @if($isImage)
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                            <div class="overflow-hidden rounded-xl bg-slate-100 max-h-80 flex items-center justify-center">
                                <img src="{{ $fileUrl }}" alt="Lampiran {{ $cutiIzin->karyawan->nama_lengkap }}" class="max-h-80 object-contain w-full rounded-xl">
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-xs text-slate-500 font-mono truncate max-w-[200px]">{{ basename($cutiIzin->lampiran) }}</span>
                                <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="btn-secondary text-xs py-1.5 px-3">
                                    🔍 Buka Ukuran Penuh ↗
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xs uppercase">
                                    {{ $ext ?: 'DOC' }}
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-700 truncate max-w-[220px]">{{ basename($cutiIzin->lampiran) }}</div>
                                    <div class="text-[10px] text-slate-400">Dokumen Lampiran Pengajuan</div>
                                </div>
                            </div>
                            <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="btn-primary text-xs py-2 px-4">
                                📄 Buka Dokumen ↗
                            </a>
                        </div>
                    @endif
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
            @if($cutiIzin->status === 'pending' && (auth()->user()->isAdmin() || in_array(auth()->user()->role, ['admin', 'admin_hrd', 'manajer', 'super_admin'])))
                <div class="grid grid-cols-2 gap-4" x-data="{ showReject: false }">
                    <form method="POST" action="{{ auth()->user()->isAdmin() ? route('admin.cuti-izin.approve', $cutiIzin) : route('approval.cuti-izin.approve', $cutiIzin) }}" class="space-y-2">
                        @csrf @method('PATCH')
                        <textarea name="catatan_prosesor" placeholder="Catatan (opsional)..." rows="2" class="form-input text-sm"></textarea>
                        <button type="submit" class="btn-success w-full justify-center" onclick="return confirm('Setujui pengajuan ini?')">
                            ✓ Setujui
                        </button>
                    </form>
                    <form method="POST" action="{{ auth()->user()->isAdmin() ? route('admin.cuti-izin.reject', $cutiIzin) : route('approval.cuti-izin.reject', $cutiIzin) }}" class="space-y-2">
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
