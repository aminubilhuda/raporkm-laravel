# Dapodik Sync

Panduan konfigurasi dan sinkronisasi data dengan Dapodik.

## Konfigurasi Dapodik

1. Klik menu **Dapodik** di sidebar
2. Di bagian **Konfigurasi**, isi:
    - **URL Dapodik**: URL server Dapodik (contoh: `https://dapodik.kemdikbud.go.id`)
    - **Token**: Token API dari Dapodik
3. Klik **Simpan Konfigurasi**

!!! warning "Penting"
    Token Dapodik bisa didapatkan dari:
    1. Login ke Dapodik
    2. Buka menu **Pengaturan** → **Token**
    3. Generate token baru
    4. Copy token dan paste ke kolom Token

## Sinkronisasi Data

### Endpoint yang Tersedia

| Endpoint | Deskripsi |
|----------|-----------|
| **Sekolah** | Data sekolah (profil, alamat, kontak) |
| **Peserta Didik** | Data siswa |
| **Rombongan Belajar** | Data kelas/rombel |
| **Pengguna** | Data pengguna Dapodik |
| **GTK** | Guru dan Tenaga Kependidikan |
| **Pembelajaran** | Data pembelajaran/mapel |

### Cara Sinkronisasi

1. Pilih endpoint yang ingin disinkronisasi
2. Klik **Sync** di sebelah endpoint
3. Tunggu hingga proses selesai
4. Status akan ditampilkan di **Log Sync**

### Sync Semua Sekaligus

1. Klik **Sync All** untuk sinkronisasi semua endpoint
2. Proses akan berjalan secara berurutan
3. Progress akan ditampilkan secara real-time

## Log Sync

1. Klik menu **Dapodik** → **Log**
2. Anda akan melihat riwayat sinkronisasi
3. Informasi yang ditampilkan:
    - Endpoint
    - Status (Berhasil/Gagal)
    - Jumlah record yang di-sync
    - Waktu eksekusi
    - Pesan error (jika gagal)

### Filter Log

- Gunakan filter untuk mencari log tertentu
- Filter berdasarkan: Status, Endpoint, Rentang Waktu

## Troubleshooting

### Error: Connection Timeout

- Pastikan server Dapodik bisa diakses
- Cek firewall/whitelist IP server
- Coba lagi setelah beberapa menit

### Error: Invalid Token

- Generate token baru di Dapodik
- Update token di konfigurasi E-Rapor

### Error: Data Tidak Sinkron

- Cek log untuk detail error
- Pastikan data di Dapodik sudah lengkap
- Coba sync endpoint satu per satu

!!! tip "Tips"
    - Lakukan sinkronisasi secara berkala (minimal 1x sehari)
    - Backup database sebelum sync besar
    - Gunakan **Sync All** hanya saat awal semester
