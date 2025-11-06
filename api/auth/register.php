<?php
require_once '../config/database.php';
require_once '../helpers/response.php';

header('Content-Type: application/json; charset=utf-8');

// ==== 1. Ambil data dari request ====
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

// ==== 2. Validasi input ====
if ($username === '' || $email === '' || $password === '') {
    send_json(['status' => 'error', 'message' => 'Semua field wajib diisi!']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_json(['status' => 'error', 'message' => 'Format email tidak valid!']);
    exit;
}

// ==== 3. Koneksi database ====
$conn = get_connection();

// ==== 4. Cek apakah username / email sudah ada ====
$check = $conn->prepare("SELECT id_user FROM users WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    send_json(['status' => 'error', 'message' => 'Username atau email sudah digunakan!']);
    exit;
}
$check->close();

// ==== 5. Hash password ====
$hashed = password_hash($password, PASSWORD_DEFAULT);

// ==== 6. Simpan ke database ====
$stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'Viewer')");
$stmt->bind_param("sss", $username, $hashed, $email);

if ($stmt->execute()) {
    send_json(['status' => 'success', 'message' => 'Akun berhasil dibuat. Silakan login.']);
} else {
    send_json(['status' => 'error', 'message' => 'Terjadi kesalahan server saat menyimpan data.']);
}

$stmt->close();
$conn->close();
?>