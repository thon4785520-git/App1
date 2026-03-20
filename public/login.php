<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/expert_repository.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    remember_old_input($_POST);

    $email = filter_var((string) ($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = (string) ($_POST['password'] ?? '');
    $user = $email ? find_user_by_email($email) : null;

    if (!$user || !password_verify($password, $user['password_hash'])) {
        flash('error', 'อีเมลหรือรหัสผ่านไม่ถูกต้อง');
        redirect('login.php');
    }

    login_user($user);
    clear_old_input();
    flash('success', 'เข้าสู่ระบบสำเร็จ');
    redirect('dashboard.php');
}

$title = 'เข้าสู่ระบบ';
require __DIR__ . '/../includes/header.php';
?>
<div class="container-fluid auth-screen">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card border-0 shadow-lg auth-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="text-center mb-4">
                        <div class="icon-circle mx-auto mb-3"><i class="fas fa-university"></i></div>
                        <h2 class="h4">เข้าสู่ระบบ</h2>
                        <p class="text-muted mb-0">จัดการข้อมูลผู้เชี่ยวชาญอย่างปลอดภัย</p>
                    </div>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                        <div class="form-group">
                            <label>อีเมล</label>
                            <input type="email" class="form-control" name="email" value="<?= e(old('email')); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>รหัสผ่าน</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button class="btn btn-primary btn-block">Login</button>
                        <a href="<?= e(base_url('register.php')); ?>" class="btn btn-link btn-block">สมัครใช้งาน</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
