<?php
session_start();
require_once __DIR__ . '/config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, password_hash, full_name, role FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'role' => $user['role'],
        ];

        header('Location: /index.php');
        exit;
    }
    $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
}

$title = 'เข้าสู่ระบบ';
include __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card hero-card p-4">
            <h3 class="font-weight-bold mb-3">เข้าสู่ระบบระบบยืมคืนอุปกรณ์ศาสนพิธี</h3>
            <p class="text-muted">สำหรับผู้ดูแลระบบและผู้ใช้งานทั่วไป</p>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label>ชื่อผู้ใช้</label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="form-group">
                    <label>รหัสผ่าน</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button class="btn btn-gold btn-block">เข้าสู่ระบบ</button>
            </form>
            <small class="text-muted mt-3 d-block">บัญชีตัวอย่าง: admin / password และ user1 / password</small>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
