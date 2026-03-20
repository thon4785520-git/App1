<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <img src="<?= e($expert['profile_image'] ? base_url($expert['profile_image']) : 'https://via.placeholder.com/180x180.png?text=Expert'); ?>" class="rounded-circle img-fluid profile-photo mb-3" alt="profile">
                <span class="badge badge-pill badge-<?= $expert['approval_status'] === 'approved' ? 'success' : 'warning'; ?> mb-3"><?= e($expert['approval_status']); ?></span>
                <h2 class="h4"><?= e($expert['full_name']); ?></h2>
                <p class="text-primary mb-1"><?= e($expert['position_title']); ?></p>
                <p class="text-muted"><?= e($expert['department']); ?></p>
                <p><i class="fas fa-phone mr-2"></i><?= e($expert['phone']); ?></p>
                <p><i class="fas fa-envelope mr-2"></i><?= e($expert['email']); ?></p>
                <?php if ($expert['resume_file']): ?><a href="<?= e(base_url($expert['resume_file'])); ?>" target="_blank" class="btn btn-outline-primary btn-sm mr-2 mb-2"><i class="fas fa-file-pdf mr-1"></i>Resume</a><?php endif; ?>
                <a href="<?= e(base_url('experts/export?id=' . $expert['id'])); ?>" class="btn btn-primary btn-sm mb-2"><i class="fas fa-file-export mr-1"></i>Export PDF</a>
                <?php if ((Auth::user()['role'] ?? '') === 'admin' && $expert['approval_status'] !== 'approved'): ?>
                    <form method="post" action="<?= e(base_url('experts/approve')); ?>" class="mt-3">
                        <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
                        <input type="hidden" name="id" value="<?= (int) $expert['id']; ?>">
                        <button class="btn btn-success btn-block"><i class="fas fa-check mr-1"></i>อนุมัติโปรไฟล์</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h3 class="h5">ความเชี่ยวชาญพิเศษ</h3>
                <p><?= e($expert['expertise_summary']); ?></p>
                <div>
                    <?php foreach ($expert['skills'] as $skill): ?>
                        <span class="badge badge-info badge-pill mr-1 mb-1"><?= e($skill['name']); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php foreach (['work_experience' => 'ข้อมูลการปฏิบัติงาน', 'research' => 'ข้อมูลด้านวิชาการ', 'training' => 'ข้อมูลการพัฒนาตนเอง', 'awards' => 'รางวัล', 'social_links' => 'Social Links'] as $key => $label): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0"><h3 class="h5 mb-0"><?= e($label); ?></h3></div>
                <div class="card-body">
                    <?php if (empty($expert[$key])): ?>
                        <p class="text-muted mb-0">ยังไม่มีข้อมูล</p>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($expert[$key] as $item): ?>
                                <div class="timeline-item mb-3">
                                    <div class="font-weight-bold"><?= e($item['title'] ?? $item['course_name'] ?? $item['organization'] ?? $item['platform_name'] ?? $item['category']); ?></div>
                                    <div class="text-muted small"><?= e($item['description'] ?? $item['project_name'] ?? $item['publication_name'] ?? $item['certificate_name'] ?? $item['link_url'] ?? ''); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
