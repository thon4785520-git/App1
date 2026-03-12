<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, full_name, role) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        trim($_POST['username']),
        password_hash($_POST['password'], PASSWORD_DEFAULT),
        trim($_POST['full_name']),
        $_POST['role'] === 'admin' ? 'admin' : 'user',
    ]);
    header('Location: /admin/users.php');
    exit;
}

$users = $pdo->query('SELECT id, username, full_name, role, created_at FROM users ORDER BY id DESC')->fetchAll();
$title = 'จัดการข้อมูลผู้ใช้งาน';
include __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <?php include __DIR__ . '/../includes/admin_menu.php'; ?>
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="font-weight-bold">จัดการข้อมูลผู้ใช้งาน</h3>
        </div>
        <div class="card premium-card mb-4 p-3">
            <form method="post" class="form-row">
                <div class="col-md-3"><input name="username" class="form-control" placeholder="Username" required></div>
                <div class="col-md-3"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
                <div class="col-md-3"><input name="full_name" class="form-control" placeholder="ชื่อ-นามสกุล" required></div>
                <div class="col-md-2">
                    <select name="role" class="form-control"><option value="user">User</option><option value="admin">Admin</option></select>
                </div>
                <div class="col-md-1"><button class="btn btn-gold btn-block">เพิ่ม</button></div>
            </form>
        </div>
        <div class="card premium-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>#</th><th>Username</th><th>ชื่อ</th><th>สิทธิ์</th><th>วันที่สร้าง</th></tr></thead>
                    <tbody><?php foreach ($users as $u): ?><tr><td><?= $u['id'] ?></td><td><?= htmlspecialchars($u['username']) ?></td><td><?= htmlspecialchars($u['full_name']) ?></td><td><span class="badge badge-gold"><?= htmlspecialchars($u['role']) ?></span></td><td><?= $u['created_at'] ?></td></tr><?php endforeach; ?></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
