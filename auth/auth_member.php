<?php
// auth/auth_member.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: ../login.php");
    exit;
}
?>