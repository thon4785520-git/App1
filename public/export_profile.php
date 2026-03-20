<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/expert_repository.php';
require_login();

$expert = find_expert((int) ($_GET['id'] ?? 0));
if (!$expert) {
    flash('error', 'ไม่พบข้อมูลสำหรับส่งออก');
    redirect('experts.php');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Export Profile</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 32px; color: #1f2937; }
        h1, h2 { margin-bottom: 8px; }
        .section { margin-top: 24px; }
        .badge { display: inline-block; padding: 4px 10px; background: #e0f2fe; border-radius: 999px; margin-right: 6px; }
    </style>
</head>
<body onload="window.print()">
    <h1><?= e($expert['full_name']); ?></h1>
    <p><?= e($expert['position_title']); ?> | <?= e($expert['department']); ?></p>
    <p><?= e($expert['email']); ?> | <?= e($expert['phone']); ?></p>
    <div class="section">
        <h2>ความเชี่ยวชาญ</h2>
        <p><?= e($expert['expertise_summary']); ?></p>
        <?php foreach ($expert['skills'] as $skill): ?><span class="badge"><?= e($skill['name']); ?></span><?php endforeach; ?>
    </div>
    <?php foreach (['work_experience' => 'ประสบการณ์ทำงาน', 'research' => 'ผลงานวิชาการ', 'training' => 'การอบรม', 'seminars' => 'การสัมมนา', 'awards' => 'รางวัล'] as $key => $label): ?>
        <div class="section">
            <h2><?= e($label); ?></h2>
            <?php if (empty($expert[$key])): ?>
                <p>-</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($expert[$key] as $row): ?>
                        <li><?= e(implode(' | ', array_filter($row, static fn($value, $column) => !in_array($column, ['id', 'expert_id'], true) && $value !== '', ARRAY_FILTER_USE_BOTH))); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>
