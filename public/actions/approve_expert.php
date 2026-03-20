<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/expert_repository.php';
require_role(['admin']);
verify_csrf();

$id = (int) ($_POST['id'] ?? 0);
if ($id > 0) {
    approve_expert($id);
    flash('success', 'อนุมัติโปรไฟล์เรียบร้อย');
}

redirect('expert_view.php?id=' . $id);
