<?php
session_start();
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../helpers/response.php";

$conn = get_connection();

if ($_SERVER["REQUEST_METHOD"] !== "POST")
    json_err("Method not allowed", 405);

$login = trim($_POST["login"] ?? "");
$pass = $_POST["password"] ?? "";

if ($login === "" || $pass === "")
    json_err("Login dan password wajib diisi");

$sql = "SELECT id_user, username, password, email, role FROM users WHERE (username=? OR email=?) LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $login, $login);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user)
    json_err("Akun tidak ditemukan atau password salah", 401);

$stored = $user["password"];

// 1) Jika sudah bcrypt
$ok = password_get_info($stored)["algo"] !== 0 ? password_verify($pass, $stored) : false;

// 2) Jika masih MD5 (migrasi mulus)
if (!$ok && strlen($stored) === 32 && $stored === md5($pass)) {
    $ok = true;
    // re-hash ke bcrypt
    $newHash = password_hash($pass, PASSWORD_BCRYPT);
    $upd = $conn->prepare("UPDATE users SET password=? WHERE id_user=?");
    $upd->bind_param("si", $newHash, $user["id_user"]);
    $upd->execute();
}

if (!$ok)
    json_err("Akun tidak ditemukan atau password salah", 401);

$_SESSION["login"] = true;
$_SESSION["username"] = $user["username"];
$_SESSION["role"] = $user["role"];
$_SESSION["uid"] = (int) $user["id_user"];

// catat login_history
$ip = $_SERVER['REMOTE_ADDR'] ?? null;
$h = $conn->prepare("INSERT INTO login_history (id_user, ip_address) VALUES (?, ?)");
$h->bind_param("is", $_SESSION["uid"], $ip);
$h->execute();

json_ok(["role" => $user["role"]], ["message" => "Login berhasil"]);
