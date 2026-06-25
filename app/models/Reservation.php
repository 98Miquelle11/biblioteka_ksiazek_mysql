<?php

declare(strict_types=1);

class Reservation
{
    public static function findCopyForReservation(PDO $pdo, int $copyId): ?array
    {
        $stmt = $pdo->prepare("
            SELECT
                egzemplarz.id_egzemplarz,
                wydanie.id_ksiazka,
                status_egzemplarz.nazwa AS status_egzemplarza
            FROM egzemplarz
            INNER JOIN wydanie
                ON egzemplarz.id_wydanie = wydanie.id_wydanie
            INNER JOIN status_egzemplarz
                ON egzemplarz.id_status_egzemplarz = status_egzemplarz.id_status_egzemplarz
            WHERE egzemplarz.id_egzemplarz = :id_egzemplarz
            LIMIT 1
        ");

        $stmt->execute([
            'id_egzemplarz' => $copyId,
        ]);

        $copy = $stmt->fetch();

        return $copy ?: null;
    }

    public static function create(PDO $pdo, int $copyId, int $userId): bool
    {
        $stmt = $pdo->prepare("
            INSERT INTO rezerwacja (
                id_egzemplarz,
                id_czytelnik,
                data_rezerwacja,
                status_rezerwacja
            ) VALUES (
                :id_egzemplarz,
                :id_czytelnik,
                NOW(),
                'aktywna'
            )
        ");

        return $stmt->execute([
            'id_egzemplarz' => $copyId,
            'id_czytelnik' => $userId,
        ]);
    }
}