<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO categories (name, description) VALUES (?, ?)');
    $stmt->execute([trim($_POST['name']), trim($_POST['description'])]);
    header('Location: /admin/categories.php');
    exit;
}
$rows = $pdo->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll();
$title = 'จัดการข้อมูลประเภท';
include __DIR__ . '/../includes/header.php';
?>
<div class="row"><?php include __DIR__ . '/../includes/admin_menu.php'; ?><div class="col-lg-10">
    <h3 class="font-weight-bold mb-3">จัดการข้อมูลประเภท</h3>
    <div class="card premium-card mb-4 p-3">
        <form method="post" class="form-row">
            <div class="col-md-4"><input name="name" class="form-control" placeholder="ชื่อประเภท" required></div>
            <div class="col-md-6"><input name="description" class="form-control" placeholder="รายละเอียด"></div>
            <div class="col-md-2"><button class="btn btn-gold btn-block">บันทึก</button></div>
        </form>
    </div>
    <div class="card premium-card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>#</th><th>ประเภท</th><th>รายละเอียด</th></tr></thead><tbody><?php foreach ($rows as $r): ?><tr><td><?= $r['id'] ?></td><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['description']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
