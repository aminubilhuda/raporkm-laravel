# Manajemen Pengguna

Panduan mengelola pegawai (guru, TU, kepsek) dan hak akses menu.

## Daftar Pegawai

1. Klik menu **Pegawai** di sidebar
2. Anda akan melihat daftar seluruh pegawai
3. Gunakan **search** untuk mencari pegawai tertentu
4. Gunakan **filter jabatan** untuk memfilter berdasarkan role

## Tambah Pegawai Baru

1. Klik **Tambah Pegawai**
2. Isi data yang diperlukan:
    - **Nama**: Nama lengkap pegawai
    - **Username**: Untuk login (unik)
    - **Email**: Alamat email (opsional)
    - **Password**: Minimal 6 karakter
    - **Jabatan**: Pilih (Tata Usaha / Guru / Kepsek)
    - **Kontak**: Nomor telepon (opsional)
3. Isi data GTK (opsional):
    - NIP
    - NUPTK
    - Jenis Kelamin
    - Agama
4. Klik **Simpan**

## Edit Pegawai

1. Di daftar pegawai, klik ikon **Edit** (pensil) di baris pegawai
2. Ubah data yang diperlukan
3. **Hak Akses Menu** (untuk guru/kepsek):
    - Pilih menu yang ingin di-grant/revoke
    - **Auto**: Otomatis berdasarkan peran (wali kelas, pengajar, dll)
    - **Grant**: Paksa tampil selalu
    - **Revoke**: Sembunyikan selalu
4. Klik **Simpan**

!!! info "Tentang Hak Akses Menu"
    Sistem hak akses menu menggunakan pendekatan **hybrid**:
    - **Auto-detect**: Menu otomatis muncul berdasarkan data (misal: guru yang mengajar akan melihat menu Penilaian)
    - **Admin Override**: Anda bisa memaksa tampil atau sembunyikan menu tertentu untuk guru/kepsek

## Nonaktifkan & Hapus Pegawai

- **Nonaktifkan**: Klik ikon **Trash** → Pegawai akan di-soft delete (bisa dipulihkan)
- **Pulihkan**: Klik **Restore** di daftar yang sudah dihapus

## Reset Password

1. Edit pegawai yang bersangkutan
2. Isi kolom **Password** dengan password baru
3. Klik **Simpan**
