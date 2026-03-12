<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;
$title = $title ?? 'ระบบสารสนเทศยืมคืนอุปกรณ์ศาสนพิธี';
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar shadow-sm">
    <a class="navbar-brand font-weight-bold" href="/index.php"><i class="fas fa-gopuram mr-2"></i>ระบบศาสนพิธี</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ml-auto align-items-lg-center">
            <?php if ($user): ?>
                <li class="nav-item mr-lg-3 text-light small">สวัสดี, <?= htmlspecialchars($user['full_name']) ?> (<?= htmlspecialchars($user['role']) ?>)</li>
                <li class="nav-item"><a href="/logout.php" class="btn btn-outline-light btn-sm">ออกจากระบบ</a></li>
            <?php else: ?>
                <li class="nav-item"><a href="/login.php" class="btn btn-warning btn-sm">เข้าสู่ระบบ</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<main class="container-fluid py-4">
