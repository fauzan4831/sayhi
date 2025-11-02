<?php
// Koneksi ke database (XAMPP default)
function get_connection() {
    $host = "localhost";
    $user = "root"; // default user XAMPP
    $pass = "";     // default password kosong
    $db   = "sayhi_db"; // ubah jika nama DB kamu berbeda

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Pastikan set UTF-8 (biar karakter nama tamu tidak error)
    $conn->set_charset("utf8mb4");

    return $conn;
}
?>
