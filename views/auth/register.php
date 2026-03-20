<div class="card shadow-lg border-0 auth-card">
    <div class="card-body p-4 p-lg-5">
        <div class="text-center mb-4">
            <div class="icon-circle mx-auto mb-3"><i class="fas fa-user-plus"></i></div>
            <h2 class="h4">ลงทะเบียนผู้ใช้งาน</h2>
            <p class="text-muted mb-0">รองรับบทบาท Expert และ Viewer</p>
        </div>
        <?php if ($message = flash('error')): ?>
            <div class="alert alert-danger"><?= e($message); ?></div>
        <?php endif; ?>
        <form method="post" action="<?= e(base_url('register')); ?>">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
            <div class="form-group"><label>ชื่อ-นามสกุล</label><input class="form-control" name="full_name" value="<?= e(old('full_name')); ?>" required></div>
            <div class="form-group"><label>อีเมล</label><input type="email" class="form-control" name="email" value="<?= e(old('email')); ?>" required></div>
            <div class="form-group"><label>รหัสผ่าน</label><input type="password" class="form-control" name="password" required></div>
            <div class="form-group">
                <label>บทบาท</label>
                <select class="form-control" name="role">
                    <option value="expert">Expert</option>
                    <option value="viewer">Viewer</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
            <a href="<?= e(base_url('login')); ?>" class="btn btn-link btn-block">มีบัญชีแล้ว</a>
        </form>
    </div>
</div>
