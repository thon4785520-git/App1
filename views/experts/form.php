<?php $isEdit = $expert !== null; ?>
<form method="post" enctype="multipart/form-data" action="<?= e(base_url($isEdit ? 'experts/update' : 'experts/store')); ?>">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()); ?>">
    <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int) $expert['id']; ?>"><?php endif; ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0"><h2 class="h5 mb-0">ข้อมูลส่วนตัว</h2></div>
        <div class="card-body">
            <div class="form-row">
                <div class="col-md-6 form-group"><label>ชื่อ-นามสกุล</label><input class="form-control" name="full_name" value="<?= e($expert['full_name'] ?? old('full_name')); ?>" required></div>
                <div class="col-md-6 form-group"><label>ตำแหน่ง</label><input class="form-control" name="position_title" value="<?= e($expert['position_title'] ?? old('position_title')); ?>"></div>
                <div class="col-md-6 form-group"><label>หน่วยงาน</label><input class="form-control" name="department" value="<?= e($expert['department'] ?? old('department')); ?>"></div>
                <div class="col-md-3 form-group"><label>เบอร์โทร</label><input class="form-control" name="phone" value="<?= e($expert['phone'] ?? old('phone')); ?>"></div>
                <div class="col-md-3 form-group"><label>อีเมล</label><input type="email" class="form-control" name="email" value="<?= e($expert['email'] ?? old('email')); ?>" required></div>
                <div class="col-md-6 form-group"><label>รูปโปรไฟล์</label><input type="file" class="form-control-file" name="profile_image" accept="image/*"></div>
                <div class="col-md-6 form-group"><label>Resume (PDF)</label><input type="file" class="form-control-file" name="resume_file" accept="application/pdf"></div>
                <div class="col-md-12 form-group"><label>ความเชี่ยวชาญพิเศษ</label><textarea class="form-control" rows="3" name="expertise_summary"><?= e($expert['expertise_summary'] ?? old('expertise_summary')); ?></textarea></div>
                <div class="col-md-8 form-group"><label>Skill tags</label><input class="form-control" id="skillInput" name="skills" value="<?= e($isEdit ? implode(', ', array_column($expert['skills'], 'name')) : old('skills')); ?>" placeholder="PHP, Data Analytics, AI"></div>
                <div class="col-md-4 form-group"><label>Portfolio URL</label><input class="form-control" name="portfolio_url" value="<?= e($expert['portfolio_url'] ?? old('portfolio_url')); ?>"></div>
                <?php if ($isEdit && (Auth::user()['role'] ?? '') === 'admin'): ?>
                    <div class="col-md-4 form-group"><label>สถานะอนุมัติ</label><select class="form-control" name="approval_status"><option value="approved" <?= ($expert['approval_status'] ?? '') === 'approved' ? 'selected' : ''; ?>>Approved</option><option value="pending" <?= ($expert['approval_status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option></select></div>
                <?php endif; ?>
            </div>
            <div id="skillSuggestions" class="small text-muted"></div>
        </div>
    </div>

    <?php
    $sections = [
        'work_experience' => ['title' => 'ข้อมูลการปฏิบัติงาน', 'fields' => ['organization' => 'องค์กร/หน่วยงาน', 'project_name' => 'โครงการ', 'role_title' => 'บทบาทหน้าที่', 'start_date' => 'เริ่ม', 'end_date' => 'สิ้นสุด', 'description' => 'รายละเอียด']],
        'research' => ['title' => 'ข้อมูลด้านวิชาการ', 'fields' => ['category' => 'ประเภท', 'title' => 'ชื่อผลงาน', 'publication_name' => 'แหล่งเผยแพร่', 'published_year' => 'ปี', 'description' => 'รายละเอียด', 'link_url' => 'ลิงก์']],
        'training' => ['title' => 'ข้อมูลการพัฒนาตนเอง', 'fields' => ['course_name' => 'หลักสูตร', 'provider_name' => 'หน่วยงาน', 'certificate_name' => 'ใบประกาศ', 'start_date' => 'เริ่ม', 'end_date' => 'สิ้นสุด', 'description' => 'รายละเอียด']],
        'awards' => ['title' => 'ข้อมูลอื่นๆ / รางวัล', 'fields' => ['title' => 'ชื่อรางวัล', 'issuer_name' => 'หน่วยงาน', 'award_year' => 'ปี', 'description' => 'รายละเอียด']],
        'social_links' => ['title' => 'Social Links', 'fields' => ['platform_name' => 'แพลตฟอร์ม', 'link_url' => 'URL']],
    ];
    ?>
    <?php foreach ($sections as $key => $section): ?>
        <?php $rows = $expert[$key] ?? [array_fill_keys(array_keys($section['fields']), '')]; ?>
        <div class="card border-0 shadow-sm mb-4 repeater" data-section="<?= e($key); ?>">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h2 class="h5 mb-0"><?= e($section['title']); ?></h2>
                <button type="button" class="btn btn-outline-primary btn-sm repeater-add"><i class="fas fa-plus mr-1"></i>เพิ่มรายการ</button>
            </div>
            <div class="card-body repeater-container">
                <?php foreach ($rows as $index => $row): ?>
                    <div class="border rounded p-3 mb-3 repeater-item">
                        <div class="form-row">
                            <?php foreach ($section['fields'] as $field => $label): ?>
                                <div class="col-md-<?= count($section['fields']) <= 2 ? 6 : 4; ?> form-group">
                                    <label><?= e($label); ?></label>
                                    <input class="form-control" name="<?= e($key); ?>[<?= $index; ?>][<?= e($field); ?>]" value="<?= e($row[$field] ?? ''); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm repeater-remove"><i class="fas fa-trash mr-1"></i>ลบ</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <button class="btn btn-primary btn-lg"><i class="fas fa-save mr-1"></i><?= $isEdit ? 'บันทึกการแก้ไข' : 'บันทึกโปรไฟล์'; ?></button>
</form>
