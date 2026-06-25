<?php

declare(strict_types=1);

class Loan
{
    public static function getAll(PDO $pdo): array
    {
        $stmt = $pdo->query("
            SELECT
                wypozyczenie.id_wypozyczenie,
                wypozyczenie.id_egzemplarz,
                wypozyczenie.id_czytelnik,
                wypozyczenie.data_wypozyczenie,
                wypozyczenie.przewidywana_data_zwrot,
                wypozyczenie.rzeczywista_data_zwrot,
                ksiazka.tytul,
                egzemplarz.forma,
                status_egzemplarz.nazwa AS status_egzemplarza,
                czytelnik.imie,
                czytelnik.nazwisko,
                czytelnik.email
            FROM wypozyczenie
            INNER JOIN egzemplarz
                ON wypozyczenie.id_egzemplarz = egzemplarz.id_egzemplarz
            INNER JOIN status_egzemplarz
                ON egzemplarz.id_status_egzemplarz = status_egzemplarz.id_status_egzemplarz
            INNER JOIN wydanie
                ON egzemplarz.id_wydanie = wydanie.id_wydanie
            INNER JOIN ksiazka
                ON wydanie.id_ksiazka = ksiazka.id_ksiazka
            INNER JOIN czytelnik
                ON wypozyczenie.id_czytelnik = czytelnik.id_czytelnik
            ORDER BY wypozyczenie.data_wypozyczenie DESC, wypozyczenie.id_wypozyczenie DESC
        ");

        return $stmt->fetchAll();
    }

    public static function getAvailableCopies(PDO $pdo): array
    {
        $stmt = $pdo->query("
            SELECT
                egzemplarz.id_egzemplarz,
                ksiazka.tytul,
                egzemplarz.forma,
                wydanie.rok_wydania,
                wydawnictwo.nazwa AS wydawnictwo,
                status_egzemplarz.nazwa AS status_egzemplarza
            FROM egzemplarz
            INNER JOIN status_egzemplarz
                ON egzemplarz.id_status_egzemplarz = status_egzemplarz.id_status_egzemplarz
            INNER JOIN wydanie
                ON egzemplarz.id_wydanie = wydanie.id_wydanie
            INNER JOIN ksiazka
                ON wydanie.id_ksiazka = ksiazka.id_ksiazka
            INNER JOIN wydawnictwo
                ON wydanie.id_wydawnictwo = wydawnictwo.id_wydawnictwo
            WHERE status_egzemplarz.nazwa = 'dostepny'
            ORDER BY ksiazka.tytul ASC, egzemplarz.id_egzemplarz ASC
        ");

        return $stmt->fetchAll();
    }

    public static function getReaders(PDO $pdo): array
    {
        $stmt = $pdo->query("
            SELECT
                id_czytelnik,
                imie,
                nazwisko,
                email,
                rola
            FROM czytelnik
            ORDER BY nazwisko ASC, imie ASC, email ASC
        ");

        return $stmt->fetchAll();
    }

    public static function create(PDO $pdo, int $copyId, int $readerId, string $loanDate, string $expectedReturnDate): bool
    {
        $stmt = $pdo->prepare("
            INSERT INTO wypozyczenie (
                id_egzemplarz,
                id_czytelnik,
                data_wypozyczenie,
                przewidywana_data_zwrot,
                rzeczywista_data_zwrot
            ) VALUES (
                :id_egzemplarz,
                :id_czytelnik,
                :data_wypozyczenie,
                :przewidywana_data_zwrot,
                NULL
            )
        ");

        return $stmt->execute([
            'id_egzemplarz' => $copyId,
            'id_czytelnik' => $readerId,
            'data_wypozyczenie' => $loanDate,
            'przewidywana_data_zwrot' => $expectedReturnDate,
        ]);
    }

    public static function findActiveById(PDO $pdo, int $loanId): ?array
    {
        $stmt = $pdo->prepare("
            SELECT *
            FROM wypozyczenie
            WHERE id_wypozyczenie = :id_wypozyczenie
              AND rzeczywista_data_zwrot IS NULL
            LIMIT 1
        ");

        $stmt->execute([
            'id_wypozyczenie' => $loanId,
        ]);

        $loan = $stmt->fetch();

        return $loan ?: null;
    }

    public static function markReturned(PDO $pdo, int $loanId): bool
    {
        $stmt = $pdo->prepare("
            UPDATE wypozyczenie
            SET rzeczywista_data_zwrot = CURDATE()
            WHERE id_wypozyczenie = :id_wypozyczenie
              AND rzeczywista_data_zwrot IS NULL
        ");

        return $stmt->execute([
            'id_wypozyczenie' => $loanId,
        ]);
    }
}