<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    logout_user();
}

flash('success', 'ออกจากระบบเรียบร้อยแล้ว');
redirect('login.php');
