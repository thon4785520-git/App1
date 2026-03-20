<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form class="form-row align-items-end">
            <div class="col-md-5 form-group">
                <label>ค้นหา</label>
                <input type="text" class="form-control" name="keyword" placeholder="ชื่อ ตำแหน่ง หรือความเชี่ยวชาญ" value="<?= e($filters['keyword']); ?>">
            </div>
            <div class="col-md-4 form-group">
                <label>Skill Filter</label>
                <input type="text" class="form-control" name="skill" placeholder="เช่น Data Analytics" value="<?= e($filters['skill']); ?>">
            </div>
            <div class="col-md-3 form-group d-flex">
                <button class="btn btn-primary mr-2 flex-fill"><i class="fas fa-search mr-1"></i>ค้นหา</button>
                <a href="<?= e(base_url('experts')); ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <?php foreach ($result['data'] as $expert): ?>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card expert-card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="h5 mb-1"><?= e($expert['full_name']); ?></h3>
                            <p class="text-primary mb-1"><?= e($expert['position_title']); ?></p>
                            <p class="text-muted small mb-0"><?= e($expert['department']); ?></p>
                        </div>
                        <span class="badge badge-pill badge-<?= $expert['approval_status'] === 'approved' ? 'success' : 'warning'; ?>"><?= e($expert['approval_status']); ?></span>
                    </div>
                    <p class="text-muted mb-3"><i class="fas fa-envelope mr-2"></i><?= e($expert['email']); ?></p>
                    <p class="small flex-fill"><?= e($expert['expertise_summary']); ?></p>
                    <div class="mb-3">
                        <?php foreach (array_filter(array_map('trim', explode(',', (string) ($expert['skills'] ?? '')))) as $tag): ?>
                            <span class="badge badge-info badge-pill mr-1 mb-1"><?= e($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="d-flex flex-wrap mt-auto">
                        <a href="<?= e(base_url('experts/show?id=' . $expert['id'])); ?>" class="btn btn-outline-primary btn-sm mr-2 mb-2">รายละเอียด</a>
                        <?php if (in_array($user['role'], ['admin', 'expert'], true) && ($user['role'] === 'admin' || (int) $user['id'] === (int) $expert['user_id'])): ?>
                            <a href="<?= e(base_url('experts/edit?id=' . $expert['id'])); ?>" class="btn btn-outline-secondary btn-sm mr-2 mb-2">แก้ไข</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<nav>
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $result['last_page']; $i++): ?>
            <li class="page-item <?= $i === $result['page'] ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?= $i; ?>&keyword=<?= urlencode($filters['keyword']); ?>&skill=<?= urlencode($filters['skill']); ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
