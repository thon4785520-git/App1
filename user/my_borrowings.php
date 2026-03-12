<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
require_once __DIR__ . '/../config/database.php';

$sql = 'SELECT bt.id, bt.borrow_date, bt.due_date, bt.return_date, bt.status,
               GROUP_CONCAT(CONCAT(e.name, " (", bi.quantity, ")") SEPARATOR ", ") AS items
        FROM borrow_transactions bt
        LEFT JOIN borrow_items bi ON bi.transaction_id = bt.id
        LEFT JOIN equipment e ON e.id = bi.equipment_id
        WHERE bt.user_id = ?
        GROUP BY bt.id
        ORDER BY bt.id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user']['id']]);
$rows = $stmt->fetchAll();
$title = 'ประวัติการยืม-คืน';
include __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <?php include __DIR__ . '/../includes/user_menu.php'; ?>
    <h3 class="font-weight-bold mb-3">ประวัติการยืม-คืน</h3>
    <div class="card premium-card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>#</th><th>รายการอุปกรณ์</th><th>วันที่ยืม</th><th>กำหนดคืน</th><th>สถานะ</th></tr></thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['items'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($r['borrow_date']) ?></td>
                        <td><?= htmlspecialchars($r['due_date']) ?></td>
                        <td><span class="badge badge-pill <?= $r['status'] === 'returned' ? 'badge-success' : ($r['status'] === 'overdue' ? 'badge-danger' : 'badge-warning') ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
