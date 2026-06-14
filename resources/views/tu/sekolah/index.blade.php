@extends('layouts.tu')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-teal-primary-dark flex items-center gap-2">
            <x-heroicon-o-building-office-2 class="w-7 h-7" />
            Profil Sekolah
        </h1>
        <p class="mt-1 text-sm text-gray-500">Kelola informasi dan identitas sekolah.</p>
    </div>

    <form method="POST" action="{{ route('tu.sekolah.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        {{-- Logo Sekolah --}}
        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Logo Sekolah</h2>
            <div class="flex flex-col sm:flex-row gap-6 items-start">
                <div class="flex-shrink-0">
                    <div class="w-36 h-36 rounded-card border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden bg-gray-50">
                        @if($sekolah->logo)
                            <img id="logo-preview" src="{{ asset('storage/'.$sekolah->logo) }}" alt="Logo" class="w-full h-full object-contain">
                        @else
                            <img id="logo-preview" src="" alt="" class="w-full h-full object-contain hidden">
                            <div id="logo-placeholder" class="text-center">
                                <x-heroicon-o-photo class="w-10 h-10 text-gray-300 mx-auto" />
                                <p class="text-xs text-gray-400 mt-1">Belum ada logo</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex-1 space-y-3">
                    <div>
                        <x-input-label for="logo" value="Upload Logo Baru" />
                        <input type="file" id="logo" name="logo" accept="image/jpeg,image/png"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-teal-primary/10 file:text-teal-primary hover:file:bg-teal-primary/20"
                            onchange="previewLogo(this)">
                        <x-input-error :messages="$errors->get('logo')" class="mt-1" />
                        <p class="text-xs text-gray-400 mt-1">Format: JPG/PNG, maks 5MB. Favicon akan otomatis dibuat dari logo.</p>
                    </div>
                    @if($sekolah->logo)
                        <label class="flex items-center gap-2 text-sm text-coral cursor-pointer">
                            <input type="checkbox" name="hapus_logo" value="1" class="rounded border-coral text-coral focus:ring-coral">
                            Hapus logo saat ini
                        </label>
                        <p class="text-xs text-gray-400">File: {{ basename($sekolah->logo) }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Logo Provinsi --}}
        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-gold">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Logo Provinsi</h2>
            <div class="flex flex-col sm:flex-row gap-6 items-start">
                <div class="flex-shrink-0">
                    <div class="w-36 h-36 rounded-card border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden bg-gray-50">
                        @if($sekolah->logo_prov)
                            <img id="logo-prov-preview" src="{{ asset('storage/'.$sekolah->logo_prov) }}" alt="Logo Provinsi" class="w-full h-full object-contain">
                        @else
                            <img id="logo-prov-preview" src="" alt="" class="w-full h-full object-contain hidden">
                            <div id="logo-prov-placeholder" class="text-center">
                                <x-heroicon-o-photo class="w-10 h-10 text-gray-300 mx-auto" />
                                <p class="text-xs text-gray-400 mt-1">Belum ada logo</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex-1 space-y-3">
                    <div>
                        <x-input-label for="logo_prov" value="Upload Logo Provinsi Baru" />
                        <input type="file" id="logo_prov" name="logo_prov" accept="image/jpeg,image/png"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-gold/10 file:text-gold hover:file:bg-gold/20"
                            onchange="previewLogoProv(this)">
                        <x-input-error :messages="$errors->get('logo_prov')" class="mt-1" />
                        <p class="text-xs text-gray-400 mt-1">Format: JPG/PNG, maks 5MB. Logo ini ditampilkan di header rapor.</p>
                    </div>
                    @if($sekolah->logo_prov)
                        <label class="flex items-center gap-2 text-sm text-coral cursor-pointer">
                            <input type="checkbox" name="hapus_logo_prov" value="1" class="rounded border-coral text-coral focus:ring-coral">
                            Hapus logo provinsi saat ini
                        </label>
                        <p class="text-xs text-gray-400">File: {{ basename($sekolah->logo_prov) }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Identitas Sekolah --}}
        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-teal-primary">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Identitas Sekolah</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="npsn" value="NPSN" />
                    <x-text-input id="npsn" name="npsn" :value="old('npsn', $sekolah->npsn)" class="block w-full mt-1" required />
                    <x-input-error :messages="$errors->get('npsn')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="nama_sekolah" value="Nama Sekolah" />
                    <x-text-input id="nama_sekolah" name="nama_sekolah" :value="old('nama_sekolah', $sekolah->nama_sekolah)" class="block w-full mt-1" required />
                    <x-input-error :messages="$errors->get('nama_sekolah')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" :value="old('email', $sekolah->email)" class="block w-full mt-1" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="kontak" value="Kontak" />
                    <x-text-input id="kontak" name="kontak" :value="old('kontak', $sekolah->kontak)" class="block w-full mt-1" />
                    <x-input-error :messages="$errors->get('kontak')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="website" value="Website" />
                    <x-text-input id="website" name="website" type="url" :value="old('website', $sekolah->website)" class="block w-full mt-1" />
                    <x-input-error :messages="$errors->get('website')" class="mt-1" />
                </div>
            </div>
        </div>

        {{-- Alamat --}}
        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-gold">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Alamat</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-input-label for="alamat" value="Alamat Lengkap" />
                    <textarea id="alamat" name="alamat" rows="2" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">{{ old('alamat', $sekolah->alamat) }}</textarea>
                    <x-input-error :messages="$errors->get('alamat')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="desa" value="Desa/Kelurahan" />
                    <x-text-input id="desa" name="desa" :value="old('desa', $sekolah->desa)" class="block w-full mt-1" />
                </div>
                <div>
                    <x-input-label for="kecamatan" value="Kecamatan" />
                    <x-text-input id="kecamatan" name="kecamatan" :value="old('kecamatan', $sekolah->kecamatan)" class="block w-full mt-1" />
                </div>
                <div>
                    <x-input-label for="kabupaten" value="Kabupaten" />
                    <x-text-input id="kabupaten" name="kabupaten" :value="old('kabupaten', $sekolah->kabupaten)" class="block w-full mt-1" />
                </div>
                <div>
                    <x-input-label for="provinsi" value="Provinsi" />
                    <x-text-input id="provinsi" name="provinsi" :value="old('provinsi', $sekolah->provinsi)" class="block w-full mt-1" />
                </div>
            </div>
        </div>

        {{-- Visi & Misi --}}
        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-sky">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-4">Visi & Misi</h2>
            <div class="space-y-4">
                <div>
                    <x-input-label for="visi" value="Visi" />
                    <textarea id="visi" name="visi" rows="3" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">{{ old('visi', $sekolah->visi) }}</textarea>
                    <x-input-error :messages="$errors->get('visi')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="misi" value="Misi" />
                    <textarea id="misi" name="misi" rows="5" class="mt-1 block w-full border-teal-primary/20 rounded-card focus:border-teal-primary focus:ring-2 focus:ring-teal-primary/20">{{ old('misi', $sekolah->misi) }}</textarea>
                    <x-input-error :messages="$errors->get('misi')" class="mt-1" />
                </div>
            </div>
        </div>

        {{-- GPS & Lokasi Sekolah --}}
        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-emerald-500">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-1">GPS & Lokasi Sekolah</h2>
            <p class="text-xs text-gray-400 mb-4">Atur titik lokasi sekolah untuk validasi absensi berbasis GPS.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Map --}}
                <div class="md:col-span-2">
                    <div id="map" style="width:100%;height:400px;border:1px solid #e5e7eb;border-radius:12px;position:relative;z-index:1;"></div>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <button type="button" onclick="getCurrentLocation()"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;font-size:12px;font-weight:600;color:#fff;background:#10b981;border:none;border-radius:8px;cursor:pointer;"
                            onmouseover="this.style.background='#059669'"
                            onmouseout="this.style.background='#10b981'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                            Lokasi Saat Ini
                        </button>
                        <button type="button" onclick="clearLocation()"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;font-size:12px;font-weight:600;color:#374151;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer;"
                            onmouseover="this.style.background='#e5e7eb'"
                            onmouseout="this.style.background='#f3f4f6'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                            Hapus Pin
                        </button>
                        <span id="map-status" style="font-size:12px;color:#9ca3af;align-self:center;"></span>
                    </div>
                </div>

                {{-- Latitude --}}
                <div>
                    <x-input-label for="latitude" value="Latitude" />
                    <x-text-input id="latitude" name="latitude" type="number" step="any" min="-90" max="90"
                        :value="old('latitude', $sekolah->latitude)" class="block w-full mt-1" placeholder="-7.1234567" readonly />
                    <x-input-error :messages="$errors->get('latitude')" class="mt-1" />
                </div>

                {{-- Longitude --}}
                <div>
                    <x-input-label for="longitude" value="Longitude" />
                    <x-text-input id="longitude" name="longitude" type="number" step="any" min="-180" max="180"
                        :value="old('longitude', $sekolah->longitude)" class="block w-full mt-1" placeholder="112.1234567" readonly />
                    <x-input-error :messages="$errors->get('longitude')" class="mt-1" />
                </div>
            </div>
        </div>

        {{-- Pengaturan Absensi --}}
        <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-gold">
            <h2 class="text-lg font-extrabold text-teal-primary-dark mb-1">Pengaturan Absensi</h2>
            <p class="text-xs text-gray-400 mb-4">Konfigurasi radius dan jam kerja untuk absensi GPS guru & TU.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Radius --}}
                <div>
                    <x-input-label for="radius_absen" value="Radius Absensi (meter)" />
                    <div class="relative mt-1">
                        <x-text-input id="radius_absen" name="radius_absen" type="number" min="10" max="5000"
                            :value="old('radius_absen', $sekolah->radius_absen ?? 100)" class="block w-full" />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-400 text-sm">m</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">10 - 5.000 meter. Default: 100m</p>
                    <x-input-error :messages="$errors->get('radius_absen')" class="mt-1" />
                </div>

                {{-- Jam Masuk --}}
                <div>
                    <x-input-label for="jam_masuk" value="Jam Masuk" />
                    <x-text-input id="jam_masuk" name="jam_masuk" type="time"
                        :value="old('jam_masuk', $sekolah->jam_masuk ?? '07:00')" class="block w-full mt-1" />
                    <p class="text-xs text-gray-400 mt-1">Batas waktu check-in</p>
                    <x-input-error :messages="$errors->get('jam_masuk')" class="mt-1" />
                </div>

                {{-- Jam Pulang --}}
                <div>
                    <x-input-label for="jam_pulang" value="Jam Pulang" />
                    <x-text-input id="jam_pulang" name="jam_pulang" type="time"
                        :value="old('jam_pulang', $sekolah->jam_pulang ?? '15:00')" class="block w-full mt-1" />
                    <p class="text-xs text-gray-400 mt-1">Batas waktu check-out</p>
                    <x-input-error :messages="$errors->get('jam_pulang')" class="mt-1" />
                </div>
            </div>

            {{-- Radius Preview --}}
            <div class="mt-4 p-3 bg-gray-50 rounded-card">
                <p class="text-xs text-gray-500">
                    <strong>Info:</strong> Guru & TU yang berada dalam radius
                    <span id="radius-preview" class="font-bold text-emerald-600">{{ $sekolah->radius_absen ?? 100 }}</span>
                    meter dari lokasi sekolah akan dianggap valid untuk absensi.
                    Status "tepat_waktu" jika check-in sebelum jam
                    <span class="font-bold text-emerald-600">{{ $sekolah->jam_masuk ?? '07:00' }}</span>.
                </p>
            </div>
        </div>

        <div class="flex justify-end">
            <x-primary-button>Simpan Perubahan</x-primary-button>
        </div>
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.locatecontrol@0.79.0/dist/L.Control.Locate.min.js" crossorigin=""></script>

<script>
    const defaultLat = {{ $sekolah->latitude ?? '-6.9' }};
    const defaultLng = {{ $sekolah->longitude ?? '112.0' }};
    const defaultRadius = {{ $sekolah->radius_absen ?? 100 }};

    let map, marker, circle;

    function initMap() {
        map = L.map('map', { zoomControl: false }).setView([defaultLat, defaultLng], 16);

        L.control.zoom({ position: 'topright' }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.control.locate({
            position: 'topright',
            setView: false,
            keepCurrentZoomLevel: true,
            showCompass: true,
            markerStyle: { color: '#10b981' },
            circleStyle: { color: '#10b981', fillColor: '#10b981', fillOpacity: 0.05 }
        }).addTo(map);

        if (document.getElementById('latitude').value && document.getElementById('longitude').value) {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);
            addMarker(lat, lng);
            map.setView([lat, lng], 17);
        }

        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            document.getElementById('latitude').value = lat.toFixed(7);
            document.getElementById('longitude').value = lng.toFixed(7);
            addMarker(lat, lng);
            updateRadius();
        });
    }

    function addMarker(lat, lng) {
        if (marker) map.removeLayer(marker);
        if (circle) map.removeLayer(circle);

        marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            document.getElementById('latitude').value = pos.lat.toFixed(7);
            document.getElementById('longitude').value = pos.lng.toFixed(7);
            updateRadius();
        });

        updateRadius();
    }

    function updateRadius() {
        if (circle) map.removeLayer(circle);

        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);
        const radius = parseInt(document.getElementById('radius_absen').value) || 100;

        if (isNaN(lat) || isNaN(lng)) return;

        circle = L.circle([lat, lng], {
            radius: radius,
            color: '#10b981',
            fillColor: '#10b981',
            fillOpacity: 0.1,
            weight: 2
        }).addTo(map);

        document.getElementById('radius-preview').textContent = radius;
    }

    function getCurrentLocation() {
        const status = document.getElementById('map-status');
        status.textContent = 'Mengambil lokasi...';
        status.classList.remove('hidden');

        if (!navigator.geolocation) {
            status.textContent = 'Browser tidak mendukung geolocation';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                document.getElementById('latitude').value = lat.toFixed(7);
                document.getElementById('longitude').value = lng.toFixed(7);
                map.setView([lat, lng], 17);
                addMarker(lat, lng);
                status.textContent = 'Lokasi berhasil diambil!';
                setTimeout(() => { status.textContent = ''; }, 2000);
            },
            function(err) {
                status.textContent = 'Gagal: ' + err.message;
                setTimeout(() => { status.textContent = ''; }, 3000);
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    function clearLocation() {
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        if (marker) { map.removeLayer(marker); marker = null; }
        if (circle) { map.removeLayer(circle); circle = null; }
    }

    function previewLogo(input) {
        const preview = document.getElementById('logo-preview');
        const placeholder = document.getElementById('logo-placeholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewLogoProv(input) {
        const preview = document.getElementById('logo-prov-preview');
        const placeholder = document.getElementById('logo-prov-placeholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            initMap();
            map.invalidateSize();
        }, 100);

        document.getElementById('radius_absen').addEventListener('input', updateRadius);
    });
</script>
@endsection
