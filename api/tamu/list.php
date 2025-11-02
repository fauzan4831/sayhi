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

if ($search !== "") {
    $like = "%{$search}%";
    $where = " WHERE nama LIKE ? OR instansi LIKE ? ";
    $params = [$like, $like];
    $types = "ss";
}

// total
if ($where) {
    $ts = $conn->prepare("SELECT COUNT(*) as total FROM tamu {$where}");
    $ts->bind_param($types, ...$params);
    $ts->execute();
    $totalData = (int) $ts->get_result()->fetch_assoc()['total'];
} else {
    $totalData = (int) $conn->query("SELECT COUNT(*) as total FROM tamu")->fetch_assoc()['total'];
}
$totalPage = (int) ceil($totalData / $limit);

// data
$sql = "SELECT * FROM tamu {$where} ORDER BY id_tamu DESC LIMIT ? OFFSET ?";
if ($where) {
    $stmt = $conn->prepare($sql);
    $types2 = $types . "ii";
    $params2 = array_merge($params, [$limit, $offset]);
    $stmt->bind_param($types2, ...$params2);
} else {
    $stmt = $conn->prepare("SELECT * FROM tamu ORDER BY id_tamu DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
    // Masking aman
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

json_ok($data, ["page" => $page, "totalPage" => $totalPage]);
