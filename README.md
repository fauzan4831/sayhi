# SayHi! - Aplikasi Buku Tamu Digital

SayHi! adalah aplikasi buku tamu (guest book) berbasis web yang modern, dibangun dengan PHP native dan JavaScript. Aplikasi ini memungkinkan pencatatan tamu secara digital melalui halaman publik yang interaktif dan menyediakan dashboard admin yang fungsional untuk mengelola data, melihat laporan, dan mengelola pengguna.

![Ilustrasi Halaman Tamu](fauzan4831/sayhi/sayhi-7ca4e2cd995262a9b0962d6510cb9f48efcdb7b9/gambar/illustration.png)

## ✨ Fitur Utama

### Halaman Publik (`user-page.html`)
* **Formulir Entri Tamu:** Tamu dapat memasukkan data diri (Nama, Status, No. HP, Email, Tanggal).
* **Validasi Real-time:** Validasi input frontend untuk data seperti email dan nomor HP.
* **Dashboard Statistik:** Menampilkan jumlah tamu berdasarkan status (Dosen, Mahasiswa, Umum) secara *real-time* (diperbarui setiap 2 detik).
* **Desain Modern:** Tampilan yang bersih dan menarik menggunakan Bootstrap 5 dan tema kustom.

### Panel Admin (Dilindungi Login)
* **Autentikasi Aman:** Sistem registrasi dan login pengguna dengan *password hashing* (BCRYPT) dan manajemen sesi (PHP `$_SESSION`).
* **Manajemen Data Tamu (`data.html`):**
    * Menampilkan semua data tamu dalam tabel dengan paginasi.
    * Fitur pencarian *real-time* (berdasarkan nama atau instansi).
    * Menghapus data tamu (hanya untuk Admin).
* **Halaman Laporan (`laporan.html`):**
    * Filter data tamu berdasarkan rentang tanggal (dari tanggal A ke tanggal B).
    * Fitur **Ekspor ke CSV** untuk data yang telah difilter.
* **Manajemen Akun (`akun.html`):**
    * Mengubah profil admin (email, password).
    * Mengelola pengguna lain (melihat daftar, reset password, mengubah role) - *fitur backend tersedia*.
* **Kontrol Akses (RBAC):**
    * **Admin:** Hak akses penuh, termasuk menghapus tamu dan mengelola akun.
    * **Viewer:** Hanya dapat melihat data dan laporan, tidak dapat menghapus atau mengubah.

## 🛠️ Teknologi yang Digunakan

* **Frontend:**
    * HTML5
    * CSS3 (Kustom `sayhi-theme.css`, `style.css`)
    * JavaScript (Vanilla JS, ES6+ Async/Await, Fetch API)
    * [Bootstrap 5](https://getbootstrap.com/) (via CDN)
    * [SweetAlert2](https://sweetalert2.github.io/) (via CDN)
    * [Font Awesome](https://fontawesome.com/) (via CDN)

* **Backend:**
    * PHP (Native/Vanilla, tanpa framework)
    * API Berbasis REST (JSON) untuk komunikasi data.
    * Manajemen Sesi PHP (`session_start()`).

* **Database:**
    * MySQL / MariaDB

* **Lingkungan Pengembangan:**
    * XAMPP (disarankan, berdasarkan `api/config/database.php`)

## 🚀 Instalasi dan Penggunaan

Untuk menjalankan proyek ini di lingkungan lokal Anda (misalnya menggunakan XAMPP):

1.  **Clone Repositori**
    ```bash
    git clone [https://github.com/username/repository-name.git](https://github.com/username/repository-name.git)
    ```
    Atau unduh ZIP dan ekstrak.

2.  **Pindahkan Proyek**
    Pindahkan seluruh folder proyek (misalnya `sayhi/`) ke dalam direktori server web Anda (contoh: `C:/xampp/htdocs/`).

3.  **Setup Database**
    * Buka phpMyAdmin (`http://localhost/phpmyadmin`).
    * Buat database baru dengan nama `sayhi_db`.
    * Pilih database `sayhi_db` dan jalankan kueri SQL berikut untuk membuat tabel yang diperlukan:

    ```sql
    -- Tabel untuk status tamu (Dosen, Mahasiswa, Umum)
    CREATE TABLE `status_tamu` (
      `id_status` int(11) NOT NULL AUTO_INCREMENT,
      `nama_status` varchar(50) NOT NULL,
      PRIMARY KEY (`id_status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Isi data status_tamu
    INSERT INTO `status_tamu` (`id_status`, `nama_status`) VALUES
    (1, 'Mahasiswa'),
    (2, 'Dosen'),
    (3, 'Umum');

    -- Tabel untuk data tamu
    CREATE TABLE `tamu` (
      `id_tamu` int(11) NOT NULL AUTO_INCREMENT,
      `nama` varchar(100) NOT NULL,
      `instansi` varchar(100) DEFAULT NULL,
      `no_hp` varchar(20) DEFAULT NULL,
      `email` varchar(100) DEFAULT NULL,
      `id_status` int(11) DEFAULT NULL,
      `tanggal` date DEFAULT NULL,
      PRIMARY KEY (`id_tamu`),
      KEY `id_status` (`id_status`),
      CONSTRAINT `tamu_ibfk_1` FOREIGN KEY (`id_status`) REFERENCES `status_tamu` (`id_status`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Tabel untuk pengguna (admin/viewer)
    CREATE TABLE `users` (
      `id_user` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(50) NOT NULL,
      `password` varchar(255) NOT NULL,
      `email` varchar(100) NOT NULL,
      `role` enum('Admin','Viewer') NOT NULL DEFAULT 'Viewer',
      PRIMARY KEY (`id_user`),
      UNIQUE KEY `username` (`username`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Tabel untuk histori login (opsional namun ada di kode)
    CREATE TABLE `login_history` (
      `id_history` int(11) NOT NULL AUTO_INCREMENT,
      `id_user` int(11) DEFAULT NULL,
      `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
      `ip_address` varchar(45) DEFAULT NULL,
      PRIMARY KEY (`id_history`),
      KEY `id_user` (`id_user`),
      CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ```

4.  **Konfigurasi Koneksi**
    * Buka file `api/config/database.php`.
    * Sesuaikan nilai `$host`, `$user`, `$pass`, dan `$db` jika diperlukan (pengaturan default XAMPP adalah `root` tanpa password).

5.  **Jalankan Aplikasi**
    * Pastikan server Apache dan MySQL Anda berjalan.
    * **Halaman Tamu:** Buka `http://localhost/sayhi/user-page.html`
    * **Halaman Login:** Buka `http://localhost/sayhi/login.html`
    * Anda dapat mendaftar akun baru melalui `signup.html`. Akun pertama yang dibuat secara default akan memiliki role 'Viewer'. Anda dapat mengubahnya menjadi 'Admin' langsung di database.

## 📁 Susunan Project
````markdown
# SayHi! - Aplikasi Buku Tamu Digital

SayHi! adalah aplikasi buku tamu (guest book) berbasis web yang modern, dibangun dengan PHP native dan JavaScript. Aplikasi ini memungkinkan pencatatan tamu secara digital melalui halaman publik yang interaktif dan menyediakan dashboard admin yang fungsional untuk mengelola data, melihat laporan, dan mengelola pengguna.

![Ilustrasi Halaman Tamu](fauzan4831/sayhi/sayhi-7ca4e2cd995262a9b0962d6510cb9f48efcdb7b9/gambar/illustration.png)

## ✨ Fitur Utama

### Halaman Publik (`user-page.html`)
* **Formulir Entri Tamu:** Tamu dapat memasukkan data diri (Nama, Status, No. HP, Email, Tanggal).
* **Validasi Real-time:** Validasi input frontend untuk data seperti email dan nomor HP.
* **Dashboard Statistik:** Menampilkan jumlah tamu berdasarkan status (Dosen, Mahasiswa, Umum) secara *real-time* (diperbarui setiap 2 detik).
* **Desain Modern:** Tampilan yang bersih dan menarik menggunakan Bootstrap 5 dan tema kustom.

### Panel Admin (Dilindungi Login)
* **Autentikasi Aman:** Sistem registrasi dan login pengguna dengan *password hashing* (BCRYPT) dan manajemen sesi (PHP `$_SESSION`).
* **Manajemen Data Tamu (`data.html`):**
    * Menampilkan semua data tamu dalam tabel dengan paginasi.
    * Fitur pencarian *real-time* (berdasarkan nama atau instansi).
    * Menghapus data tamu (hanya untuk Admin).
* **Halaman Laporan (`laporan.html`):**
    * Filter data tamu berdasarkan rentang tanggal (dari tanggal A ke tanggal B).
    * Fitur **Ekspor ke CSV** untuk data yang telah difilter.
* **Manajemen Akun (`akun.html`):**
    * Mengubah profil admin (email, password).
    * Mengelola pengguna lain (melihat daftar, reset password, mengubah role) - *fitur backend tersedia*.
* **Kontrol Akses (RBAC):**
    * **Admin:** Hak akses penuh, termasuk menghapus tamu dan mengelola akun.
    * **Viewer:** Hanya dapat melihat data dan laporan, tidak dapat menghapus atau mengubah.

## 🛠️ Teknologi yang Digunakan

* **Frontend:**
    * HTML5
    * CSS3 (Kustom `sayhi-theme.css`, `style.css`)
    * JavaScript (Vanilla JS, ES6+ Async/Await, Fetch API)
    * [Bootstrap 5](https://getbootstrap.com/) (via CDN)
    * [SweetAlert2](https://sweetalert2.github.io/) (via CDN)
    * [Font Awesome](https://fontawesome.com/) (via CDN)

* **Backend:**
    * PHP (Native/Vanilla, tanpa framework)
    * API Berbasis REST (JSON) untuk komunikasi data.
    * Manajemen Sesi PHP (`session_start()`).

* **Database:**
    * MySQL / MariaDB

* **Lingkungan Pengembangan:**
    * XAMPP (disarankan, berdasarkan `api/config/database.php`)

## 🚀 Instalasi dan Penggunaan

Untuk menjalankan proyek ini di lingkungan lokal Anda (misalnya menggunakan XAMPP):

1.  **Clone Repositori**
    ```bash
    git clone [https://github.com/username/repository-name.git](https://github.com/username/repository-name.git)
    ```
    Atau unduh ZIP dan ekstrak.

2.  **Pindahkan Proyek**
    Pindahkan seluruh folder proyek (misalnya `sayhi/`) ke dalam direktori server web Anda (contoh: `C:/xampp/htdocs/`).

3.  **Setup Database**
    * Buka phpMyAdmin (`http://localhost/phpmyadmin`).
    * Buat database baru dengan nama `sayhi_db`.
    * Pilih database `sayhi_db` dan jalankan kueri SQL berikut untuk membuat tabel yang diperlukan:

    ```sql
    -- Tabel untuk status tamu (Dosen, Mahasiswa, Umum)
    CREATE TABLE `status_tamu` (
      `id_status` int(11) NOT NULL AUTO_INCREMENT,
      `nama_status` varchar(50) NOT NULL,
      PRIMARY KEY (`id_status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Isi data status_tamu
    INSERT INTO `status_tamu` (`id_status`, `nama_status`) VALUES
    (1, 'Mahasiswa'),
    (2, 'Dosen'),
    (3, 'Umum');

    -- Tabel untuk data tamu
    CREATE TABLE `tamu` (
      `id_tamu` int(11) NOT NULL AUTO_INCREMENT,
      `nama` varchar(100) NOT NULL,
      `instansi` varchar(100) DEFAULT NULL,
      `no_hp` varchar(20) DEFAULT NULL,
      `email` varchar(100) DEFAULT NULL,
      `id_status` int(11) DEFAULT NULL,
      `tanggal` date DEFAULT NULL,
      PRIMARY KEY (`id_tamu`),
      KEY `id_status` (`id_status`),
      CONSTRAINT `tamu_ibfk_1` FOREIGN KEY (`id_status`) REFERENCES `status_tamu` (`id_status`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Tabel untuk pengguna (admin/viewer)
    CREATE TABLE `users` (
      `id_user` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(50) NOT NULL,
      `password` varchar(255) NOT NULL,
      `email` varchar(100) NOT NULL,
      `role` enum('Admin','Viewer') NOT NULL DEFAULT 'Viewer',
      PRIMARY KEY (`id_user`),
      UNIQUE KEY `username` (`username`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Tabel untuk histori login (opsional namun ada di kode)
    CREATE TABLE `login_history` (
      `id_history` int(11) NOT NULL AUTO_INCREMENT,
      `id_user` int(11) DEFAULT NULL,
      `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
      `ip_address` varchar(45) DEFAULT NULL,
      PRIMARY KEY (`id_history`),
      KEY `id_user` (`id_user`),
      CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ```

4.  **Konfigurasi Koneksi**
    * Buka file `api/config/database.php`.
    * Sesuaikan nilai `$host`, `$user`, `$pass`, dan `$db` jika diperlukan (pengaturan default XAMPP adalah `root` tanpa password).

5.  **Jalankan Aplikasi**
    * Pastikan server Apache dan MySQL Anda berjalan.
    * **Halaman Tamu:** Buka `http://localhost/sayhi/user-page.html`
    * **Halaman Login:** Buka `http://localhost/sayhi/login.html`
    * Anda dapat mendaftar akun baru melalui `signup.html`. Akun pertama yang dibuat secara default akan memiliki role 'Viewer'. Anda dapat mengubahnya menjadi 'Admin' langsung di database.

## 📁 Susunan Project

````

sayhi/
├── api/                \# Logika backend (PHP)
│   ├── auth/           \# Skrip autentikasi (login, register, logout, me)
│   ├── config/         \# Koneksi database (database.php)
│   ├── dashboard/      \# Endpoint statistik (counts.php)
│   ├── helpers/        \# Fungsi helper (response.php)
│   └── tamu/           \# Endpoint CRUD tamu (add, list, delete)
├── components/         \# Potongan HTML (navbar.html)
├── css/                \# Stylesheets (app.css, sayhi-theme.css, style.css, dll)
├── gambar/             \# Aset gambar (logo.png, illustration.png)
├── javascript/         \# Logika frontend (script.js, user-page.js)
├── akun.html           \# Halaman manajemen akun (Admin)
├── data.html           \# Halaman data tamu (Admin)
├── index.html          \# Halaman landing (Admin, setelah login)
├── laporan.html        \# Halaman laporan (Admin)
├── login.html          \# Halaman login
├── signup.html         \# Halaman registrasi
├── user-page.html      \# Halaman buku tamu publik
└── README.md           \# File ini

```

## 🤝 Kontribusi

Kontribusi, isu, dan permintaan fitur sangat diterima! Jangan ragu untuk *fork* repositori ini dan membuka *pull request*.

1.  Fork repositori ini.
2.  Buat *branch* fitur Anda (`git checkout -b fitur/FiturKeren`).
3.  Commit perubahan Anda (`git commit -m 'Menambahkan FiturKeren'`).
4.  Push ke *branch* Anda (`git push origin fitur/FiturKeren`).
5.  Buka *Pull Request*.

## 📄 Lisensi

Proyek ini dilisensikan di bawah **MIT License**.

---
MIT License

Copyright (c) 2025 [Nama Anda/Pemilik Proyek]

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT, OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
