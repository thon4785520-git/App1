<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../config/database.php';

$monthly = $pdo->query('SELECT DATE_FORMAT(borrow_date, "%Y-%m") month, COUNT(*) total FROM borrow_transactions GROUP BY month ORDER BY month DESC LIMIT 6')->fetchAll();
$popular = $pdo->query('SELECT e.name, SUM(bi.quantity) qty FROM borrow_items bi JOIN equipment e ON e.id=bi.equipment_id GROUP BY e.id ORDER BY qty DESC LIMIT 10')->fetchAll();
$title = 'ระบบรายงาน';
include __DIR__ . '/../includes/header.php';
?>
<div class="row"><?php include __DIR__ . '/../includes/admin_menu.php'; ?><div class="col-lg-10">
    <h3 class="font-weight-bold mb-3">ระบบรายงาน</h3>
    <div class="row">
        <div class="col-md-6 mb-3"><div class="card premium-card"><div class="card-header">จำนวนการยืมรายเดือน</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>เดือน</th><th>จำนวนรายการ</th></tr></thead><tbody><?php foreach ($monthly as $m): ?><tr><td><?= $m['month'] ?></td><td><?= $m['total'] ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
        <div class="col-md-6 mb-3"><div class="card premium-card"><div class="card-header">อุปกรณ์ยอดนิยม</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>อุปกรณ์</th><th>จำนวนที่ถูกยืม</th></tr></thead><tbody><?php foreach ($popular as $p): ?><tr><td><?= htmlspecialchars($p['name']) ?></td><td><?= $p['qty'] ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
    </div>
</div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
