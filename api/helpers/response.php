<?php
// Helper global untuk respon JSON terstruktur

function send_json($data, $http_code = 200) {
    http_response_code($http_code);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Sukses → hasil OK (misal untuk login/register)
function json_ok($data = [], $extra = []) {
    $response = array_merge([
        "status" => "success",
        "timestamp" => date("Y-m-d H:i:s")
    ], $extra, ["data" => $data]);

    send_json($response, 200);
}

// Error → hasil gagal (bisa kirim pesan & kode HTTP)
function json_err($message = "Terjadi kesalahan", $http_code = 400) {
    send_json([
        "status" => "error",
        "message" => $message,
        "timestamp" => date("Y-m-d H:i:s")
    ], $http_code);
}
?>
