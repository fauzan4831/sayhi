<?php
require_once __DIR__."/../config/database.php";
require_once __DIR__."/../helpers/response.php";
$conn = get_connection();
session_start();

// Opsional: hanya Admin yang boleh hapus
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin") json_err("Unauthorized", 401);

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) json_err("ID tidak valid");

$stmt = $conn->prepare("DELETE FROM tamu WHERE id_tamu = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

json_ok([], ["message"=>"Data berhasil dihapus"]);
