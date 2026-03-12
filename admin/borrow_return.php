<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'mark_return') {
    $id = (int) $_POST['transaction_id'];
    $pdo->prepare("UPDATE borrow_transactions SET status='returned', return_date=CURDATE() WHERE id=?")->execute([$id]);
    header('Location: /admin/borrow_return.php');
    exit;
}

$rows = $pdo->query('SELECT * FROM borrow_transactions ORDER BY id DESC')->fetchAll();
$title = 'จัดการการยืมคืน';
include __DIR__ . '/../includes/header.php';
?>
<div class="row"><?php include __DIR__ . '/../includes/admin_menu.php'; ?><div class="col-lg-10">
    <h3 class="font-weight-bold mb-3">จัดการการยืมคืน</h3>
    <div class="card premium-card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>ผู้ยืม</th><th>วันที่ยืม</th><th>กำหนดคืน</th><th>สถานะ</th><th>จัดการ</th></tr></thead><tbody><?php foreach ($rows as $r): ?><tr><td><?= htmlspecialchars($r['borrower_name']) ?></td><td><?= $r['borrow_date'] ?></td><td><?= $r['due_date'] ?></td><td><span class="badge badge-pill <?= $r['status'] === 'returned' ? 'badge-success' : ($r['status'] === 'overdue' ? 'badge-danger' : 'badge-warning') ?>"><?= $r['status'] ?></span></td><td><?php if ($r['status'] !== 'returned'): ?><form method="post" class="d-inline"><input type="hidden" name="transaction_id" value="<?= $r['id'] ?>"><input type="hidden" name="action" value="mark_return"><button class="btn btn-sm btn-outline-success">บันทึกคืน</button></form><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
