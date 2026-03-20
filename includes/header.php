<?php
require_once __DIR__ . '/bootstrap.php';
$title = $title ?? app_config('app_name');
$user = current_user();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title); ?> | <?= e((string) app_config('app_name')); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?= e(base_url('assets/css/app.css')); ?>">
</head>
<body>
<div class="d-flex" id="wrapper">
    <?php if ($user): ?>
        <aside class="sidebar shadow-sm">
            <div class="sidebar-brand px-4 py-4 text-white">
                <small class="d-block text-uppercase">SKRU Expert DB</small>
                <strong><?= e((string) app_config('app_name')); ?></strong>
            </div>
            <nav class="nav flex-column px-3 pb-4">
                <a class="nav-link" href="<?= e(base_url('dashboard.php')); ?>"><i class="fas fa-chart-pie mr-2"></i>Dashboard</a>
                <a class="nav-link" href="<?= e(base_url('experts.php')); ?>"><i class="fas fa-user-tie mr-2"></i>ผู้เชี่ยวชาญ</a>
                <?php if (in_array($user['role'], ['admin', 'expert'], true)): ?>
                    <a class="nav-link" href="<?= e(base_url('expert_form.php')); ?>"><i class="fas fa-plus-circle mr-2"></i>เพิ่มโปรไฟล์</a>
                <?php endif; ?>
                <span class="badge badge-light mx-3 mt-3 p-2 text-uppercase"><?= e($user['role']); ?></span>
            </nav>
        </aside>
    <?php endif; ?>
    <main class="content flex-fill">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4">
            <div>
                <h1 class="h4 mb-0"><?= e($title); ?></h1>
                <small class="text-muted">ระบบจัดเก็บข้อมูลผู้เชี่ยวชาญเชิงวิชาการและวิชาชีพ</small>
            </div>
            <?php if ($user): ?>
                <div class="ml-auto d-flex align-items-center">
                    <span class="mr-3 text-muted"><i class="fas fa-user-circle mr-1"></i><?= e($user['full_name']); ?></span>
                    <form method="post" action="<?= e(base_url('logout.php')); ?>">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                        <button class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt mr-1"></i>ออกจากระบบ</button>
                    </form>
                </div>
            <?php endif; ?>
        </nav>
        <section class="p-4 fade-in">
            <?php if ($success = flash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show"><?= e($success); ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
            <?php endif; ?>
            <?php if ($error = flash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show"><?= e($error); ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
            <?php endif; ?>
