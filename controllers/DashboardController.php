<?php

declare(strict_types=1);

class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(['admin', 'expert', 'viewer']);
        $expertModel = new Expert();
        $stats = $expertModel->stats();
        $experts = $expertModel->paginate([], 1, 4)['data'];

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'experts' => $experts,
        ]);
    }
}
