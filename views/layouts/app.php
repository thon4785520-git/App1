<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? APP_NAME); ?> | <?= e(APP_NAME); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?= e(base_url('assets/css/app.css')); ?>">
</head>
<body>
<div class="d-flex" id="wrapper">
    <aside class="sidebar shadow-sm">
        <div class="sidebar-brand px-4 py-4 text-white">
            <small class="d-block text-uppercase">Expert DB</small>
            <strong><?= e(APP_NAME); ?></strong>
        </div>
        <nav class="nav flex-column px-3 pb-4">
            <a class="nav-link" href="<?= e(base_url('dashboard')); ?>"><i class="fas fa-chart-pie mr-2"></i> Dashboard</a>
            <a class="nav-link" href="<?= e(base_url('experts')); ?>"><i class="fas fa-user-tie mr-2"></i> ผู้เชี่ยวชาญ</a>
            <?php if (in_array(Auth::user()['role'] ?? '', ['admin', 'expert'], true)): ?>
                <a class="nav-link" href="<?= e(base_url('experts/create')); ?>"><i class="fas fa-plus-circle mr-2"></i> เพิ่มโปรไฟล์</a>
            <?php endif; ?>
            <div class="small text-uppercase text-white-50 px-3 mt-4">สิทธิ์ใช้งาน</div>
            <span class="badge badge-light mx-3 mt-2 p-2 text-uppercase"><?= e(Auth::user()['role'] ?? 'guest'); ?></span>
        </nav>
    </aside>
    <main class="content flex-fill">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4">
            <div>
                <h1 class="h4 mb-0"><?= e($title ?? 'Dashboard'); ?></h1>
                <small class="text-muted">ฐานข้อมูลบุคลากรผู้เชี่ยวชาญเชิงวิชาการและวิชาชีพ</small>
            </div>
            <div class="ml-auto d-flex align-items-center">
                <span class="mr-3 text-muted"><i class="fas fa-user-circle mr-1"></i><?= e(Auth::user()['full_name'] ?? ''); ?></span>
                <form method="post" action="<?= e(base_url('logout')); ?>">
                    <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt mr-1"></i>ออกจากระบบ</button>
                </form>
            </div>
        </nav>
        <section class="p-4 fade-in">
            <?php if ($message = flash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert"><?= e($message); ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
            <?php endif; ?>
            <?php if ($message = flash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert"><?= e($message); ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
            <?php endif; ?>
            <?= $content; ?>
        </section>
    </main>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(base_url('assets/js/app.js')); ?>"></script>
</body>
</html>
