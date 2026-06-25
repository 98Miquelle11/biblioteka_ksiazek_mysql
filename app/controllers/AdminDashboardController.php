<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/AdminDashboard.php';

class AdminDashboardController
{
    private AdminDashboard $dashboardModel;

    public function __construct(PDO $pdo)
    {
        $this->dashboardModel = new AdminDashboard($pdo);
    }

    public function index(): void
    {
        requireAdmin();

        $stats = $this->dashboardModel->getStats();

        require __DIR__ . '/../views/admin/dashboard.php';
    }
}