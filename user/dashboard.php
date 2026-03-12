<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
require_once __DIR__ . '/../config/database.php';

$title = 'หน้าแรกผู้ใช้งาน';
$available = (int) $pdo->query('SELECT SUM(quantity_available) FROM equipment')->fetchColumn();
$myBorrow = $pdo->prepare('SELECT COUNT(*) FROM borrow_transactions WHERE user_id=? AND status IN ("borrowed", "overdue")');
$myBorrow->execute([$_SESSION['user']['id']]);
$myActive = (int) $myBorrow->fetchColumn();
include __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <?php include __DIR__ . '/../includes/user_menu.php'; ?>
    <div class="card hero-card p-4 mb-4">
        <h2 class="font-weight-bold">ระบบยืมคืนอุปกรณ์ศาสนพิธี</h2>
        <p class="text-muted">ใช้งานง่าย ปลอดภัย และตรวจสอบสถานะได้แบบเรียลไทม์</p>
    </div>
    <div class="row">
        <div class="col-md-6"><div class="card stat-card p-3"><h5>อุปกรณ์พร้อมใช้งานทั้งหมด</h5><h1><?= $available ?></h1></div></div>
        <div class="col-md-6"><div class="card stat-card p-3" style="border-left-color:#f39c12"><h5>รายการที่คุณกำลังยืม</h5><h1><?= $myActive ?></h1></div></div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
