<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Interaktif - WebGIS Miskin & Ibadah</title>

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Turf.js -->
    <script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>
    <!-- DaisyUI + Tailwind -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4/dist/full.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Plus Jakarta Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        body { font-weight: 500; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
        h1, h2, h3, h4, h5, h6, .btn, .card-title, .stat-value { font-weight: 700 !important; letter-spacing: -0.01em; }
        .map-wrapper { flex: 1; position: relative; min-height: 0; }
        #map { width: 100%; height: 100%; }
        
        /* Floating Controls */
        .floating-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
            width: 300px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        }

        /* Marker dragging hint */
        #dragHint, #mapHint {
            position: absolute;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
        }

        /* Custom Layer Control */
        .leaflet-control-layers {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15) !important;
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.85) !important;
            padding: 8px 12px !important;
        }
    </style>
</head>
<body>

    <!-- ═══ NAVBAR ═══════════════════════════════════════════════════════════ -->
    <div class="navbar bg-base-100 border-b border-base-200 shadow-sm flex-shrink-0 z-[1001]">
        <div class="flex-1">
            <a href="{{ url('/') }}" class="btn btn-ghost text-lg font-bold gap-2 text-primary">
                <i class="fa-solid fa-house-chimney-crack"></i>
                WebGIS Miskin & Ibadah
            </a>
        </div>
        <div class="flex-none">
            <ul class="menu menu-horizontal px-1 gap-1">
                <li>
                    <a href="{{ url('/map') }}" class="font-semibold bg-primary/10 text-primary rounded-lg">
                        <i class="fa-solid fa-map"></i> Peta Interaktif
                    </a>
                </li>
                <li>
                    <a href="{{ url('/data') }}" class="font-medium">
                        <i class="fa-solid fa-table-list"></i> Data Tabular
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- ═══ MAP CONTAINER ════════════════════════════════════════════════════ -->
    <div class="map-wrapper">
        <div id="map"></div>

        <!-- Floating Control Panel -->
        <div class="floating-panel flex flex-col gap-3">
            <!-- Mode Selector -->
            <div class="card glass-card p-4">
                <h3 class="text-sm font-bold mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-primary"></i>
                    Mode Operasi
                </h3>
                <div class="grid grid-cols-1 gap-2">
                    <button class="btn btn-sm btn-outline btn-primary justify-start gap-2 active" id="btnModeView" onclick="setMode('view')">
                        <i class="fa-solid fa-eye"></i> View & Analisis
                    </button>
                    <button class="btn btn-sm btn-outline btn-success justify-start gap-2" id="btnModeIbadah" onclick="setMode('ibadah')">
                        <i class="fa-solid fa-plus"></i> Tambah Rumah Ibadah
                    </button>
                    <button class="btn btn-sm btn-outline btn-error justify-start gap-2" id="btnModeMiskin" onclick="setMode('miskin')">
                        <i class="fa-solid fa-plus"></i> Tambah Rumah Miskin
                    </button>
                </div>
            </div>

            <!-- Analysis Panel (Conditional) -->
            <div id="analysisPanel" class="card glass-card p-4 hidden">
                <h3 class="text-sm font-bold mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-secondary"></i>
                    Statistik Radius
                </h3>
                <div id="noSelectionMsg" class="text-xs text-base-content/50 italic py-2 text-center">
                    Klik Rumah Ibadah pada peta <br> untuk memulai analisis.
                </div>
                <div id="statsContent" class="hidden">
                    <div class="bg-base-200/50 rounded-lg p-3 mb-3 border border-base-300">
                        <div class="text-xs font-bold text-primary truncate" id="activeName">Nama Ibadah</div>
                        <div class="text-[10px] text-base-content/40" id="activeAddr">Alamat</div>
                    </div>
                    
                    <div class="form-control mb-4">
                        <label class="label py-1">
                            <span class="label-text-alt font-bold">Radius: <span id="radiusVal">500</span>m</span>
                        </label>
                        <input type="range" min="50" max="2000" value="500" step="50" class="range range-xs range-primary" id="radiusSlider">
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div class="text-center p-2 bg-white rounded-lg border border-base-200 shadow-sm">
                            <div class="text-lg font-bold text-error" id="statRumah">0</div>
                            <div class="text-[8px] uppercase tracking-wider font-bold opacity-50">Rumah</div>
                        </div>
                        <div class="text-center p-2 bg-white rounded-lg border border-base-200 shadow-sm">
                            <div class="text-lg font-bold text-warning" id="statKk">0</div>
                            <div class="text-[8px] uppercase tracking-wider font-bold opacity-50">KK</div>
                        </div>
                        <div class="text-center p-2 bg-white rounded-lg border border-base-200 shadow-sm">
                            <div class="text-lg font-bold text-info" id="statJiwa">0</div>
                            <div class="text-[8px] uppercase tracking-wider font-bold opacity-50">Jiwa</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hints -->
        <div id="mapHint" class="badge badge-primary badge-lg py-4 px-6 gap-2 shadow-xl hidden">
            <i class="fa-solid fa-mouse-pointer"></i>
            <span>Klik peta untuk menentukan lokasi baru</span>
        </div>
        <div id="dragHint" class="badge badge-warning badge-lg py-4 px-6 gap-2 shadow-xl hidden">
            <i class="fa-solid fa-arrows-up-down-left-right"></i>
            <span>Lepaskan untuk menyimpan lokasi baru</span>
        </div>
    </div>

    <!-- ═══ MODALS ═══════════════════════════════════════════════════════════ -->
    
    <!-- Modal: Input Ibadah -->
    <dialog id="modalIbadah" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg flex items-center gap-2 mb-4">
                <i class="fa-solid fa-mosque text-success"></i> Data Rumah Ibadah
            </h3>
            <div class="form-control gap-3">
                <div class="bg-base-200 p-2 rounded text-xs font-mono" id="ib_coords">Lat: -, Lng: -</div>
                <label class="form-control w-full">
                    <div class="label"><span class="label-text font-bold">Nama Rumah Ibadah</span></div>
                    <input type="text" id="ib_nama" placeholder="Contoh: Masjid Nurul Iman" class="input input-bordered w-full" />
                </label>
                <label class="form-control w-full">
                    <div class="label"><span class="label-text font-bold">Alamat</span></div>
                    <textarea id="ib_alamat" class="textarea textarea-bordered h-20" placeholder="Alamat lengkap..."></textarea>
                </label>
                <label class="form-control w-full">
                    <div class="label"><span class="label-text font-bold">Radius Default Analysis (Meter)</span></div>
                    <input type="number" id="ib_radius" value="500" class="input input-bordered w-full" />
                </label>
            </div>
            <div class="modal-action">
                <button class="btn btn-ghost" onclick="modalIbadah.close()">Batal</button>
                <button class="btn btn-success" id="btnSaveIbadah" onclick="saveIbadah()">Simpan</button>
            </div>
        </div>
    </dialog>

    <!-- Modal: Input Miskin -->
    <dialog id="modalMiskin" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg flex items-center gap-2 mb-4">
                <i class="fa-solid fa-home text-error"></i> Data Rumah Miskin
            </h3>
            <div class="form-control gap-3">
                <div class="bg-base-200 p-2 rounded text-xs font-mono" id="rm_coords">Lat: -, Lng: -</div>
                <label class="form-control w-full">
                    <div class="label"><span class="label-text font-bold">Alamat</span></div>
                    <textarea id="rm_alamat" class="textarea textarea-bordered h-20" placeholder="Alamat lengkap..."></textarea>
                </label>
                <label class="form-control w-full">
                    <div class="label"><span class="label-text font-bold">ID Rumah / Nama Pemilik</span></div>
                    <input type="text" id="rm_id" placeholder="Contoh: RM-001" class="input input-bordered w-full" />
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text font-bold">Jumlah KK</span></div>
                        <input type="number" id="rm_kk" value="1" class="input input-bordered w-full" />
                    </label>
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text font-bold">Jumlah Jiwa</span></div>
                        <input type="number" id="rm_jiwa" value="4" class="input input-bordered w-full" />
                    </label>
                </div>
            </div>
            <div class="modal-action">
                <button class="btn btn-ghost" onclick="modalMiskin.close()">Batal</button>
                <button class="btn btn-error" id="btnSaveMiskin" onclick="saveMiskin()">Simpan</button>
            </div>
        </div>
    </dialog>

    <!-- Modal: Confirm Delete -->
    <dialog id="deleteModal" class="modal">
        <div class="modal-box max-w-sm text-center">
            <div class="text-5xl mb-4 text-error"><i class="fa-solid fa-trash-can"></i></div>
            <h3 class="font-bold text-xl mb-2">Hapus Data?</h3>
            <p class="text-sm text-base-content/60 mb-6">Data ini akan dihapus secara permanen dari sistem.</p>
            <div class="flex gap-2 justify-center">
                <button class="btn btn-ghost px-8" onclick="deleteModal.close()">Batal</button>
                <button class="btn btn-error px-8 text-white" id="confirmDeleteBtn">Ya, Hapus</button>
            </div>
        </div>
    </dialog>

    <!-- Toast Container -->
    <div class="toast toast-end toast-bottom z-[9999]" id="toastBox"></div>

    <!-- ═══ JAVASCRIPT ════════════════════════════════════════════════════════ -->
    <script>
        // ── CONFIG & STATE ──────────────────────────────────────────────────
        let map, currentMode = 'view';
        let layerIbadah = L.layerGroup(), layerMiskin = L.layerGroup();
        let activeCircle = null, activeIbadah = null;
        let ibadahData = [], miskinData = [];
        let tempMarker = null;
        
        let ibadahMarkers = new Map(); // id -> marker
        let miskinMarkers = new Map(); // id_rumah -> marker

        const icons = {
            ibadah: (color = '#22c55e') => L.divIcon({
                html: `<div style="background:${color};width:32px;height:32px;border-radius:32px 32px 0 32px;border:3px solid white;transform:rotate(-45deg);display:flex;align-items:center;justify-center;box-shadow:0 4px 10px rgba(0,0,0,0.3);position:relative">
                        <i class="fa-solid fa-mosque" style="transform:rotate(45deg);color:white;font-size:14px;margin-left:6px;margin-top:2px"></i>
                       </div>`,
                className: '', iconSize: [32, 32], iconAnchor: [0, 32], popupAnchor: [16, -35]
            }),
            miskin: (color = '#ef4444', glow = false) => L.divIcon({
                html: `<div style="background:${color};width:24px;height:24px;border-radius:24px;border:2px solid white;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 8px rgba(0,0,0,0.3);${glow ? 'box-shadow: 0 0 15px ' + color : ''}">
                        <i class="fa-solid fa-home" style="color:white;font-size:10px"></i>
                       </div>`,
                className: '', iconSize: [24, 24], iconAnchor: [12, 12], popupAnchor: [0, -15]
            }),
            temp: () => L.divIcon({
                html: `<div class="animate-pulse" style="background:#3b82f6;width:20px;height:20px;border-radius:20px;border:3px solid white;box-shadow:0 0 15px rgba(59,130,246,0.6)"></div>`,
                className: '', iconSize: [20, 20], iconAnchor: [10, 10]
            })
        };

        // ── INIT ────────────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            loadData();
        });

        function initMap() {
            map = L.map('map', { zoomControl: false }).setView([-0.0553, 109.3463], 17);
            
            const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 });
            const satellite = L.layerGroup([
                L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 }),
                L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 }),
                L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 })
            ]);

            satellite.addTo(map);
            L.control.zoom({ position: 'bottomright' }).addTo(map);
            L.control.layers({ "Satelit": satellite, "Street": osm }, 
                             { "Rumah Ibadah": layerIbadah, "Rumah Miskin": layerMiskin }, 
                             { position: 'topright' }).addTo(map);

            layerIbadah.addTo(map);
            layerMiskin.addTo(map);

            map.on('click', e => {
                if (currentMode === 'view') return;
                handleMapClick(e.latlng);
            });
        }

        async function loadData() {
            try {
                const [ri, rm] = await Promise.all([
                    fetch('{{ url('/api/ibadah') }}').then(r => r.json()),
                    fetch('{{ url('/api/miskin') }}').then(r => r.json())
                ]);
                
                // Validate data is array
                if (!Array.isArray(ri) || !Array.isArray(rm)) {
                    console.error("Invalid API response", { ri, rm });
                    return;
                }

                ibadahData = ri;
                miskinData = rm;
                
                // Sync activeIbadah to point to the new object in the fresh data
                if (activeIbadah) {
                    const matched = ibadahData.find(i => i.id == activeIbadah.id);
                    if (matched) activeIbadah = matched;
                }

                renderMarkers();
            } catch (e) { 
                console.error(e);
                showToast('Gagal memuat data', 'error'); 
            }
        }

        function renderMarkers() {
            layerIbadah.clearLayers();
            layerMiskin.clearLayers();
            ibadahMarkers.clear();
            miskinMarkers.clear();

            ibadahData.forEach(item => {
                const m = L.marker([item.latitude, item.longitude], { icon: icons.ibadah(), draggable: true })
                    .bindPopup(buildPopupIbadah(item))
                    .on('click', () => handleIbadahClick(item));
                
                m.on('dragend', e => updateLocation('ibadah', item.id, e.target.getLatLng(), item));
                layerIbadah.addLayer(m);
                ibadahMarkers.set(item.id, m);
            });

            miskinData.forEach(item => {
                const marker = L.marker([item.latitude, item.longitude], { 
                    icon: icons.miskin('#ef4444'),
                    draggable: true 
                }).bindPopup(buildPopupMiskin(item));

                marker.on('dragend', e => updateLocation('miskin', item.id_rumah, e.target.getLatLng(), item));
                layerMiskin.addLayer(marker);
                miskinMarkers.set(item.id_rumah, marker);
            });

            // If an analysis is active, update highlights immediately
            if (activeCircle) {
                updateAnalysisUI(activeCircle.getLatLng(), activeCircle.getRadius());
            }
        }

        // ── MODE & CLICK HANDLING ───────────────────────────────────────────
        function setMode(mode) {
            currentMode = mode;
            document.querySelectorAll('.btn-outline').forEach(b => b.classList.remove('active'));
            document.getElementById('btnMode' + mode.charAt(0).toUpperCase() + mode.slice(1)).classList.add('active');
            
            document.getElementById('mapHint').classList.toggle('hidden', mode === 'view');
            document.getElementById('analysisPanel').classList.toggle('hidden', mode !== 'view');
            
            if (mode !== 'view') {
                if (activeCircle) { map.removeLayer(activeCircle); activeCircle = null; }
                document.getElementById('statsContent').classList.add('hidden');
                document.getElementById('noSelectionMsg').classList.remove('hidden');
            }
            if (tempMarker) { map.removeLayer(tempMarker); tempMarker = null; }
        }

        function handleMapClick(latlng) {
            if (tempMarker) map.removeLayer(tempMarker);
            tempMarker = L.marker(latlng, { icon: icons.temp() }).addTo(map);
            
            if (currentMode === 'ibadah') {
                document.getElementById('ib_coords').innerText = `Lat: ${latlng.lat.toFixed(6)}, Lng: ${latlng.lng.toFixed(6)}`;
                document.getElementById('ib_nama').value = '';
                document.getElementById('ib_alamat').value = 'Mencari alamat...';
                modalIbadah.showModal();
                
                // Auto fetch address
                reverseGeocode(latlng.lat, latlng.lng, 'ib_alamat');
            } else {
                document.getElementById('rm_coords').innerText = `Lat: ${latlng.lat.toFixed(6)}, Lng: ${latlng.lng.toFixed(6)}`;
                document.getElementById('rm_id').value = '';
                document.getElementById('rm_alamat').value = 'Mencari alamat...';
                modalMiskin.showModal();

                // Auto fetch address
                reverseGeocode(latlng.lat, latlng.lng, 'rm_alamat');
            }
        }

        async function reverseGeocode(lat, lng, targetId) {
            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`);
                const data = await res.json();
                if (data && data.display_name) {
                    document.getElementById(targetId).value = data.display_name;
                } else {
                    document.getElementById(targetId).value = "Alamat tidak ditemukan";
                }
            } catch (e) {
                document.getElementById(targetId).value = "Gagal mengambil alamat otomatis";
            }
        }

        function handleIbadahClick(item) {
            if (currentMode !== 'view') setMode('view');
            
            activeIbadah = item;
            document.getElementById('noSelectionMsg').classList.add('hidden');
            document.getElementById('statsContent').classList.remove('hidden');
            document.getElementById('activeName').innerText = item.nama;
            document.getElementById('activeAddr').innerText = item.alamat;
            
            const radius = document.getElementById('radiusSlider').value = item.radius || 500;
            updateRadius(item.latitude, item.longitude, radius);
        }

        // ── RADIUS ANALYSIS ────────────────────────────────────────────────
        function updateRadius(lat, lng, radius) {
            if (activeCircle) map.removeLayer(activeCircle);
            activeCircle = L.circle([lat, lng], {
                radius: parseInt(radius), 
                color: '#2563eb', 
                fillColor: '#3b82f6', 
                fillOpacity: 0.2, 
                weight: 2,
                dashArray: '5, 10'
            }).addTo(map);
            
            document.getElementById('radiusVal').innerText = radius;
            updateAnalysisUI([lat, lng], radius);
        }

        function updateAnalysisUI(center, radius) {
            let houses = 0, kk = 0, jiwa = 0;
            
            miskinData.forEach(h => {
                const houseMarker = miskinMarkers.get(h.id_rumah);
                const isH = isInside(h, center, radius);
                
                if (isH) {
                    houses++;
                    kk += parseInt(h.jumlah_kk);
                    jiwa += parseInt(h.jumlah_orang);
                }

                // Update marker icon in real-time
                if (houseMarker) {
                    const currentColor = isH ? '#fbbf24' : '#ef4444';
                    // Only update icon if state changed (optional optimization, but setIcon is fine)
                    houseMarker.setIcon(icons.miskin(currentColor, isH));
                    houseMarker.setZIndexOffset(isH ? 1000 : 0);
                }
            });

            document.getElementById('statRumah').innerText = houses;
            document.getElementById('statKk').innerText = kk;
            document.getElementById('statJiwa').innerText = jiwa;
        }

        function isInside(point, center, radius) {
            // center can be [lat, lng] or {lat, lng}
            const cLat = center.lat !== undefined ? center.lat : center[0];
            const cLng = center.lng !== undefined ? center.lng : center[1];
            
            const p1 = turf.point([Number(point.longitude), Number(point.latitude)]);
            const p2 = turf.point([Number(cLng), Number(cLat)]);
            return turf.distance(p1, p2, { units: 'meters' }) <= radius;
        }

        document.getElementById('radiusSlider').addEventListener('input', e => {
            if (activeIbadah) {
                updateRadius(activeIbadah.latitude, activeIbadah.longitude, e.target.value);
            }
        });

        // Save radius to DB when user finishes sliding
        document.getElementById('radiusSlider').addEventListener('change', async e => {
            if (!activeIbadah) return;
            
            const newRadius = e.target.value;
            try {
                const res = await fetch(`{{ url('/api/ibadah') }}/${activeIbadah.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: activeIbadah.id,
                        nama: activeIbadah.nama,
                        alamat: activeIbadah.alamat,
                        latitude: activeIbadah.latitude,
                        longitude: activeIbadah.longitude,
                        radius: newRadius
                    })
                });
                const resJson = await res.json();
                if (resJson.success) {
                    activeIbadah.radius = newRadius;
                    showToast('Radius disimpan ke database', 'success');
                    // Update the local data copy so it persists in memory
                    const localItem = ibadahData.find(i => i.id == activeIbadah.id);
                    if (localItem) localItem.radius = newRadius;
                }
            } catch (e) {
                showToast('Gagal menyimpan radius ke database', 'error');
            }
        });

        // ── POPUPS ──────────────────────────────────────────────────────────
        function buildPopupIbadah(item) {
            return `<div class="p-1">
                <div class="font-bold text-sm">🕌 ${item.nama}</div>
                <div class="text-[10px] text-gray-500 mb-2">${item.alamat}</div>
                <div class="flex gap-1 border-t pt-2 mt-1">
                    <button class="btn btn-xs btn-outline btn-error" onclick="prepDelete('ibadah', ${item.id})">Hapus</button>
                </div>
            </div>`;
        }

        function buildPopupMiskin(item) {
            return `<div class="p-1">
                <div class="font-bold text-sm">🏠 ID: ${item.id_rumah}</div>
                <div class="text-[10px] text-gray-500 mb-1">${item.alamat || 'Alamat tidak tersedia'}</div>
                <div class="text-[10px] text-gray-400">KK: ${item.jumlah_kk} | Jiwa: ${item.jumlah_orang}</div>
                <div class="flex gap-1 border-t pt-2 mt-1">
                    <button class="btn btn-xs btn-outline btn-error" onclick="prepDelete('miskin', '${item.id_rumah}')">Hapus</button>
                </div>
            </div>`;
        }

        // ── CRUD OPERATIONS ────────────────────────────────────────────────
        async function saveIbadah() {
            const pos = tempMarker.getLatLng();
            const data = {
                nama: document.getElementById('ib_nama').value,
                alamat: document.getElementById('ib_alamat').value,
                latitude: pos.lat, longitude: pos.lng,
                radius: document.getElementById('ib_radius').value
            };
            const btn = document.getElementById('btnSaveIbadah');
            btn.disabled = true;
            
            const res = await fetch('{{ url('/api/ibadah') }}', { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data) 
            });
            const resJson = await res.json();
            if (resJson.success) {
                showToast('Rumah Ibadah disimpan', 'success');
                modalIbadah.close();
                loadData();
            } else showToast(resJson.message, 'error');
            btn.disabled = false;
        }

        async function saveMiskin() {
            const pos = tempMarker.getLatLng();
            const data = {
                id_rumah: document.getElementById('rm_id').value,
                alamat: document.getElementById('rm_alamat').value,
                jumlah_kk: document.getElementById('rm_kk').value,
                jumlah_orang: document.getElementById('rm_jiwa').value,
                latitude: pos.lat, longitude: pos.lng
            };
            const btn = document.getElementById('btnSaveMiskin');
            btn.disabled = true;

            const res = await fetch('{{ url('/api/miskin') }}', { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data) 
            });
            const resJson = await res.json();
            if (resJson.success) {
                showToast('Rumah Miskin disimpan', 'success');
                modalMiskin.close();
                loadData();
            } else showToast(resJson.message, 'error');
            btn.disabled = false;
        }

        async function updateLocation(type, id, latlng, oldData) {
            const endpoint = `{{ url('/api') }}/${type}/${id}`;
            const payload = type === 'ibadah' 
                ? { id, nama: oldData.nama, alamat: oldData.alamat, latitude: latlng.lat, longitude: latlng.lng, radius: oldData.radius }
                : { id_rumah: id, alamat: oldData.alamat, jumlah_kk: oldData.jumlah_kk, jumlah_orang: oldData.jumlah_orang, latitude: latlng.lat, longitude: latlng.lng };
            
            try {
                const res = await fetch(endpoint, { 
                    method: 'PUT', 
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload) 
                });
                const resJson = await res.json();
                if (resJson.success) {
                    showToast('Lokasi diperbarui', 'success');
                    setTimeout(loadData, 100); // Small delay to let Leaflet finish
                } else {
                    showToast(resJson.message || 'Gagal memperbarui lokasi', 'error');
                    setTimeout(loadData, 100);
                }
            } catch (e) {
                showToast('Gagal terhubung ke server', 'error');
                setTimeout(loadData, 100);
            }
        }

        let deleteTarget = null;
        function prepDelete(type, id) {
            deleteTarget = { type, id };
            map.closePopup();
            deleteModal.showModal();
        }

        document.getElementById('confirmDeleteBtn').onclick = async () => {
            const { type, id } = deleteTarget;
            const res = await fetch(`{{ url('/api') }}/${type}/${id}`, { 
                method: 'DELETE', 
                headers: { 'Content-Type': 'application/json' }
            });
            const resJson = await res.json();
            if (resJson.success) {
                showToast('Data dihapus', 'success');
                deleteModal.close();
                loadData();
            } else showToast('Gagal menghapus', 'error');
        };

        // ── TOAST ──────────────────────────────────────────────────────────
        function showToast(msg, type = 'info') {
            const t = document.createElement('div');
            const alertCls = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-error' : 'alert-info';
            t.className = `alert ${alertCls} shadow-lg py-3 text-xs font-bold`;
            t.innerHTML = `<span><i class="fa-solid fa-circle-info"></i> ${msg}</span>`;
            document.getElementById('toastBox').appendChild(t);
            setTimeout(() => t.remove(), 3000);
        }
    </script>
</body>
</html>
