<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../helpers/response.php";

$sql = "SELECT id_status, COUNT(*) as total FROM tamu GROUP BY id_status";
$res = $conn->query($sql);

$count = ["Mahasiswa" => 0, "Dosen" => 0, "Umum" => 0];
while ($row = $res->fetch_assoc()) {
    if ((int) $row["id_status"] === 1)
        $count["Mahasiswa"] = (int) $row["total"];
    if ((int) $row["id_status"] === 2)
        $count["Dosen"] = (int) $row["total"];
    if ((int) $row["id_status"] === 3)
        $count["Umum"] = (int) $row["total"];
}
json_ok($count);
