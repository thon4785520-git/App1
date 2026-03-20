<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/expert_repository.php';
require_login();

$expert = find_expert((int) ($_GET['id'] ?? 0));
if (!$expert) {
    flash('error', 'ไม่พบข้อมูลผู้เชี่ยวชาญ');
    redirect('experts.php');
}

$title = $expert['full_name'];
require __DIR__ . '/../includes/header.php';
?>
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <img src="<?= e($expert['profile_image'] ? base_url($expert['profile_image']) : 'https://via.placeholder.com/180x180.png?text=Expert'); ?>" alt="profile" class="rounded-circle img-fluid profile-photo mb-3">
                <span class="badge badge-pill badge-<?= $expert['approval_status'] === 'approved' ? 'success' : 'warning'; ?> mb-3"><?= e($expert['approval_status']); ?></span>
                <h2 class="h4"><?= e($expert['full_name']); ?></h2>
                <p class="text-primary mb-1"><?= e($expert['position_title']); ?></p>
                <p class="text-muted"><?= e($expert['department']); ?></p>
                <p><i class="fas fa-phone mr-2"></i><?= e($expert['phone']); ?></p>
                <p><i class="fas fa-envelope mr-2"></i><?= e($expert['email']); ?></p>
                <?php if ($expert['resume_file']): ?>
                    <a href="<?= e(base_url($expert['resume_file'])); ?>" target="_blank" class="btn btn-outline-primary btn-sm mr-2 mb-2"><i class="fas fa-file-pdf mr-1"></i>Resume</a>
                <?php endif; ?>
                <a href="<?= e(base_url('export_profile.php?id=' . $expert['id'])); ?>" class="btn btn-primary btn-sm mb-2"><i class="fas fa-file-export mr-1"></i>Export PDF</a>
                <?php if (current_user()['role'] === 'admin' && $expert['approval_status'] !== 'approved'): ?>
                    <form method="post" action="<?= e(base_url('actions/approve_expert.php')); ?>" class="mt-3">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()); ?>">
                        <input type="hidden" name="id" value="<?= (int) $expert['id']; ?>">
                        <button class="btn btn-success btn-block"><i class="fas fa-check mr-1"></i>อนุมัติโปรไฟล์</button>
                    </form>
                <?php endif; ?>
                <?php if (expert_owner_can_edit($expert)): ?>
                    <a href="<?= e(base_url('expert_form.php?id=' . $expert['id'])); ?>" class="btn btn-outline-secondary btn-sm btn-block mt-2">แก้ไขข้อมูล</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h3 class="h5">ความเชี่ยวชาญพิเศษ</h3>
                <p><?= e($expert['expertise_summary']); ?></p>
                <?php foreach ($expert['skills'] as $skill): ?>
                    <span class="badge badge-info badge-pill mr-1 mb-1"><?= e($skill['name']); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php foreach ([
            'work_experience' => ['label' => 'ข้อมูลการปฏิบัติงาน', 'title' => 'organization', 'detail' => 'description'],
            'research' => ['label' => 'ข้อมูลด้านวิชาการ', 'title' => 'title', 'detail' => 'publication_name'],
            'training' => ['label' => 'ข้อมูลการพัฒนาตนเอง', 'title' => 'course_name', 'detail' => 'certificate_name'],
            'seminars' => ['label' => 'ประวัติการเข้าร่วมสัมมนา', 'title' => 'seminar_name', 'detail' => 'organizer_name'],
            'awards' => ['label' => 'รางวัล', 'title' => 'title', 'detail' => 'issuer_name'],
            'social_links' => ['label' => 'Social Links', 'title' => 'platform_name', 'detail' => 'link_url'],
        ] as $key => $map): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0"><h3 class="h5 mb-0"><?= e($map['label']); ?></h3></div>
                <div class="card-body">
                    <?php if (empty($expert[$key])): ?>
                        <p class="text-muted mb-0">ยังไม่มีข้อมูล</p>
                    <?php else: ?>
                        <?php foreach ($expert[$key] as $row): ?>
                            <div class="timeline-item mb-3">
                                <div class="font-weight-bold"><?= e($row[$map['title']] ?? '-'); ?></div>
                                <div class="text-muted small"><?= e(($row[$map['detail']] ?? '') . ' ' . ($row['description'] ?? '')); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
