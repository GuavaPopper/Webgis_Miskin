<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Tabular - WebGIS Miskin & Ibadah</title>

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
        body { font-weight: 500; background-color: #f8fafc; }
        h1, h2, h3, h4, h5, h6, .btn, .card-title, .stat-value { font-weight: 700 !important; letter-spacing: -0.01em; }
    </style>
</head>
<body class="min-h-screen">

    <!-- ═══ NAVBAR ═══════════════════════════════════════════════════════════ -->
    <div class="navbar bg-base-100 border-b border-base-200 shadow-sm sticky top-0 z-[1000]">
        <div class="flex-1">
            <a href="{{ url('/') }}" class="btn btn-ghost text-lg font-bold gap-2 text-primary">
                <i class="fa-solid fa-house-chimney-crack"></i>
                WebGIS Miskin & Ibadah
            </a>
        </div>
        <div class="flex-none">
            <ul class="menu menu-horizontal px-1 gap-1">
                <li><a href="{{ url('/map') }}" class="font-medium"><i class="fa-solid fa-map"></i> Peta</a></li>
                <li><a href="{{ url('/data') }}" class="font-semibold bg-primary/10 text-primary rounded-lg"><i class="fa-solid fa-table-list"></i> Data Tabular</a></li>
            </ul>
        </div>
    </div>

    <!-- ═══ MAIN CONTENT ════════════════════════════════════════════════════ -->
    <main class="max-w-screen-xl mx-auto px-4 py-8">
        
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold flex items-center gap-3">
                    <i class="fa-solid fa-database text-primary"></i>
                    Manajemen Data
                </h1>
                <p class="text-base-content/50 mt-1">Daftar rumah ibadah dan keluarga miskin dalam matriks tabular.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ url('/map') }}" class="btn btn-primary shadow-lg"><i class="fa-solid fa-plus"></i> Tambah Data di Peta</a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat bg-white shadow rounded-2xl border border-base-200">
                <div class="stat-figure text-success text-3xl"><i class="fa-solid fa-mosque"></i></div>
                <div class="stat-title text-xs uppercase font-bold opacity-50">Total Rumah Ibadah</div>
                <div class="stat-value text-success" id="countIbadah">-</div>
            </div>
            <div class="stat bg-white shadow rounded-2xl border border-base-200">
                <div class="stat-figure text-error text-3xl"><i class="fa-solid fa-house-user"></i></div>
                <div class="stat-title text-xs uppercase font-bold opacity-50">Total Rumah Miskin</div>
                <div class="stat-value text-error" id="countMiskin">-</div>
            </div>
            <div class="stat bg-white shadow rounded-2xl border border-base-200">
                <div class="stat-figure text-warning text-3xl"><i class="fa-solid fa-users"></i></div>
                <div class="stat-title text-xs uppercase font-bold opacity-50">Total Jiwa Terdata</div>
                <div class="stat-value text-warning" id="countJiwa">-</div>
            </div>
        </div>

        <!-- Tabs -->
        <div role="tablist" class="tabs tabs-lifted tabs-lg mb-6">
            <input type="radio" name="data_tabs" role="tab" class="tab font-bold" aria-label="🕌 Rumah Ibadah" checked />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full" id="tableIbadah">
                        <thead>
                            <tr class="text-xs font-bold uppercase opacity-50">
                                <th>#</th>
                                <th>Nama Rumah Ibadah</th>
                                <th>Alamat</th>
                                <th>Radius Analisis</th>
                                <th>Koordinat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bodyIbadah"></tbody>
                    </table>
                </div>
            </div>

            <input type="radio" name="data_tabs" role="tab" class="tab font-bold" aria-label="🏠 Rumah Miskin" />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full" id="tableMiskin">
                        <thead>
                            <tr class="text-xs font-bold uppercase opacity-50">
                                <th>ID Rumah</th>
                                <th>Alamat</th>
                                <th>Jumlah KK</th>
                                <th>Jumlah Jiwa</th>
                                <th>Koordinat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="bodyMiskin"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal: Confirm Delete -->
    <dialog id="deleteModal" class="modal">
        <div class="modal-box max-w-sm text-center">
            <div class="text-5xl mb-4 text-error"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <h3 class="font-bold text-xl mb-2">Hapus Item?</h3>
            <p class="text-sm text-base-content/60 mb-6">Penghapusan tidak dapat dibatalkan.</p>
            <div class="flex gap-2 justify-center">
                <button class="btn btn-ghost px-8" onclick="deleteModal.close()">Batal</button>
                <button class="btn btn-error px-8 text-white" id="confirmDeleteBtn">Konfirmasi Hapus</button>
            </div>
        </div>
    </dialog>

    <!-- Toast Container -->
    <div class="toast toast-end toast-bottom z-[9999]" id="toastBox"></div>

    <script>
        document.addEventListener('DOMContentLoaded', loadAllData);

        async function loadAllData() {
            try {
                const [ri, rm] = await Promise.all([
                    fetch('{{ url('/api/ibadah') }}').then(r => r.json()),
                    fetch('{{ url('/api/miskin') }}').then(r => r.json())
                ]);

                // Stats
                document.getElementById('countIbadah').innerText = ri.length;
                document.getElementById('countMiskin').innerText = rm.length;
                document.getElementById('countJiwa').innerText = rm.reduce((acc, h) => acc + parseInt(h.jumlah_orang), 0);

                // Ibadah Table
                const bodyI = document.getElementById('bodyIbadah');
                bodyI.innerHTML = ri.length ? ri.map((item, i) => `
                    <tr>
                        <td class="text-xs font-bold opacity-30">${i + 1}</td>
                        <td class="font-bold text-primary">${item.nama}</td>
                        <td class="text-sm opacity-70 max-w-xs truncate">${item.alamat}</td>
                        <td><span class="badge badge-outline badge-sm">${item.radius}m</span></td>
                        <td class="font-mono text-[10px]">${parseFloat(item.latitude).toFixed(6)}, ${parseFloat(item.longitude).toFixed(6)}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-error btn-outline" onclick="prepDelete('ibadah', ${item.id})"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `).join('') : '<tr><td colspan="6" class="text-center py-10 opacity-30 italic">Belum ada data</td></tr>';

                // Miskin Table
                const bodyM = document.getElementById('bodyMiskin');
                bodyM.innerHTML = rm.length ? rm.map(item => `
                    <tr>
                        <td class="font-bold text-error">${item.id_rumah}</td>
                        <td class="text-xs opacity-70 max-w-xs truncate">${item.alamat || '-'}</td>
                        <td><span class="badge badge-warning badge-sm">${item.jumlah_kk} KK</span></td>
                        <td><span class="badge badge-info badge-sm">${item.jumlah_orang} Jiwa</span></td>
                        <td class="font-mono text-[10px]">${parseFloat(item.latitude).toFixed(6)}, ${parseFloat(item.longitude).toFixed(6)}</td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-error btn-outline" onclick="prepDelete('miskin', '${item.id_rumah}')"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `).join('') : '<tr><td colspan="6" class="text-center py-10 opacity-30 italic">Belum ada data</td></tr>';

            } catch (e) { showToast('Gagal memuat data', 'error'); }
        }

        let deleteTarget = null;
        function prepDelete(type, id) {
            deleteTarget = { type, id };
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
                loadAllData();
            } else showToast('Gagal menghapus', 'error');
        };

        function showToast(msg, type = 'info') {
            const t = document.createElement('div');
            const alertCls = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-error' : 'alert-info';
            t.className = `alert ${alertCls} shadow-lg py-3 text-xs font-bold`;
            t.innerHTML = `<span>${msg}</span>`;
            document.getElementById('toastBox').appendChild(t);
            setTimeout(() => t.remove(), 3000);
        }
    </script>
</body>
</html>
