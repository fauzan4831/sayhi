<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../helpers/response.php";

session_start();
$conn = get_connection();

// Pastikan sesi dan role tersedia
if (!isset($_SESSION["role"])) {
    json_err("Sesi tidak ditemukan", 401);
}

// Pastikan hanya Admin yang dapat menghapus
if ($_SESSION["role"] !== "Admin") {
    json_err("Akses ditolak. Hanya admin yang boleh hapus.", 403);
}

// Validasi ID
$id = (int) ($_GET["id"] ?? 0);
if ($id <= 0) {
    json_err("ID tidak valid");
}

// Proses penghapusan data
$stmt = $conn->prepare("DELETE FROM tamu WHERE id_tamu = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Cek apakah data benar-benar terhapus
if ($stmt->affected_rows > 0) {
    json_ok([], ["message" => "Data berhasil dihapus"]);
} else {
    json_err("Data tidak ditemukan atau gagal dihapus", 404);
}
