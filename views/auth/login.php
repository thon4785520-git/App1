<div class="card shadow-lg border-0 auth-card">
    <div class="card-body p-4 p-lg-5">
        <div class="text-center mb-4">
            <div class="icon-circle mx-auto mb-3"><i class="fas fa-university"></i></div>
            <h2 class="h4">เข้าสู่ระบบ</h2>
            <p class="text-muted mb-0">จัดการฐานข้อมูลผู้เชี่ยวชาญอย่างปลอดภัย</p>
        </div>
        <?php if ($message = flash('error')): ?>
            <div class="alert alert-danger"><?= e($message); ?></div>
        <?php endif; ?>
        <form method="post" action="<?= e(base_url('login')); ?>">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
            <div class="form-group">
                <label>อีเมล</label>
                <input type="email" name="email" class="form-control" value="<?= e(old('email')); ?>" required>
            </div>
            <div class="form-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
            <a href="<?= e(base_url('register')); ?>" class="btn btn-link btn-block">สมัครใช้งาน</a>
        </form>
    </div>
</div>
