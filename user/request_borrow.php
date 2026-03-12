<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
require_once __DIR__ . '/../config/database.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipmentId = (int) $_POST['equipment_id'];
    $qty = max(1, (int) $_POST['quantity']);
    $borrowDate = $_POST['borrow_date'];
    $dueDate = $_POST['due_date'];

    $pdo->beginTransaction();
    try {
        $eqStmt = $pdo->prepare('SELECT name, quantity_available FROM equipment WHERE id=? FOR UPDATE');
        $eqStmt->execute([$equipmentId]);
        $eq = $eqStmt->fetch();

        if (!$eq || $eq['quantity_available'] < $qty) {
            throw new RuntimeException('จำนวนอุปกรณ์ไม่เพียงพอ');
        }

        $tx = $pdo->prepare('INSERT INTO borrow_transactions (user_id, borrower_name, phone, organization, borrow_date, due_date, status) VALUES (?, ?, ?, ?, ?, ?, "borrowed")');
        $tx->execute([
            $_SESSION['user']['id'],
            $_SESSION['user']['full_name'],
            trim($_POST['phone']),
            trim($_POST['organization']),
            $borrowDate,
            $dueDate,
        ]);
        $transactionId = (int) $pdo->lastInsertId();

        $item = $pdo->prepare('INSERT INTO borrow_items (transaction_id, equipment_id, quantity) VALUES (?, ?, ?)');
        $item->execute([$transactionId, $equipmentId, $qty]);

        $upd = $pdo->prepare('UPDATE equipment SET quantity_available = quantity_available - ? WHERE id=?');
        $upd->execute([$qty, $equipmentId]);

        $pdo->commit();
        $message = 'ส่งคำขอยืมอุปกรณ์เรียบร้อยแล้ว';
    } catch (Throwable $e) {
        $pdo->rollBack();
        $message = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
}

$equipment = $pdo->query('SELECT id, name, quantity_available FROM equipment WHERE quantity_available > 0 ORDER BY name')->fetchAll();
$selectedEquipment = (int) ($_GET['equipment_id'] ?? 0);
$title = 'แบบฟอร์มยืมอุปกรณ์';
include __DIR__ . '/../includes/header.php';
?>
<div class="container">
    <?php include __DIR__ . '/../includes/user_menu.php'; ?>
    <div class="card hero-card p-4">
        <h3 class="font-weight-bold mb-3">แบบฟอร์มการยืมอุปกรณ์</h3>
        <?php if ($message): ?><div class="alert alert-info"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <form method="post">
            <div class="form-row">
                <div class="form-group col-md-6"><label>เบอร์โทรศัพท์</label><input name="phone" class="form-control" required></div>
                <div class="form-group col-md-6"><label>หน่วยงาน</label><input name="organization" class="form-control"></div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6"><label>อุปกรณ์</label><select name="equipment_id" class="form-control"><?php foreach ($equipment as $e): ?><option value="<?= $e['id'] ?>" <?= $selectedEquipment === (int) $e['id'] ? 'selected' : '' ?>><?= htmlspecialchars($e['name']) ?> (คงเหลือ <?= $e['quantity_available'] ?>)</option><?php endforeach; ?></select></div>
                <div class="form-group col-md-2"><label>จำนวน</label><input type="number" name="quantity" min="1" value="1" class="form-control"></div>
                <div class="form-group col-md-2"><label>วันที่ยืม</label><input type="date" name="borrow_date" class="form-control" required></div>
                <div class="form-group col-md-2"><label>วันที่กำหนดคืน</label><input type="date" name="due_date" class="form-control" required></div>
            </div>
            <button class="btn btn-gold btn-lg btn-block">ยืนยันการขอยืมอุปกรณ์</button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
