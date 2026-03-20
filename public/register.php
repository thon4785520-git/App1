<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/expert_repository.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    remember_old_input($_POST);

    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $email = filter_var((string) ($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = (string) ($_POST['password'] ?? '');
    $role = in_array($_POST['role'] ?? '', ['expert', 'viewer'], true) ? $_POST['role'] : 'viewer';

    if ($fullName === '' || !$email || strlen($password) < 8) {
        flash('error', 'กรุณากรอกข้อมูลให้ครบ และตั้งรหัสผ่านอย่างน้อย 8 ตัวอักษร');
        redirect('register.php');
    }

    if (find_user_by_email($email)) {
        flash('error', 'อีเมลนี้ถูกใช้งานแล้ว');
        redirect('register.php');
    }

    create_user([
        'full_name' => $fullName,
        'email' => $email,
        'password' => $password,
        'role' => $role,
    ]);

    $user = find_user_by_email($email);
    login_user($user);
    clear_old_input();
    flash('success', 'ลงทะเบียนสำเร็จ กรุณาเพิ่มโปรไฟล์ของคุณ');
    redirect('expert_form.php');
}

$title = 'ลงทะเบียน';
require __DIR__ . '/../includes/header.php';
?>
<div class="container-fluid auth-screen">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card border-0 shadow-lg auth-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="text-center mb-4">
                        <div class="icon-circle mx-auto mb-3"><i class="fas fa-user-plus"></i></div>
                        <h2 class="h4">ลงทะเบียนผู้ใช้งาน</h2>
                        <p class="text-muted mb-0">รองรับบทบาท Expert และ Viewer</p>
                    </div>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                        <div class="form-group"><label>ชื่อ-นามสกุล</label><input class="form-control" name="full_name" value="<?= e(old('full_name')); ?>" required></div>
                        <div class="form-group"><label>อีเมล</label><input type="email" class="form-control" name="email" value="<?= e(old('email')); ?>" required></div>
                        <div class="form-group"><label>รหัสผ่าน</label><input type="password" class="form-control" name="password" required></div>
                        <div class="form-group"><label>บทบาท</label><select class="form-control" name="role"><option value="expert">Expert</option><option value="viewer">Viewer</option></select></div>
                        <button class="btn btn-primary btn-block">Register</button>
                        <a href="<?= e(base_url('login.php')); ?>" class="btn btn-link btn-block">มีบัญชีแล้ว</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
