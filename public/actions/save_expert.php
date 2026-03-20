<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/expert_repository.php';
require_role(['admin', 'expert']);
verify_csrf();
remember_old_input($_POST);

$id = (int) ($_POST['id'] ?? 0);
$currentExpert = $id > 0 ? find_expert($id) : null;
if ($currentExpert && !expert_owner_can_edit($currentExpert)) {
    flash('error', 'คุณไม่มีสิทธิ์แก้ไขรายการนี้');
    redirect('experts.php');
}

try {
    $payload = validate_expert_form($_POST, $_FILES, $currentExpert);
    $payload['user_id'] = $currentExpert['user_id'] ?? current_user()['id'];

    if ($currentExpert) {
        update_expert($id, $payload);
        flash('success', 'อัปเดตข้อมูลผู้เชี่ยวชาญเรียบร้อย');
        clear_old_input();
        redirect('expert_view.php?id=' . $id);
    }

    $newId = insert_expert($payload);
    flash('success', 'บันทึกข้อมูลผู้เชี่ยวชาญเรียบร้อย');
    clear_old_input();
    redirect('expert_view.php?id=' . $newId);
} catch (Throwable $exception) {
    flash('error', $exception->getMessage());
    redirect($id > 0 ? 'expert_form.php?id=' . $id : 'expert_form.php');
}
