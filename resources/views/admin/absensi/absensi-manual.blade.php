<x-app-layout>
    <x-slot name="title">Absensi Manual</x-slot>
    <x-slot name="breadcrumb">Input Absensi Manual Karyawan</x-slot>

    {{-- Header Info --}}
    <div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <div class="font-bold text-amber-800 text-sm">Absensi Manual (Tanpa Face Recognition)</div>
            <div class="text-amber-700 text-xs mt-1 leading-relaxed">
                Fitur ini digunakan untuk mengisi absensi karyawan yang belum melakukan absensi pada tanggal tertentu.
                Absensi manual <strong>tidak memerlukan</strong> verifikasi wajah maupun lokasi GPS &mdash; digunakan untuk kondisi urgensi.
                Data yang diisi akan ditandai dengan label <code class="bg-amber-100 px-1 rounded text-amber-800 font-mono text-[11px]">[MANUAL]</code>.
            </div>
        </div>
    </div>

    {{-- Filter Tanggal --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Pilih Tanggal</label>
                <input type="date" id="filter-tanggal" value="{{ $tanggal }}" max="{{ today()->toDateString() }}" class="form-input w-full sm:w-64">
            </div>
            <div class="flex items-center gap-2">
                <button id="btn-filter" type="button" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
                </button>
                <a href="{{ route('admin.absensi.monitor') }}" class="btn-secondary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Monitor Absensi
                </a>
            </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
            Menampilkan karyawan belum absen pada:
            <span class="font-semibold text-slate-700" id="tanggal-label">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    {{-- Tabel Karyawan Belum Absen --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-800 text-sm">Karyawan Belum Absen</h3>
                <span id="badge-count" class="px-2.5 py-0.5 bg-rose-100 text-rose-600 text-xs font-bold rounded-full">{{ $karyawanBelumAbsen->count() }}</span>
            </div>
            <div id="loading-spinner" class="hidden">
                <div class="flex items-center gap-2 text-xs text-slate-400">
                    <svg class="animate-spin w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Memuat...
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Karyawan</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Divisi</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Jabatan</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Jadwal</th>
                        <th class="text-right py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-karyawan">
                    @forelse($karyawanBelumAbsen as $k)
                        <tr class="table-row">
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $k->foto_url }}" class="w-9 h-9 rounded-xl object-cover ring-1 ring-slate-100 flex-shrink-0" alt="">
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $k->nama_lengkap }}</div>
                                        <div class="text-xs text-slate-400 font-mono">{{ $k->nip }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-xs text-slate-600 font-medium">{{ $k->divisi?->nama_divisi ?? '-' }}</td>
                            <td class="py-3 px-3 text-xs text-slate-600">{{ $k->jabatan?->nama_jabatan ?? '-' }}</td>
                            <td class="py-3 px-3 text-xs text-slate-500 font-mono">
                                @if($k->divisi && $k->divisi->jam_masuk)
                                    {{ substr($k->divisi->jam_masuk, 0, 5) }} &ndash; {{ substr($k->divisi->jam_keluar, 0, 5) }}
                                @else
                                    <span class="text-slate-400">&mdash;</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right">
                                <button type="button"
                                    class="btn-isi-absensi inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm"
                                    data-id="{{ $k->id }}"
                                    data-nama="{{ $k->nama_lengkap }}"
                                    data-nip="{{ $k->nip }}"
                                    data-divisi="{{ $k->divisi?->nama_divisi ?? '-' }}"
                                    data-jabatan="{{ $k->jabatan?->nama_jabatan ?? '-' }}"
                                    data-foto="{{ $k->foto_url }}"
                                    data-jam-masuk="{{ $k->divisi?->jam_masuk ? substr($k->divisi->jam_masuk,0,5) : '' }}"
                                    data-jam-keluar="{{ $k->divisi?->jam_keluar ? substr($k->divisi->jam_keluar,0,5) : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Isi Absensi
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="row-empty">
                            <td colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <svg class="w-12 h-12 text-emerald-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <div class="font-semibold text-slate-600 text-sm">Semua karyawan sudah absen</div>
                                        <div class="text-xs mt-1">Tidak ada karyawan yang perlu diisi absensi manualnya</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- =================== MODAL ABSENSI MANUAL =================== --}}
    <div id="modal-absensi" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" id="modal-backdrop"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg">
                {{-- Header --}}
                <div class="flex items-center justify-between p-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Isi Absensi Manual</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Tanpa verifikasi wajah &amp; lokasi GPS</p>
                        </div>
                    </div>
                    <button type="button" id="btn-close-modal" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Karyawan Info --}}
                <div class="px-5 pt-4 pb-3 bg-slate-50 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <img id="modal-foto" src="" class="w-12 h-12 rounded-xl object-cover ring-2 ring-white shadow" alt="">
                        <div>
                            <div id="modal-nama" class="font-bold text-slate-800 text-sm"></div>
                            <div id="modal-nip" class="text-xs text-slate-400 font-mono"></div>
                            <div class="flex items-center gap-2 mt-1">
                                <span id="modal-divisi" class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[11px] font-semibold rounded-md"></span>
                                <span id="modal-jabatan" class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[11px] font-semibold rounded-md"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <form id="form-absensi-manual" class="p-5 space-y-4">
                    @csrf
                    <input type="hidden" id="input-karyawan-id" name="karyawan_id">
                    <input type="hidden" id="input-tanggal" name="tanggal">

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tanggal Absensi</label>
                        <div id="display-tanggal" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-medium text-slate-700"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="input-waktu-masuk" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Waktu Masuk <span class="text-rose-500">*</span>
                            </label>
                            <input type="time" id="input-waktu-masuk" name="waktu_masuk" class="form-input w-full" required>
                            <p class="text-[11px] text-slate-400 mt-1" id="hint-jam-masuk"></p>
                        </div>
                        <div>
                            <label for="input-waktu-keluar" class="block text-xs font-semibold text-slate-600 mb-1.5">
                                Waktu Keluar <span class="text-slate-400 font-normal">(opsional)</span>
                            </label>
                            <input type="time" id="input-waktu-keluar" name="waktu_keluar" class="form-input w-full">
                            <p class="text-[11px] text-slate-400 mt-1" id="hint-jam-keluar"></p>
                        </div>
                    </div>

                    <div>
                        <label for="input-status" class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Status Kehadiran <span class="text-rose-500">*</span>
                        </label>
                        <select id="input-status" name="status_kehadiran" class="form-input w-full" required>
                            <option value="hadir">Hadir</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="cuti">Cuti</option>
                            <option value="alpha">Alpha</option>
                        </select>
                    </div>

                    <div>
                        <label for="input-keterangan" class="block text-xs font-semibold text-slate-600 mb-1.5">
                            Keterangan <span class="text-slate-400 font-normal">(opsional)</span>
                        </label>
                        <textarea id="input-keterangan" name="keterangan" rows="3"
                            class="form-input w-full resize-none text-sm"
                            placeholder="Alasan pengisian absensi manual..."></textarea>
                        <p class="text-[11px] text-slate-400 mt-1">
                            Akan disimpan dengan prefix <code class="bg-slate-100 px-1 rounded font-mono">[MANUAL]</code>
                        </p>
                    </div>

                    <div id="form-error" class="hidden px-4 py-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 flex items-start gap-2">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="form-error-message"></span>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                        <button type="button" id="btn-cancel-modal" class="btn-secondary">Batal</button>
                        <button type="submit" id="btn-submit" class="btn-primary inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span id="btn-submit-label">Simpan Absensi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div id="toast" class="fixed bottom-6 right-6 z-[100] hidden max-w-sm">
        <div id="toast-inner" class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-sm font-medium">
            <svg id="toast-icon" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"></svg>
            <span id="toast-message"></span>
        </div>
    </div>

    @push('scripts')
    <script>
    (function () {
        let activeTanggal = '{{ $tanggal }}';

        const filterTanggal  = document.getElementById('filter-tanggal');
        const btnFilter      = document.getElementById('btn-filter');
        const tbodyKaryawan  = document.getElementById('tbody-karyawan');
        const badgeCount     = document.getElementById('badge-count');
        const tanggalLabel   = document.getElementById('tanggal-label');
        const loadingSpinner = document.getElementById('loading-spinner');
        const modal          = document.getElementById('modal-absensi');
        const formAbsensi    = document.getElementById('form-absensi-manual');
        const formError      = document.getElementById('form-error');
        const formErrorMsg   = document.getElementById('form-error-message');
        const btnSubmit      = document.getElementById('btn-submit');
        const btnSubmitLabel = document.getElementById('btn-submit-label');
        const toast          = document.getElementById('toast');
        const toastInner     = document.getElementById('toast-inner');
        const toastIcon      = document.getElementById('toast-icon');
        const toastMsg       = document.getElementById('toast-message');

        // ── Filter ──
        btnFilter.addEventListener('click', () => loadKaryawan(filterTanggal.value));
        filterTanggal.addEventListener('keydown', e => { if (e.key === 'Enter') loadKaryawan(filterTanggal.value); });

        function formatTanggalID(isoDate) {
            const d = new Date(isoDate + 'T00:00:00');
            return d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }

        function loadKaryawan(tanggal) {
            if (!tanggal) return;
            activeTanggal = tanggal;
            loadingSpinner.classList.remove('hidden');
            tbodyKaryawan.style.opacity = '0.4';

            fetch(`{{ route('admin.absensi.belum-absen') }}?tanggal=${tanggal}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    renderTable(res.data);
                    badgeCount.textContent = res.data.length;
                    tanggalLabel.textContent = formatTanggalID(tanggal);
                }
            })
            .catch(() => showToast('Gagal memuat data. Coba lagi.', false))
            .finally(() => {
                loadingSpinner.classList.add('hidden');
                tbodyKaryawan.style.opacity = '1';
            });
        }

        function renderTable(data) {
            tbodyKaryawan.innerHTML = '';
            if (data.length === 0) {
                tbodyKaryawan.innerHTML = `
                <tr><td colspan="5" class="py-12 text-center">
                    <div class="flex flex-col items-center gap-3 text-slate-400">
                        <svg class="w-12 h-12 text-emerald-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <div class="font-semibold text-slate-600 text-sm">Semua karyawan sudah absen</div>
                            <div class="text-xs mt-1">Tidak ada karyawan yang perlu diisi absensi manualnya</div>
                        </div>
                    </div>
                </td></tr>`;
                return;
            }
            data.forEach(k => {
                const jamMasuk = k.jam_masuk ? k.jam_masuk.substring(0,5) : '';
                const jamKeluar = k.jam_keluar ? k.jam_keluar.substring(0,5) : '';
                const jadwal = (jamMasuk && jamKeluar) ? `${jamMasuk} - ${jamKeluar}` : '&mdash;';
                tbodyKaryawan.insertAdjacentHTML('beforeend', `
                <tr class="table-row">
                    <td class="py-3 px-3">
                        <div class="flex items-center gap-3">
                            <img src="${k.foto_url}" class="w-9 h-9 rounded-xl object-cover ring-1 ring-slate-100 flex-shrink-0" alt="">
                            <div>
                                <div class="font-semibold text-slate-800">${k.nama}</div>
                                <div class="text-xs text-slate-400 font-mono">${k.nip}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-3 text-xs text-slate-600 font-medium">${k.divisi}</td>
                    <td class="py-3 px-3 text-xs text-slate-600">${k.jabatan}</td>
                    <td class="py-3 px-3 text-xs text-slate-500 font-mono">${jadwal}</td>
                    <td class="py-3 px-3 text-right">
                        <button type="button" class="btn-isi-absensi inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm"
                            data-id="${k.id}" data-nama="${k.nama}" data-nip="${k.nip}"
                            data-divisi="${k.divisi}" data-jabatan="${k.jabatan}"
                            data-foto="${k.foto_url}"
                            data-jam-masuk="${jamMasuk}" data-jam-keluar="${jamKeluar}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Isi Absensi
                        </button>
                    </td>
                </tr>`);
            });
            attachButtons();
        }

        function attachButtons() {
            document.querySelectorAll('.btn-isi-absensi').forEach(btn => {
                btn.removeEventListener('click', handleBtnClick);
                btn.addEventListener('click', handleBtnClick);
            });
        }

        function handleBtnClick() { openModal(this.dataset); }
        attachButtons();

        // ── Modal ──
        function openModal(data) {
            document.getElementById('modal-foto').src = data.foto;
            document.getElementById('modal-nama').textContent    = data.nama;
            document.getElementById('modal-nip').textContent     = data.nip;
            document.getElementById('modal-divisi').textContent  = data.divisi;
            document.getElementById('modal-jabatan').textContent = data.jabatan;
            document.getElementById('input-karyawan-id').value  = data.id;
            document.getElementById('input-tanggal').value       = activeTanggal;
            document.getElementById('display-tanggal').textContent = formatTanggalID(activeTanggal);

            const jamMasuk  = data['jam-masuk']  || data.jamMasuk  || '';
            const jamKeluar = data['jam-keluar'] || data.jamKeluar || '';
            document.getElementById('input-waktu-masuk').value  = jamMasuk;
            document.getElementById('input-waktu-keluar').value = '';
            document.getElementById('hint-jam-masuk').textContent  = jamMasuk  ? `Jadwal divisi: ${jamMasuk}` : '';
            document.getElementById('hint-jam-keluar').textContent = jamKeluar ? `Jadwal divisi: ${jamKeluar}` : '';
            document.getElementById('input-status').value     = 'hadir';
            document.getElementById('input-keterangan').value = '';
            formError.classList.add('hidden');
            btnSubmit.disabled = false;
            btnSubmitLabel.textContent = 'Simpan Absensi';

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.getElementById('btn-close-modal').addEventListener('click', closeModal);
        document.getElementById('btn-cancel-modal').addEventListener('click', closeModal);
        document.getElementById('modal-backdrop').addEventListener('click', closeModal);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        // ── Submit ──
        formAbsensi.addEventListener('submit', function (e) {
            e.preventDefault();
            formError.classList.add('hidden');
            btnSubmit.disabled = true;
            btnSubmitLabel.textContent = 'Menyimpan...';

            fetch('{{ route('admin.absensi.manual') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: new FormData(formAbsensi),
            })
            .then(async r => {
                const json = await r.json();
                if (!r.ok) throw json;
                return json;
            })
            .then(res => {
                closeModal();
                showToast(res.message, true);
                loadKaryawan(activeTanggal);
            })
            .catch(err => {
                const msg = err?.message
                    || (err?.errors ? Object.values(err.errors).flat().join(' ') : null)
                    || 'Terjadi kesalahan. Silakan coba lagi.';
                formErrorMsg.textContent = msg;
                formError.classList.remove('hidden');
                btnSubmit.disabled = false;
                btnSubmitLabel.textContent = 'Simpan Absensi';
            });
        });

        // ── Toast ──
        function showToast(message, success) {
            toastMsg.textContent = message;
            toastInner.className = `flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-sm font-medium ${success ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'}`;
            toastIcon.innerHTML = success
                ? '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>';
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 4000);
        }
    })();
    </script>
    @endpush
</x-app-layout>
