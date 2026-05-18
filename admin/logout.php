<?php
session_start();

// Hapus semua session
$_SESSION = [];
session_unset();
session_destroy();

// Redirect ke login
header("Location: /florist/auth/login.php");
exit();

?>