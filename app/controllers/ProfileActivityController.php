<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/ProfileActivity.php';

class ProfileActivityController
{
    private ProfileActivity $profileActivityModel;

    public function __construct(PDO $pdo)
    {
        $this->profileActivityModel = new ProfileActivity($pdo);
    }

    public function loans(): void
    {
        requireLogin();

        $userId = currentUserId();
        $loans = $this->profileActivityModel->loansForUser($userId);

        require __DIR__ . '/../views/profile/loans.php';
    }

    public function reservations(): void
    {
        requireLogin();

        $userId = currentUserId();
        $reservations = $this->profileActivityModel->reservationsForUser($userId);

        require __DIR__ . '/../views/profile/reservations.php';
    }
}