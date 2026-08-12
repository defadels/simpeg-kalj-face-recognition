<x-app-layout>
    <x-slot name="title">Ajukan Cuti / Izin / Sakit</x-slot>

    <div class="max-w-2xl">
        <div class="card">
            <div class="mb-5">
                <h3 class="font-semibold text-slate-800">Form Pengajuan Cuti / Izin / Sakit</h3>
                <p class="text-slate-500 text-sm mt-1">Saldo cuti Anda: <strong class="text-emerald-600">{{ $karyawan->saldo_cuti }} hari</strong></p>
            </div>

            <form method="POST" action="{{ route('karyawan.cuti-izin.store') }}" enctype="multipart/form-data" class="space-y-4"
                  x-data="{ jenis: '{{ old('jenis', 'cuti') }}' }">
                @csrf

                <div>
                    <label class="form-label">Jenis Permohonan *</label>
                    <select name="jenis" class="form-input" required x-model="jenis">
                        <option value="cuti" {{ old('jenis') === 'cuti' ? 'selected' : '' }}>Cuti</option>
                        <option value="izin" {{ old('jenis') === 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ old('jenis') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    </select>
                    @error('jenis')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Tanggal Mulai *</label>
                        {{-- Untuk jenis 'sakit', retroaktif diizinkan (tidak ada min date) --}}
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                               :min="jenis === 'sakit' ? '' : '{{ today()->format('Y-m-d') }}'"
                               class="form-input" required>
                        @error('tanggal_mulai')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Tanggal Selesai *</label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                               :min="jenis === 'sakit' ? '' : '{{ today()->format('Y-m-d') }}'"
                               class="form-input" required>
                        @error('tanggal_selesai')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="form-label">Alasan *</label>
                    <textarea name="alasan" rows="4" class="form-input" required placeholder="Jelaskan alasan pengajuan cuti/izin Anda secara detail (minimal 10 karakter)...">{{ old('alasan') }}</textarea>
                    @error('alasan')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">Lampiran <span class="text-slate-400">(opsional — surat dokter, dll)</span></label>
                    <input type="file" name="lampiran" accept=".pdf,.jpg,.jpeg,.png" class="form-input">
                    <p class="text-xs text-slate-500 mt-1">Format: PDF, JPG, PNG — maks. 2MB</p>
                    @error('lampiran')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-amber-700 text-sm"><strong>Catatan:</strong>
                        Pengajuan <strong>Cuti</strong> memotong saldo cuti dan tidak dapat diajukan retroaktif.
                        Pengajuan <strong>Sakit</strong> tidak memotong saldo cuti dan dapat diajukan untuk tanggal yang sudah lewat (disertai surat dokter).
                        Perhitungan hari tidak termasuk akhir pekan.
                    </p>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">Kirim Pengajuan</button>
                    <a href="{{ route('karyawan.cuti-izin.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
