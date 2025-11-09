<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../helpers/response.php";

$conn = get_connection();
$search = trim($_GET['search'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$where = "";
$params = [];
$types = "";

// ====== Filter pencarian (nama / instansi) ======
if ($search !== "") {
    $like = "%{$search}%";
    $where = " WHERE t.nama LIKE ? OR t.instansi LIKE ? ";
    $params = [$like, $like];
    $types = "ss";
}

// ====== Filter tanggal (tambahan dari filter search) ======
$awal = $_GET["awal"] ?? null;
$akhir = $_GET["akhir"] ?? null;
if ($awal && $akhir) {
    // Jika sebelumnya sudah ada WHERE (dari pencarian)
    if ($where) {
        $where .= " AND t.tanggal BETWEEN ? AND ?";
    } else {
        $where = "WHERE t.tanggal BETWEEN ? AND ?";
    }
    $params[] = $awal;
    $params[] = $akhir;
    $types .= "ss";
}

// ====== Hitung total data ======
if ($where) {
    $sqlTotal = "SELECT COUNT(*) AS total 
                FROM tamu t 
                LEFT JOIN status_tamu s ON t.id_status = s.id_status
                {$where}";
    $ts = $conn->prepare($sqlTotal);
    $ts->bind_param($types, ...$params);
    $ts->execute();
    $totalData = (int) $ts->get_result()->fetch_assoc()['total'];
    $ts->close();
} else {
    $totalData = (int) $conn
        ->query("SELECT COUNT(*) AS total FROM tamu")
        ->fetch_assoc()['total'];
}

$totalPage = (int) ceil($totalData / $limit);

// ====== Ambil data tamu + status ======
$sql = "SELECT 
            t.id_tamu,
            t.nama,
            t.instansi,
            t.no_hp,
            t.email,
            s.nama_status AS status,
            t.tanggal
        FROM tamu t
        LEFT JOIN status_tamu s ON t.id_status = s.id_status
        {$where}
        ORDER BY t.id_tamu DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$params2 = array_merge($params, [$limit, $offset]);
$types2 = $types . "ii";
$stmt->bind_param($types2, ...$params2);

$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
    // Masking email dan no_hp untuk keamanan
    $email = $row['email'] ?? '';
    if (strpos($email, '@') !== false) {
        [$u, $d] = explode('@', $email, 2);
        $email = substr($u, 0, max(1, min(4, strlen($u)))) . "***@" . $d;
    } else {
        $email = '—';
    }

    $hp = $row['no_hp'] ?? '';
    if ($hp !== '')
        $hp = substr($hp, 0, min(4, strlen($hp))) . "****";

    $row['email'] = $email;
    $row['no_hp'] = $hp;
    $data[] = $row;
}

$stmt->close();
$conn->close();

json_ok($data, ["page" => $page, "totalPage" => $totalPage]);
