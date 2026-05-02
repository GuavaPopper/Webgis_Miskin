# Dokumentasi Fungsional: WebGIS Miskin & Ibadah

Proyek ini adalah sistem informasi geografis (WebGIS) berbasis web yang dirancang untuk memetakan sebaran rumah ibadah dan keluarga kurang mampu (miskin). Sistem ini memungkinkan pengguna untuk melakukan analisis spasial berdasarkan radius untuk mengetahui dampak atau jangkauan layanan rumah ibadah terhadap komunitas di sekitarnya.

## Filosofi & Latar Belakang

Proyek ini dibangun atas dasar filosofi kepedulian sosial, di mana rumah ibadah memiliki peran sentral dalam kesejahteraan masyarakat di sekitarnya. Prinsip utama sistem ini adalah:
> **Setiap keluarga kurang mampu yang tinggal di sekitar rumah ibadah dianggap sebagai tanggungan atau prioritas utama untuk mendapatkan bantuan dari rumah ibadah tersebut.**

Dengan adanya sistem WebGIS ini, pengelola rumah ibadah dapat dengan mudah memetakan siapa saja yang membutuhkan bantuan dalam radius tertentu, sehingga distribusi bantuan menjadi lebih tepat sasaran dan terorganisir.

## Fitur Utama

### 1. Peta Interaktif (Interactive WebGIS)
Menggunakan library **Leaflet.js**, sistem menyediakan antarmuka peta yang responsif dengan fitur:
-   **Toggle Layer**: Berpindah antara tampilan jalan (Street) dan citra satelit.
-   **Marker Kustom**: Ikon berbeda untuk membedakan antara Rumah Ibadah (hijau) dan Rumah Miskin (merah).
-   **Geocoding Otomatis**: Saat menambahkan data baru, sistem secara otomatis mengambil alamat lengkap berdasarkan koordinat klik menggunakan OpenStreetMap Nominatim.

### 2. Manajemen Data (CRUD)
Sistem mendukung pengelolaan data secara penuh:
-   **Tambah Data via Peta**: Klik pada lokasi di peta untuk memunculkan formulir input data baru.
-   **Update Lokasi (Drag & Drop)**: Marker dapat digeser (drag) untuk memperbarui koordinat lokasi di database secara real-time.
-   **Hapus Data**: Menghapus data melalui popup pada marker atau melalui tabel data.
-   **Data Tabular**: Halaman khusus yang menampilkan seluruh data dalam bentuk tabel dengan statistik ringkasan.

### 3. Analisis Spasial Radius
Fitur unggulan untuk menganalisis sebaran keluarga miskin di sekitar rumah ibadah:
-   **Analisis Otomatis**: Mengklik marker rumah ibadah akan langsung memunculkan lingkaran radius dan panel statistik.
-   **Penghitungan Real-time**: Menggunakan **Turf.js**, sistem menghitung secara instan jumlah rumah, jumlah Kepala Keluarga (KK), dan jumlah Jiwa yang berada di dalam radius terpilih.
-   **Slider Radius**: Pengguna dapat menggeser slider (50m hingga 2000m) untuk melihat perubahan statistik secara dinamis.
-   **Highlight Visual**: Marker rumah miskin yang masuk ke dalam radius akan berubah warna menjadi kuning untuk memudahkan identifikasi visual.

## Arsitektur Teknis

Proyek ini telah dimigrasikan ke framework **Laravel 11** dengan struktur sebagai berikut:

-   **Backend**: 
    -   **Laravel Eloquent**: Digunakan untuk interaksi database yang aman dan efisien.
    -   **RESTful API**: Menyediakan endpoint `/api/ibadah` dan `/api/miskin` untuk komunikasi frontend-backend.
    -   **Migrations**: Skema database yang terstruktur dan mudah dikelola.
-   **Frontend**:
    -   **Blade Templates**: Mesin templating Laravel untuk antarmuka yang dinamis.
    -   **Tailwind CSS & DaisyUI**: Memberikan desain antarmuka yang modern, premium, dan responsif.
    -   **JavaScript (Vanilla)**: Menangani logika peta dan interaksi API tanpa beban framework berat.
-   **Database**: MySQL/MariaDB untuk penyimpanan data spasial dan atribut.

## Daftar Halaman

1.  **Beranda (`/`)**: Halaman landing dengan latar belakang peta animasi dan navigasi cepat.
2.  **Peta Interaktif (`/map`)**: Antarmuka utama untuk melihat sebaran dan melakukan analisis radius.
3.  **Data Tabular (`/data`)**: Manajemen data dalam format tabel dengan ringkasan statistik total.

---
*Dokumentasi ini dibuat untuk menjelaskan kapabilitas sistem WebGIS Miskin & Ibadah versi Laravel.*
