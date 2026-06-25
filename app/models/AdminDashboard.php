<?php

declare(strict_types=1);

class AdminDashboard
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getStats(): array
    {
        return [
            'users_count' => (int)$this->getSingleValue('SELECT COUNT(*) FROM czytelnik'),
            'books_count' => (int)$this->getSingleValue('SELECT COUNT(*) FROM ksiazka'),
            'copies_count' => (int)$this->getSingleValue('SELECT COUNT(*) FROM egzemplarz'),
            'active_reservations_count' => (int)$this->getSingleValue(
                'SELECT COUNT(*) FROM rezerwacja WHERE status_rezerwacja = :status',
                ['status' => 'aktywna']
            ),
            'loans_count' => (int)$this->getSingleValue('SELECT COUNT(*) FROM wypozyczenie'),
            'payments_sum' => (float)$this->getSingleValue('SELECT COALESCE(SUM(kwota), 0) FROM wplata'),
        ];
    }

    private function getSingleValue(string $sql, array $params = []): mixed
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchColumn();
    }
}