<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO equipment (category_id, code, name, quantity_total, quantity_available, location) VALUES (?, ?, ?, ?, ?, ?)');
    $qty = (int) $_POST['quantity_total'];
    $stmt->execute([
        (int) $_POST['category_id'],
        trim($_POST['code']),
        trim($_POST['name']),
        $qty,
        $qty,
        trim($_POST['location']),
    ]);
    header('Location: /admin/equipment.php');
    exit;
}

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$items = $pdo->query('SELECT e.*, c.name category_name FROM equipment e LEFT JOIN categories c ON c.id=e.category_id ORDER BY e.id DESC')->fetchAll();
$title = 'จัดการข้อมูลอุปกรณ์';
include __DIR__ . '/../includes/header.php';
?>
<div class="row"><?php include __DIR__ . '/../includes/admin_menu.php'; ?><div class="col-lg-10">
    <h3 class="font-weight-bold mb-3">จัดการข้อมูลอุปกรณ์</h3>
    <div class="card premium-card mb-4 p-3">
        <form method="post" class="form-row">
            <div class="col-md-2"><input name="code" class="form-control" placeholder="รหัส" required></div>
            <div class="col-md-3"><input name="name" class="form-control" placeholder="ชื่ออุปกรณ์" required></div>
            <div class="col-md-2"><select name="category_id" class="form-control"><?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><input type="number" name="quantity_total" class="form-control" min="1" value="1" required></div>
            <div class="col-md-2"><input name="location" class="form-control" placeholder="ที่เก็บ"></div>
            <div class="col-md-1"><button class="btn btn-gold btn-block">เพิ่ม</button></div>
        </form>
    </div>
    <div class="card premium-card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>รหัส</th><th>ชื่อ</th><th>ประเภท</th><th>คงเหลือ</th><th>ที่เก็บ</th></tr></thead><tbody><?php foreach ($items as $i): ?><tr><td><?= htmlspecialchars($i['code']) ?></td><td><?= htmlspecialchars($i['name']) ?></td><td><?= htmlspecialchars($i['category_name']) ?></td><td><?= $i['quantity_available'] ?>/<?= $i['quantity_total'] ?></td><td><?= htmlspecialchars($i['location']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
</div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
