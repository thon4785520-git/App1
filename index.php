<?php
session_start();
if (empty($_SESSION['user'])) {
    header('Location: /login.php');
    exit;
}

if ($_SESSION['user']['role'] === 'admin') {
    header('Location: /admin/dashboard.php');
    exit;
}

header('Location: /user/dashboard.php');
exit;
