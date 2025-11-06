<?php
session_start();
session_destroy();
header("Location: /sayhi/login.html");
exit;
?>