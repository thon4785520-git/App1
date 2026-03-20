<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/expert_repository.php';
require_login();

$title = 'Dashboard';
$stats = dashboard_summary();
$experts = latest_experts();
require __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <?php foreach ([
        ['label' => 'ผู้เชี่ยวชาญทั้งหมด', 'value' => $stats['experts'], 'icon' => 'fa-user-tie'],
        ['label' => 'โปรไฟล์ที่อนุมัติแล้ว', 'value' => $stats['approved'], 'icon' => 'fa-check-circle'],
        ['label' => 'รออนุมัติ', 'value' => $stats['pending'], 'icon' => 'fa-hourglass-half'],
        ['label' => 'ผลงานวิจัย/บทความ', 'value' => $stats['research'], 'icon' => 'fa-flask'],
    ] as $card): ?>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card dashboard-stat border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon mr-3"><i class="fas <?= e($card['icon']); ?>"></i></div>
                    <div>
                        <div class="text-muted small"><?= e($card['label']); ?></div>
                        <div class="h3 mb-0"><?= e((string) $card['value']); ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="h5 mb-1">ผู้เชี่ยวชาญล่าสุด</h2>
            <p class="text-muted mb-0">ดูข้อมูลสรุปของบุคลากรล่าสุดในระบบ</p>
        </div>
        <a href="<?= e(base_url('experts.php')); ?>" class="btn btn-primary btn-sm">ดูทั้งหมด</a>
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach ($experts as $expert): ?>
                <div class="col-md-6 col-xl-3 mb-4">
                    <div class="card profile-mini h-100 border-0 shadow-hover">
                        <div class="card-body">
                            <span class="badge badge-pill badge-<?= $expert['approval_status'] === 'approved' ? 'success' : 'warning'; ?> mb-3"><?= e($expert['approval_status']); ?></span>
                            <h3 class="h6 mb-1"><?= e($expert['full_name']); ?></h3>
                            <p class="text-primary mb-1"><?= e($expert['position_title']); ?></p>
                            <p class="text-muted small mb-3"><?= e($expert['department']); ?></p>
                            <p class="small mb-3"><?= e($expert['skills'] ?? '-'); ?></p>
                            <a href="<?= e(base_url('expert_view.php?id=' . $expert['id'])); ?>" class="btn btn-outline-primary btn-sm">รายละเอียด</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
