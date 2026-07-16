<x-app-layout>
    <x-slot name="title">Konfigurasi Sistem</x-slot>
    <x-slot name="breadcrumb">Atur lokasi kantor, radius absensi, dan jam kerja</x-slot>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        <!-- Form Konfigurasi -->
        <div class="card">
            <h3 class="font-semibold text-slate-800 mb-5">Parameter Sistem</h3>
            <form method="POST" action="{{ route('super-admin.konfigurasi.update') }}" class="space-y-4" id="konfig-form">
                @csrf @method('PUT')

                <input type="hidden" name="lat_kantor" id="lat_kantor" value="{{ old('lat_kantor', $konfigurasi?->lat_kantor) }}">
                <input type="hidden" name="lng_kantor" id="lng_kantor" value="{{ old('lng_kantor', $konfigurasi?->lng_kantor) }}">

                <div class="p-4 bg-slate-50 rounded-xl">
                    <label class="form-label">Koordinat Kantor</label>
                    <div id="koordinat-display" class="text-sm font-mono text-slate-600 mt-1">
                        @if($konfigurasi && $konfigurasi->isKonfigured())
                            {{ $konfigurasi->lat_kantor }}, {{ $konfigurasi->lng_kantor }}
                        @else
                            <span class="text-amber-500">Klik pada peta untuk menentukan lokasi kantor</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="form-label">Radius Absensi (meter) *</label>
                    <input type="number" name="radius_meter" value="{{ old('radius_meter', $konfigurasi?->radius_meter ?? 100) }}" min="10" max="5000" class="form-input" required>
                    <p class="text-xs text-slate-500 mt-1">Karyawan hanya bisa absen jika dalam radius ini dari kantor</p>
                    @error('radius_meter')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Jam Masuk *</label>
                        <input type="time" name="jam_masuk" value="{{ old('jam_masuk', $konfigurasi?->jam_masuk ? substr($konfigurasi->jam_masuk, 0, 5) : '08:00') }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Jam Keluar *</label>
                        <input type="time" name="jam_keluar" value="{{ old('jam_keluar', $konfigurasi?->jam_keluar ? substr($konfigurasi->jam_keluar, 0, 5) : '17:00') }}" class="form-input" required>
                    </div>
                </div>

                <div>
                    <label class="form-label">Toleransi Keterlambatan (menit)</label>
                    <input type="number" name="toleransi_menit" value="{{ old('toleransi_menit', $konfigurasi?->toleransi_menit ?? 15) }}" min="0" max="120" class="form-input">
                    <p class="text-xs text-slate-500 mt-1">Dalam rentang ini setelah jam masuk masih dianggap tepat waktu</p>
                </div>

                @if($konfigurasi?->updated_by)
                    <p class="text-xs text-slate-400">Terakhir diubah: {{ $konfigurasi->updated_at->diffForHumans() }}</p>
                @endif

                <button type="submit" class="btn-primary w-full justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Konfigurasi
                </button>
            </form>
        </div>

        <!-- Leaflet Map -->
        <div class="card">
            <h3 class="font-semibold text-slate-800 mb-2">Tentukan Lokasi Kantor</h3>
            <p class="text-sm text-slate-500 mb-4">Klik pada peta untuk memilih koordinat kantor. Drag pin untuk menyesuaikan posisi.</p>
            <div id="map" class="w-full rounded-xl overflow-hidden" style="height: 400px; z-index: 1;"></div>
            <p class="text-xs text-slate-400 mt-2 text-center">© OpenStreetMap contributors</p>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
// Leaflet Map untuk pilih koordinat kantor
document.addEventListener('DOMContentLoaded', function() {
    const defaultLat = {{ $konfigurasi?->lat_kantor ?? -5.1477 }};
    const defaultLng = {{ $konfigurasi?->lng_kantor ?? 119.4328 }};

    // Load Leaflet CSS
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
    document.head.appendChild(link);

    // Load Leaflet JS
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    script.onload = function() {
        const map = L.map('map').setView([defaultLat, defaultLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let marker = null;

        // Tampilkan marker jika sudah ada koordinat tersimpan
        @if($konfigurasi && $konfigurasi->isKonfigured())
            marker = L.marker([{{ $konfigurasi->lat_kantor }}, {{ $konfigurasi->lng_kantor }}], { draggable: true })
                .addTo(map)
                .bindPopup('<b>📍 Kantor PT. KALJ</b>').openPopup();

            // Draw radius circle
            let circle = L.circle([{{ $konfigurasi->lat_kantor }}, {{ $konfigurasi->lng_kantor }}], {
                radius: {{ $konfigurasi->radius_meter }},
                color: '#0ea5e9',
                fillColor: '#0ea5e9',
                fillOpacity: 0.1,
                weight: 2
            }).addTo(map);

            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updateKoordinat(pos.lat, pos.lng);
                circle.setLatLng([pos.lat, pos.lng]);
            });
        @endif

        map.on('click', function(e) {
            const { lat, lng } = e.latlng;
            updateKoordinat(lat, lng);

            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker([lat, lng], { draggable: true })
                .addTo(map)
                .bindPopup(`<b>📍 Kantor PT. KALJ</b><br>${lat.toFixed(6)}, ${lng.toFixed(6)}`).openPopup();

            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updateKoordinat(pos.lat, pos.lng);
            });
        });

        function updateKoordinat(lat, lng) {
            document.getElementById('lat_kantor').value = lat;
            document.getElementById('lng_kantor').value = lng;
            document.getElementById('koordinat-display').innerHTML = `
                <span class="text-sky-600 font-mono">${lat.toFixed(7)}, ${lng.toFixed(7)}</span>
                <span class="ml-2 text-emerald-500 text-xs">✓ Dipilih</span>
            `;
        }
    };
    document.head.appendChild(script);
});
</script>
@endpush
