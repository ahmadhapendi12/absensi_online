@extends('layouts.karyawan')

@section('content')
<!-- Header Biru & Profil -->
<div class="bg-blue-600 rounded-b-[2.5rem] p-6 text-white mb-6 shadow-md">
    <div class="flex items-center justify-between mb-6">
        <h1 class="font-bold text-xl tracking-wide">
            <i class="fas fa-fingerprint mr-2"></i> E-Absensi Lazatto
        </h1>
    </div>
    <div class="flex items-center">
        <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-blue-600 font-bold text-xl mr-4 border-2 border-blue-300 overflow-hidden shadow-inner">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=EBF8FF&color=2B6CB0" alt="Profil">
        </div>
        <div>
            <p class="text-sm text-blue-100 mb-1">Halo,</p>
            <h2 class="text-lg font-bold leading-tight">{{ Auth::user()->name }}</h2>
            <p class="text-xs text-blue-200 capitalize mt-1">{{ Auth::user()->role }} | {{ Auth::user()->cabang->nama_cabang ?? 'Pusat' }}</p>
        </div>
    </div>
</div>

<!-- Tombol Absen -->
<div class="px-6 mb-6">
    <button id="btn-masuk" class="w-full bg-blue-600 text-white font-bold py-3.5 rounded-xl mb-3 shadow-lg hover:bg-blue-700 active:scale-95 transition-all flex justify-center items-center">
        <i class="fas fa-sign-in-alt mr-2 text-lg"></i> Absen Masuk
    </button>
    <button id="btn-pulang" class="w-full bg-white text-blue-600 border-2 border-blue-600 font-bold py-3.5 rounded-xl shadow-sm hover:bg-blue-50 active:scale-95 transition-all flex justify-center items-center">
        <i class="fas fa-sign-out-alt mr-2 text-lg"></i> Absen Pulang
    </button>
</div>

<!-- Area Deteksi Wajah & Lokasi -->
<div class="px-6 mb-6">
    <div class="bg-white rounded-2xl shadow-sm p-4 border border-gray-100">
        
        <!-- Status Wajah -->
        <h3 class="font-bold text-center mb-3 text-sm" id="status-wajah">
            <i class="fas fa-camera text-gray-400 mr-1"></i> Menyiapkan Kamera...
        </h3>
        
        <!-- Viewport Kamera -->
        <div class="relative bg-black rounded-xl overflow-hidden aspect-[3/4] mb-4 flex items-center justify-center shadow-inner">
            <video id="webcam" autoplay muted playsinline class="object-cover w-full h-full transform scale-x-[-1]"></video>
            <canvas id="overlay" class="absolute top-0 left-0 w-full h-full"></canvas>
        </div>

        <!-- Info GPS Real-time -->
        <div id="box-lokasi" class="flex items-start bg-gray-50 p-3 rounded-xl border border-gray-200 transition-colors">
            <i class="fas fa-map-marker-alt text-gray-400 mt-1 mr-3 text-lg" id="ikon-lokasi"></i>
            <div>
                <p class="text-xs text-gray-500">Lokasi saat ini</p>
                <p class="text-sm font-bold text-gray-800 tracking-tight" id="lokasi-text">Mencari sinyal GPS...</p>
                <p class="text-xs font-semibold mt-1 text-gray-400" id="jarak-text">Menghitung jarak...</p>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- Import Library Face API via CDN -->
<script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>

<script>
    // 1. Data Cabang (PHP ke JS)
    const cabangLat = {{ Auth::user()->cabang->latitude ?? 0 }};
    const cabangLng = {{ Auth::user()->cabang->longitude ?? 0 }};
    const radiusBatas = {{ Auth::user()->cabang->radius_meter ?? 100 }};
    
    let userLat = 0;
    let userLng = 0;
    let dalamRadius = false;

    // 2. Inisialisasi Model AI Wajah
    async function loadFaceAPI() {
        document.getElementById('status-wajah').innerHTML = '<span class="text-yellow-500"><i class="fas fa-spinner fa-spin mr-1"></i> Memuat AI Wajah...</span>';
        
        const MODEL_URL = '/models'; 
        
        // Memuat 3 model sekaligus dari folder public/models/
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
        ]);
        
        initCamera(); // Nyalakan kamera setelah AI siap
    }

    // 3. Akses Kamera & Deteksi Wajah
    async function initCamera() {
        const video = document.getElementById('webcam');
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'user', width: 480, height: 640 } 
            });
            video.srcObject = stream;
        } catch (err) {
            document.getElementById('status-wajah').innerHTML = '<span class="text-red-500"><i class="fas fa-exclamation-triangle mr-1"></i> Izin kamera ditolak</span>';
            alert('Mohon izinkan akses kamera di browser Anda.');
        }

        video.addEventListener('play', () => {
            const canvas = document.getElementById('overlay');
            const displaySize = { width: video.clientWidth, height: video.clientHeight };
            faceapi.matchDimensions(canvas, displaySize);

            setInterval(async () => {
                const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
                                                .withFaceLandmarks()
                                                .withFaceDescriptors();
                
                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                
                if (detections.length > 0) {
                    document.getElementById('status-wajah').innerHTML = '<span class="text-green-600 font-bold"><i class="fas fa-user-check mr-1"></i> Wajah Terdeteksi</span>';
                    const resizedDetections = faceapi.resizeResults(detections, displaySize);
                    faceapi.draw.drawDetections(canvas, resizedDetections);
                    faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
                } else {
                    document.getElementById('status-wajah').innerHTML = '<span class="text-red-500"><i class="fas fa-user-times mr-1"></i> Wajah Tidak Ditemukan</span>';
                }
            }, 100);
        });
    }

    // 4. Rumus Menghitung Jarak (Meter)
    function hitungJarakMeter(lat1, lon1, lat2, lon2) {
        const R = 6371e3; 
        const φ1 = lat1 * Math.PI/180;
        const φ2 = lat2 * Math.PI/180;
        const Δφ = (lat2-lat1) * Math.PI/180;
        const Δλ = (lon2-lon1) * Math.PI/180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // 5. Akses GPS Real-time
    function initGPS() {
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(position => {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;
                
                document.getElementById('lokasi-text').innerText = userLat.toFixed(5) + ', ' + userLng.toFixed(5);
                
                let jarakMeter = Math.round(hitungJarakMeter(userLat, userLng, cabangLat, cabangLng));
                let jarakText = document.getElementById('jarak-text');
                let boxLokasi = document.getElementById('box-lokasi');
                let ikonLokasi = document.getElementById('ikon-lokasi');

                if (jarakMeter <= radiusBatas) {
                    dalamRadius = true;
                    jarakText.innerHTML = `Jarak dari cabang: ${jarakMeter} m <br><span class="text-green-600"><i class="fas fa-check-circle mr-1"></i> Dalam radius</span>`;
                    boxLokasi.className = "flex items-start bg-green-50 p-3 rounded-xl border border-green-200 transition-colors";
                    ikonLokasi.className = "fas fa-map-marker-alt text-green-500 mt-1 mr-3 text-lg";
                } else {
                    dalamRadius = false;
                    jarakText.innerHTML = `Jarak dari cabang: ${jarakMeter} m <br><span class="text-red-500"><i class="fas fa-times-circle mr-1"></i> Di luar radius (${radiusBatas}m)</span>`;
                    boxLokasi.className = "flex items-start bg-red-50 p-3 rounded-xl border border-red-200 transition-colors";
                    ikonLokasi.className = "fas fa-map-marker-alt text-red-500 mt-1 mr-3 text-lg";
                }
            }, err => {
                document.getElementById('lokasi-text').innerText = "Akses lokasi ditolak/tidak aktif";
                document.getElementById('jarak-text').innerText = "Pastikan GPS HP menyala.";
            }, { enableHighAccuracy: true, maximumAge: 0 });
        } else {
            alert("Browser Anda tidak mendukung fitur GPS.");
        }
    }

    // 6. Snapshot Foto
    function ambilFoto() {
        const video = document.getElementById('webcam');
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        const context = canvas.getContext('2d');
        context.translate(canvas.width, 0);
        context.scale(-1, 1);
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        return canvas.toDataURL('image/jpeg', 0.8); 
    }

    // 7. Kirim Data Absen (AJAX)
    async function kirimAbsen(jenis) {
        if (!dalamRadius) {
            alert('Akses Ditolak: Anda berada di luar radius toleransi kantor cabang!');
            return;
        }

        const btnId = jenis === 'masuk' ? 'btn-masuk' : 'btn-pulang';
        const btn = document.getElementById(btnId);
        const teksAsli = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim data...';
        btn.disabled = true;

        const dataPayload = {
            latitude: userLat,
            longitude: userLng,
            foto_base64: ambilFoto(),
            _token: '{{ csrf_token() }}'
        };

        try {
            const response = await fetch("{{ route('karyawan.presensi.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(dataPayload)
            });

            const result = await response.json();

            if (response.ok) {
                alert('Sukses: ' + result.message);
                window.location.href = "{{ route('karyawan.riwayat.index') }}"; 
            } else {
                alert('Gagal: ' + result.message);
                btn.innerHTML = teksAsli;
                btn.disabled = false;
            }
        } catch (error) {
            alert('Terjadi kesalahan jaringan atau server terputus.');
            btn.innerHTML = teksAsli;
            btn.disabled = false;
        }
    }

    // 8. Trigger Event Listeners & Jalankan Fungsi
    document.getElementById('btn-masuk').addEventListener('click', () => kirimAbsen('masuk'));
    document.getElementById('btn-pulang').addEventListener('click', () => kirimAbsen('pulang'));

    window.onload = () => {
        loadFaceAPI();
        initGPS();
    };
</script>
@endpush