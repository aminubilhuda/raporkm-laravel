@extends('layouts.guru')
@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol@0.79.0/dist/L.Control.Locate.min.css" />
<style>
    #map { height: 400px; width: 100%; border-radius: 12px; position: relative; }
    .webcam-container { position: relative; width: 320px; height: 240px; border-radius: 12px; overflow: hidden; }
    #webcam { width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }
    .leaflet-control-zoom { z-index: 1000 !important; }
</style>

<div class="space-y-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-coral-dark flex items-center gap-2">
            <x-heroicon-o-map-pin class="w-7 h-7" /> Absensi GPS
        </h1>
        <p class="mt-1 text-sm text-gray-500">Check-in dan check-out dengan lokasi GPS & selfie.</p>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-card">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    @if(session('status'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-card">
        {{ session('status') }}
    </div>
    @endif

    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-coral">
        <h2 class="font-extrabold text-coral-dark mb-4">Lokasi Sekolah</h2>
        <div id="map" style="position: relative;"></div>
        <div class="mt-4 flex gap-2">
            <button type="button" onclick="getMyLocation()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 text-white rounded-lg text-sm font-bold">
                <x-heroicon-m-map-pin class="w-4 h-4" /> Lokasi Saya
            </button>
            <button type="button" onclick="startWebcam()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-bold">
                <x-heroicon-m-camera class="w-4 h-4" /> Aktifkan Kamera
            </button>
        </div>
        <div id="location-info" class="mt-3 text-sm text-gray-600"></div>
    </div>

    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-coral">
        <h2 class="font-extrabold text-coral-dark mb-4">Formulir Absensi</h2>

        @if($presensi)
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-card">
                <p class="font-bold text-blue-800">Status Hari Ini:</p>
                @if($presensi->check_in)
                    <p class="text-sm mt-2">✅ Check-in: {{ $presensi->check_in }} ({{ $presensi->status_check_in }})</p>
                @endif
                @if($presensi->check_out)
                    <p class="text-sm mt-1">✅ Check-out: {{ $presensi->check_out }} ({{ $presensi->status_check_out }})</p>
                @endif
            </div>
        @endif

        <form id="absensiForm" method="POST" action="{{ route('guru.absensi-guru.check-in') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="latitude" id="input-latitude">
            <input type="hidden" name="longitude" id="input-longitude">
            <input type="hidden" name="foto_selfie_in" id="input-foto-selfie-in">
            <input type="hidden" name="foto_selfie_out" id="input-foto-selfie-out">
            <input type="hidden" name="latitude_out" id="input-latitude-out">
            <input type="hidden" name="longitude_out" id="input-longitude-out">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-extrabold uppercase text-coral-dark mb-1">Latitude</label>
                    <input type="text" id="display-lat" readonly class="block w-full border-coral/20 rounded-card bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-extrabold uppercase text-coral-dark mb-1">Longitude</label>
                    <input type="text" id="display-lng" readonly class="block w-full border-coral/20 rounded-card bg-gray-50">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-extrabold uppercase text-coral-dark mb-1">Foto Selfie</label>
                <div class="webcam-container border-2 border-dashed border-coral/30 rounded-card" style="width:320px;height:240px;">
                    <video id="webcam" autoplay playsinline></video>
                    <canvas id="webcam-canvas" style="display:none;"></canvas>
                </div>
                <div id="foto-preview" class="mt-2 hidden">
                    <img id="preview-img" class="w-32 h-24 object-cover rounded-card border">
                    <p id="foto-size" class="text-xs text-gray-500 mt-1"></p>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-extrabold uppercase text-coral-dark mb-1">Keterangan (opsional)</label>
                <textarea name="keterangan" rows="2" class="block w-full border-coral/20 rounded-card" placeholder="Catatan untuk absensi..."></textarea>
            </div>

            <div class="mt-6 flex gap-3">
                @if(!$presensi || !$presensi->check_in)
                    <button type="button" onclick="submitCheckIn()" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 text-white rounded-card font-bold text-sm hover:bg-emerald-600 transition">
                        <x-heroicon-m-arrow-right-on-rectangle class="w-5 h-5" /> Check-in
                    </button>
                @elseif(!$presensi->check_out)
                    <button type="button" onclick="submitCheckOut()" class="inline-flex items-center gap-2 px-6 py-3 bg-orange-500 text-white rounded-card font-bold text-sm hover:bg-orange-600 transition">
                        <x-heroicon-m-arrow-left-on-rectangle class="w-5 h-5" /> Check-out
                    </button>
                @else
                    <span class="text-green-600 font-bold">✅ Absensi hari ini sudah selesai.</span>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-card shadow-card p-5 md:p-6 border-l-[6px] border-l-coral">
        <h2 class="font-extrabold text-coral-dark mb-4">Riwayat Absensi (30 Hari Terakhir)</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface-base text-left">
                    <tr>
                        <th class="px-4 py-3 font-extrabold text-xs uppercase">Tanggal</th>
                        <th class="px-4 py-3 font-extrabold text-xs uppercase">Check-in</th>
                        <th class="px-4 py-3 font-extrabold text-xs uppercase">Status In</th>
                        <th class="px-4 py-3 font-extrabold text-xs uppercase">Check-out</th>
                        <th class="px-4 py-3 font-extrabold text-xs uppercase">Status Out</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($riwayat as $r)
                    <tr class="hover:bg-coral-bg/30">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $r->check_in ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold
                                {{ $r->status_check_in === 'tepat_waktu' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $r->status_check_in === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $r->check_out ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($r->status_check_out === 'pulang_tepat')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Tepat</span>
                            @elseif($r->status_check_out === 'pulang_cepat')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Cepat</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada riwayat absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.locatecontrol@0.79.0/dist/L.Control.Locate.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sekolahLat = {{ $sekolah?->latitude ?? -6.9 }};
    const sekolahLng = {{ $sekolah?->longitude ?? 112.0 }};
    const radius = {{ $sekolah?->radius_absen ?? 100 }};

    const map = L.map('map').setView([sekolahLat, sekolahLng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // School location marker
    const schoolMarker = L.marker([sekolahLat, sekolahLng]).addTo(map)
        .bindPopup('<b>Lokasi Sekolah</b><br>Radius: ' + radius + 'm').openPopup();

    // Radius circle
    L.circle([sekolahLat, sekolahLng], {
        color: '#10b981',
        fillColor: '#10b981',
        fillOpacity: 0.15,
        radius: radius
    }).addTo(map);

    // User location marker (will be set)
    let userMarker = null;
    let currentLat = null;
    let currentLng = null;

    // Geolocation control
    L.control.locate({
        position: 'topleft',
        strings: { title: "Lokasi Saya" },
        locateOptions: { enableHighAccuracy: true }
    }).addTo(map);

    function getMyLocation() {
        if (!navigator.geolocation) {
            document.getElementById('location-info').innerHTML = '<span class="text-red-600 font-bold">❌ Geolocation tidak didukung browser ini.</span>';
            return;
        }

        document.getElementById('location-info').innerHTML = '⏳ Mencari lokasi...';

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                currentLat = pos.coords.latitude;
                currentLng = pos.coords.longitude;

                document.getElementById('input-latitude').value = currentLat;
                document.getElementById('input-longitude').value = currentLng;
                document.getElementById('input-latitude-out').value = currentLat;
                document.getElementById('input-longitude-out').value = currentLng;
                document.getElementById('display-lat').value = currentLat.toFixed(6);
                document.getElementById('display-lng').value = currentLng.toFixed(6);

                if (userMarker) {
                    userMarker.setLatLng([currentLat, currentLng]);
                } else {
                    userMarker = L.marker([currentLat, currentLng], {
                        icon: L.divIcon({
                            className: 'user-marker',
                            html: '<div style="background:#3b82f6;width:16px;height:16px;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);"></div>',
                            iconSize: [16, 16],
                            iconAnchor: [8, 8]
                        })
                    }).addTo(map).bindPopup('Lokasi Anda');
                }

                map.setView([currentLat, currentLng], 16);

                const dist = haversineDistance(currentLat, currentLng, sekolahLat, sekolahLng);
                document.getElementById('location-info').innerHTML =
                    '📍 Lokasi ditemukan: <strong>' + currentLat.toFixed(6) + ', ' + currentLng.toFixed(6) + '</strong><br>' +
                    '📏 Jarak ke sekolah: <strong>' + Math.round(dist) + 'm</strong>' +
                    (dist <= radius ? ' <span class="text-green-600 font-bold">✅ Dalam radius</span>' : ' <span class="text-red-600 font-bold">❌ Di luar radius</span>');
            },
            (err) => {
                var msg = '';
                switch (err.code) {
                    case err.PERMISSION_DENIED:
                        msg = '❌ Akses lokasi ditolak. Silakan izinkan akses lokasi di browser.';
                        break;
                    case err.POSITION_UNAVAILABLE:
                        msg = '❌ Informasi lokasi tidak tersedia.';
                        break;
                    case err.TIMEOUT:
                        msg = '❌ Waktu permintaan lokasi habis. Silakan coba lagi.';
                        break;
                    default:
                        msg = '❌ Gagal mendapatkan lokasi: ' + err.message;
                }
                document.getElementById('location-info').innerHTML = msg;
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    }

    function haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3;
        const phi1 = lat1 * Math.PI/180;
        const phi2 = lat2 * Math.PI/180;
        const dPhi = (lat2-lat1) * Math.PI/180;
        const dLambda = (lon2-lon1) * Math.PI/180;
        const a = Math.sin(dPhi/2)**2 + Math.cos(phi1)*Math.cos(phi2)*Math.sin(dLambda/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    // Webcam
    let videoStream = null;

    function startWebcam() {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } })
            .then(stream => {
                videoStream = stream;
                document.getElementById('webcam').srcObject = stream;
            })
            .catch(err => {
                alert('Gagal mengakses kamera: ' + err.message);
            });
    }

    function capturePhoto() {
        const video = document.getElementById('webcam');
        const canvas = document.getElementById('webcam-canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0);

        return new Promise((resolve) => {
            // Compress to 50-80KB using canvas
            let quality = 0.7;
            let dataUrl;
            do {
                dataUrl = canvas.toDataURL('image/jpeg', quality);
                quality -= 0.05;
            } while (dataUrl.length * 0.75 > 80000 && quality > 0.1);

            resolve(dataUrl);
        });
    }

    async function submitCheckIn() {
        if (!currentLat || !currentLng) {
            alert('Harap dapatkan lokasi GPS terlebih dahulu!');
            return;
        }

        // Start webcam if not started
        if (!videoStream) {
            await startWebcam();
            // Wait a moment for camera to initialize
            await new Promise(r => setTimeout(r, 500));
        }

        const photo = await capturePhoto();
        const blob = await fetch(photo).then(r => r.blob());

        // Create FormData
        const formData = new FormData(document.getElementById('absensiForm'));
        formData.set('foto_selfie_in', blob, 'selfie.jpg');

        try {
            const response = await fetch('{{ route("guru.absensi-guru.check-in") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            if (response.ok) {
                window.location.reload();
            } else {
                alert(data.errors ? Object.values(data.errors).flat().join('\n') : 'Gagal check-in');
            }
        } catch (e) {
            alert('Terjadi kesalahan: ' + e.message);
        }
    }

    async function submitCheckOut() {
        if (!currentLat || !currentLng) {
            alert('Harap dapatkan lokasi GPS terlebih dahulu!');
            return;
        }

        const photo = await capturePhoto();
        const blob = await fetch(photo).then(r => r.blob());

        const formData = new FormData(document.getElementById('absensiForm'));
        formData.set('foto_selfie_out', blob, 'selfie.jpg');

        try {
            const response = await fetch('{{ route("guru.absensi-guru.check-out") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            if (response.ok) {
                window.location.reload();
            } else {
                alert(data.errors ? Object.values(data.errors).flat().join('\n') : 'Gagal check-out');
            }
        } catch (e) {
            alert('Terjadi kesalahan: ' + e.message);
        }
    }

    // Auto-start webcam and get location on load
    startWebcam();
    getMyLocation();
});
</script>
@endsection
