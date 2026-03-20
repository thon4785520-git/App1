<?php

declare(strict_types=1);

class ApiController extends Controller
{
    public function searchSkills(): void
    {
        Auth::requireRole(['admin', 'expert', 'viewer']);
        $query = trim((string) ($_GET['q'] ?? ''));
        $expertModel = new Expert();
        $this->json(['data' => $query === '' ? [] : $expertModel->searchSkills($query)]);
    }
}
