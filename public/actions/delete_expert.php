<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/expert_repository.php';
require_role(['admin']);
verify_csrf();

$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    delete_expert($id);
    flash('success', 'ลบข้อมูลเรียบร้อย');
}

redirect('experts.php');
