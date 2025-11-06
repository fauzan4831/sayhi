<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../helpers/response.php";

// buka koneksi
$conn = get_connection();

// jalankan query statistik
$sql = "SELECT id_status, COUNT(*) AS total FROM tamu GROUP BY id_status";
$res = $conn->query($sql);

if (!$res) {
    // kalau query error, kirim JSON error yang rapi
    json_err("Gagal mengambil statistik tamu");
    exit;
}

// default 0 semua
$count = [
    "Mahasiswa" => 0,
    "Dosen"     => 0,
    "Umum"      => 0,
];

// mapping id_status ke label
while ($row = $res->fetch_assoc()) {
    $status = (int) $row["id_status"];
    $total  = (int) $row["total"];

    if ($status === 1) {
        $count["Mahasiswa"] = $total;
    } elseif ($status === 2) {
        $count["Dosen"] = $total;
    } elseif ($status === 3) {
        $count["Umum"] = $total;
    }
}

$res->free();
$conn->close();

// kirim JSON sukses
json_ok($count);
