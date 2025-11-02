<?php
session_start();
header("Content-Type: application/json");
$ok = isset($_SESSION["login"]) && $_SESSION["login"] === true;
echo json_encode(["loggedIn"=>$ok, "role"=>$_SESSION["role"] ?? null, "username"=>$_SESSION["username"] ?? null]);
