<x-app-layout>
    <x-slot name="title">Konfigurasi Sistem</x-slot>
    <x-slot name="breadcrumb">Atur lokasi kantor, radius absensi, dan jam kerja</x-slot>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
        
        <!-- Parameter Form -->
        <div class="xl:col-span-5 card">
            <div class="flex items-center gap-2 mb-5 border-b border-slate-100 pb-3">
                <div class="w-2.5 h-6 bg-[#0056B3] rounded-full"></div>
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Parameter Kantor & Waktu</h3>
            </div>

            <form method="POST" action="{{ route('super-admin.konfigurasi.update') }}" class="space-y-4" id="konfig-form">
                @csrf @method('PUT')

                <input type="hidden" name="lat_kantor" id="lat_kantor" value="{{ old('lat_kantor', $konfigurasi?->lat_kantor) }}">
                <input type="hidden" name="lng_kantor" id="lng_kantor" value="{{ old('lng_kantor', $konfigurasi?->lng_kantor) }}">

                <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-xl">
                    <span class="form-label text-[10px]">Koordinat Terpilih</span>
                    <div id="koordinat-display" class="text-xs font-mono font-bold text-slate-700 mt-1 flex items-center gap-1.5">
                        @if($konfigurasi && $konfigurasi->isKonfigured())
                            <span class="text-[#0056B3]">{{ $konfigurasi->lat_kantor }}, {{ $konfigurasi->lng_kantor }}</span>
                            <span class="text-emerald-500 font-sans text-[10px]">✓ Aktif</span>
                        @else
                            <span class="text-amber-600">⚠️ Klik pada peta untuk memilih lokasi</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="form-label" for="radius_meter">Radius Absensi (meter) *</label>
                    <input type="number" id="radius_meter" name="radius_meter" value="{{ old('radius_meter', $konfigurasi?->radius_meter ?? 100) }}" min="10" max="5000" class="form-input" required>
                    <p class="text-[10px] text-slate-400 font-medium mt-1">Karyawan wajib berada dalam radius ini untuk dapat merekam absensi</p>
                    @error('radius_meter')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label" for="jam_masuk">Jam Masuk *</label>
                        <input type="time" id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk', $konfigurasi?->jam_masuk ? substr($konfigurasi->jam_masuk, 0, 5) : '08:00') }}" class="form-input" required>
                        @error('jam_masuk')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="jam_keluar">Jam Keluar *</label>
                        <input type="time" id="jam_keluar" name="jam_keluar" value="{{ old('jam_keluar', $konfigurasi?->jam_keluar ? substr($konfigurasi->jam_keluar, 0, 5) : '17:00') }}" class="form-input" required>
                        @error('jam_keluar')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="form-label" for="toleransi_menit">Toleransi Keterlambatan (menit) *</label>
                    <input type="number" id="toleransi_menit" name="toleransi_menit" value="{{ old('toleransi_menit', $konfigurasi?->toleransi_menit ?? 15) }}" min="0" max="120" class="form-input" required>
                    <p class="text-[10px] text-slate-400 font-medium mt-1">Rentang waktu setelah jam masuk yang masih dianggap tepat waktu</p>
                    @error('toleransi_menit')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                @if($konfigurasi?->updated_by)
                    <div class="text-[10px] text-slate-400 font-medium pt-1">
                        Terakhir diupdate oleh: <span class="text-slate-600 font-bold">{{ $konfigurasi->updater?->nama ?? 'Sistem' }}</span> ({{ $konfigurasi->updated_at->diffForHumans() }})
                    </div>
                @endif

                <button type="submit" class="btn-primary w-full justify-center py-3 mt-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2v-9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Simpan Konfigurasi
                </button>
            </form>
        </div>

        <!-- Peta Kantor -->
        <div class="xl:col-span-7 card" x-data="mapSearchApp()">
            <div class="flex items-center gap-2 mb-4 border-b border-slate-100 pb-3">
                <div class="w-2.5 h-6 bg-[#C8102E] rounded-full"></div>
                <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wide">Peta Lokasi Kantor</h3>
            </div>
            
            <p class="text-xs text-slate-500 mb-4 font-medium">Cari alamat lewat kotak pencarian, klik tombol lokasi saya, atau klik langsung pada peta.</p>

            <!-- Search Address Bar -->
            <div class="flex gap-2 mb-4 relative">
                <div class="flex-1 relative">
                    <input type="text" x-model="searchQuery" @input.debounce.500ms="searchAddress()" placeholder="Cari alamat atau nama tempat... (min. 3 huruf)" class="form-input pl-10 pr-4">
                    <div class="absolute left-3.5 top-3.5 text-slate-400">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div x-show="showDropdown && results.length > 0" class="absolute left-0 right-0 mt-1 bg-white border border-slate-200 rounded-xl shadow-lg z-50 max-h-60 overflow-y-auto text-xs" style="display: none;" @click.outside="showDropdown = false">
                        <template x-for="item in results" :key="item.place_id">
                            <button type="button" @click="selectAddress(item)" class="w-full text-left px-4 py-2.5 hover:bg-slate-50 border-b border-slate-100 last:border-0 font-medium text-slate-700 block truncate">
                                <span x-text="item.display_name"></span>
                            </button>
                        </template>
                    </div>
                </div>
                <button type="button" id="btn-lokasi-saya" class="btn-secondary whitespace-nowrap gap-1.5 font-bold">
                    <svg class="w-4 h-4 text-[#0056B3]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Lokasi Saya
                </button>
            </div>

            <!-- Latitude & Longitude Manual Inputs -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label" for="input-latitude">Latitude</label>
                    <input type="number" step="any" id="input-latitude" class="form-input font-mono font-bold text-slate-700" placeholder="Contoh: -5.14770">
                </div>
                <div>
                    <label class="form-label" for="input-longitude">Longitude</label>
                    <input type="number" step="any" id="input-longitude" class="form-input font-mono font-bold text-slate-700" placeholder="Contoh: 119.43280">
                </div>
            </div>
            
            <!-- Map Container (Pastikan height didefinisikan secara langsung agar tidak runtuh menjadi 0) -->
            <div id="map" class="w-full rounded-2xl border border-slate-200" style="height: 420px; min-height: 420px; z-index: 10;"></div>

            <div class="flex justify-between items-center mt-3 text-[10px] text-slate-400 font-semibold uppercase tracking-wider">
                <span>© OpenStreetMap Contributors</span>
                <span>PT. KALJ GPS System</span>
            </div>
        </div>
    </div>
@push('scripts')
<script>
const registerMapSearchApp = () => {
    if (window.Alpine) {
        window.Alpine.data('mapSearchApp', () => ({
            searchQuery: '',
            results: [],
            showDropdown: false,
            
            async searchAddress() {
                if (this.searchQuery.length < 3) {
                    this.results = [];
                    this.showDropdown = false;
                    return;
                }
                try {
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&limit=5&q=${encodeURIComponent(this.searchQuery)}`);
                    this.results = await response.json();
                    this.showDropdown = true;
                } catch (e) {
                    console.error('Nominatim Geocoding error:', e);
                }
            },
            
            selectAddress(item) {
                const lat = parseFloat(item.lat);
                const lon = parseFloat(item.lon);
                this.showDropdown = false;
                this.searchQuery = item.display_name;
                
                // Dispatch event to Leaflet handler
                window.dispatchEvent(new CustomEvent('map-move-to', { detail: { lat, lng: lon } }));
            }
        }));
    }
};

if (window.Alpine) {
    registerMapSearchApp();
} else {
    document.addEventListener('alpine:init', registerMapSearchApp);
}


document.addEventListener('DOMContentLoaded', function() {
    const defaultLat = {{ $konfigurasi?->lat_kantor ?? -5.1477 }};
    const defaultLng = {{ $konfigurasi?->lng_kantor ?? 119.4328 }};
    const radiusInput = document.getElementById('radius_meter');
    const inputLat = document.getElementById('input-latitude');
    const inputLng = document.getElementById('input-longitude');

    // Inisialisasi Peta
    const map = L.map('map').setView([defaultLat, defaultLng], 16);

    // Gunakan CartoDB Voyager tile layer
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
        attribution: '© OpenStreetMap, © CartoDB'
    }).addTo(map);

    let marker = null;
    let circle = null;

    // Sinkronisasi inisiasi koordinat ke input atas
    @if($konfigurasi && $konfigurasi->isKonfigured())
        inputLat.value = defaultLat;
        inputLng.value = defaultLng;
        
        marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map)
            .bindPopup('<b class="text-xs text-slate-800 font-bold">📍 Kantor PT. KALJ</b>').openPopup();

        circle = L.circle([defaultLat, defaultLng], {
            radius: parseInt(radiusInput.value) || 100,
            color: '#C8102E',
            fillColor: '#0056B3',
            fillOpacity: 0.15,
            weight: 2
        }).addTo(map);

        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateKoordinat(pos.lat, pos.lng);
        });
    @endif

    // Update marker, circle, & map viewport ke koordinat baru
    function setMapLocation(lat, lng, zoom = 16) {
        map.setView([lat, lng], zoom);
        updateKoordinat(lat, lng);

        if (marker) {
            marker.setLatLng([lat, lng]).openPopup();
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map)
                .bindPopup('<b class="text-xs text-slate-800 font-bold">📍 Kantor PT. KALJ</b>').openPopup();
            
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updateKoordinat(pos.lat, pos.lng);
            });
        }

        const radiusVal = parseInt(radiusInput.value) || 100;
        if (circle) {
            circle.setLatLng([lat, lng]);
            circle.setRadius(radiusVal);
        } else {
            circle = L.circle([lat, lng], {
                radius: radiusVal,
                color: '#C8102E',
                fillColor: '#0056B3',
                fillOpacity: 0.15,
                weight: 2
            }).addTo(map);
        }
    }

    // Klik Peta
    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        setMapLocation(lat, lng);
    });

    // Listener input manual Latitude & Longitude
    function handleManualInput() {
        const lat = parseFloat(inputLat.value);
        const lng = parseFloat(inputLng.value);
        if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
            setMapLocation(lat, lng);
        }
    }
    inputLat.addEventListener('change', handleManualInput);
    inputLng.addEventListener('change', handleManualInput);

    // Tombol Lokasi Saya
    const btnLokasiSaya = document.getElementById('btn-lokasi-saya');
    btnLokasiSaya.addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Geolocation tidak didukung oleh browser Anda.');
            return;
        }
        btnLokasiSaya.innerHTML = '⌛ Mencari...';
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                setMapLocation(lat, lng, 18);
                btnLokasiSaya.innerHTML = `
                    <svg class="w-4 h-4 text-[#0056B3]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Lokasi Saya
                `;
            },
            (err) => {
                alert('Gagal mendapatkan lokasi Anda. Pastikan GPS aktif dan izin lokasi diberikan.');
                btnLokasiSaya.innerHTML = `
                    <svg class="w-4 h-4 text-[#0056B3]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Lokasi Saya
                `;
            },
            { enableHighAccuracy: true }
        );
    });

    // Listener dari pencarian alamat Autocomplete
    window.addEventListener('map-move-to', (e) => {
        const { lat, lng } = e.detail;
        setMapLocation(lat, lng, 18);
    });

    // Update radius circle ketika input radius berubah
    radiusInput.addEventListener('input', function() {
        const radiusVal = parseInt(this.value) || 100;
        if (circle) {
            circle.setRadius(radiusVal);
        }
    });

    function updateKoordinat(lat, lng) {
        const latFixed = lat.toFixed(7);
        const lngFixed = lng.toFixed(7);
        
        // Update form hidden inputs
        document.getElementById('lat_kantor').value = latFixed;
        document.getElementById('lng_kantor').value = lngFixed;
        
        // Update manual inputs
        inputLat.value = latFixed;
        inputLng.value = lngFixed;
        
        // Update display text
        document.getElementById('koordinat-display').innerHTML = `
            <span class="text-[#0056B3] font-mono">${latFixed}, ${lngFixed}</span>
            <span class="ml-2 text-emerald-600 text-[10px] font-sans">✓ Terpilih</span>
        `;
        if (circle) {
            circle.setLatLng([lat, lng]);
        }
    }

    const refreshMap = () => {
        map.invalidateSize();
    };

    [100, 300, 600, 1200, 2500].forEach(delay => {
        setTimeout(refreshMap, delay);
    });

    window.addEventListener('resize', refreshMap);
    window.addEventListener('load', refreshMap);
});
</script>
@endpush
</x-app-layout>
