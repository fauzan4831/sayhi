<?php
require_once __DIR__."/../config/database.php";
require_once __DIR__."/../helpers/response.php";
$conn = get_connection();
if ($_SERVER["REQUEST_METHOD"] !== "POST") json_err("Method not allowed", 405);

$nama     = trim($_POST["nama"] ?? "");
$instansi = trim($_POST["instansi"] ?? "");
$no_hp    = trim($_POST["no_hp"] ?? "");
$email    = trim($_POST["email"] ?? "");
$id_status= (int)($_POST["id_status"] ?? 0);
$tanggal  = trim($_POST["tanggal"] ?? "");

if ($nama==="" || $instansi==="" || $no_hp==="" || $tanggal==="") json_err("Data belum lengkap");
if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) json_err("Email tidak valid");
if (!preg_match('/^[0-9+\-\s]{6,20}$/', $no_hp)) json_err("Nomor HP tidak valid (6-20 digit)");

$stmt = $conn->prepare("INSERT INTO tamu (nama, instansi, no_hp, email, id_status, tanggal) VALUES (?,?,?,?,?,?)");
$stmt->bind_param("ssssss", $nama, $instansi, $no_hp, $email, $id_status, $tanggal);
$stmt->execute();

json_ok([], ["message"=>"Data berhasil disimpan"]);
