<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/expert_repository.php';
require_login();

header('Content-Type: application/json; charset=utf-8');
$query = trim((string) ($_GET['q'] ?? ''));
echo json_encode(['data' => $query === '' ? [] : search_skill_suggestions($query)], JSON_UNESCAPED_UNICODE);
