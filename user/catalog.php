<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
require_once __DIR__ . '/../config/database.php';

$q = trim($_GET['q'] ?? '');
$sql = 'SELECT e.*, c.name category_name FROM equipment e LEFT JOIN categories c ON c.id=e.category_id';
$params = [];
if ($q !== '') {
    $sql .= ' WHERE e.name LIKE ? OR e.code LIKE ?';
    $params = ["%$q%", "%$q%"];
}
$sql .= ' ORDER BY e.id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();
$title = 'รายการอุปกรณ์';
include __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <?php include __DIR__ . '/../includes/user_menu.php'; ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="font-weight-bold">รายการอุปกรณ์ศาสนพิธี</h3>
        <form class="form-inline" method="get"><input class="form-control" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="ค้นหาอุปกรณ์..."></form>
    </div>
    <div class="row">
        <?php foreach ($items as $item): ?>
            <div class="col-md-4 mb-4">
                <div class="card premium-card h-100">
                    <div class="card-body d-flex flex-column">
                        <span class="badge badge-gold align-self-start mb-2"><?= htmlspecialchars($item['category_name']) ?></span>
                        <h5 class="font-weight-bold"><?= htmlspecialchars($item['name']) ?></h5>
                        <p class="text-muted">รหัส: <?= htmlspecialchars($item['code']) ?></p>
                        <p class="mb-1">คงเหลือ: <?= $item['quantity_available'] ?>/<?= $item['quantity_total'] ?></p>
                        <p class="small text-muted">ที่เก็บ: <?= htmlspecialchars($item['location']) ?></p>
                        <a href="/user/request_borrow.php?equipment_id=<?= $item['id'] ?>" class="btn btn-gold mt-auto">จอง/ยืม</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
