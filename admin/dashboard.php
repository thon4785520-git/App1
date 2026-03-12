<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../config/database.php';

$title = 'แดชบอร์ดผู้ดูแล';
$totalEquipment = (int) $pdo->query('SELECT COUNT(*) FROM equipment')->fetchColumn();
$borrowed = (int) $pdo->query("SELECT COUNT(*) FROM borrow_transactions WHERE status='borrowed'")->fetchColumn();
$overdue = (int) $pdo->query("SELECT COUNT(*) FROM borrow_transactions WHERE status='overdue'")->fetchColumn();
$latest = $pdo->query('SELECT borrower_name, borrow_date, due_date, status FROM borrow_transactions ORDER BY id DESC LIMIT 5')->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <?php include __DIR__ . '/../includes/admin_menu.php'; ?>
    <div class="col-lg-10">
        <h3 class="font-weight-bold mb-4">แดชบอร์ดสรุปภาพรวม</h3>
        <div class="row mb-4">
            <div class="col-md-4"><div class="card stat-card p-3"><h6>อุปกรณ์ทั้งหมด</h6><h2><?= $totalEquipment ?></h2></div></div>
            <div class="col-md-4"><div class="card stat-card p-3" style="border-left-color:#f39c12"><h6>กำลังถูกยืม</h6><h2><?= $borrowed ?></h2></div></div>
            <div class="col-md-4"><div class="card stat-card p-3" style="border-left-color:#dc3545"><h6>ค้างส่งคืน</h6><h2 class="text-danger"><?= $overdue ?></h2></div></div>
        </div>
        <div class="card premium-card">
            <div class="card-header">กิจกรรมล่าสุด</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>ผู้ยืม</th><th>วันที่ยืม</th><th>กำหนดคืน</th><th>สถานะ</th></tr></thead>
                    <tbody>
                    <?php foreach ($latest as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['borrower_name']) ?></td>
                            <td><?= htmlspecialchars($row['borrow_date']) ?></td>
                            <td><?= htmlspecialchars($row['due_date']) ?></td>
                            <td><span class="badge badge-pill <?= $row['status'] === 'returned' ? 'badge-success' : ($row['status'] === 'overdue' ? 'badge-danger' : 'badge-warning') ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
