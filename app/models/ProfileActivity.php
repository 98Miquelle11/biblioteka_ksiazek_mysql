<?php

declare(strict_types=1);

class ProfileActivity
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function loansForUser(int $userId): array
    {
        $sql = "
            SELECT
                wyp.*,
                e.id_egzemplarz,
                e.forma,
                k.tytul,
                wyd.nazwa AS wydawnictwo,
                w.rok_wydania,
                w.isbn
            FROM wypozyczenie wyp
            INNER JOIN egzemplarz e ON e.id_egzemplarz = wyp.id_egzemplarz
            INNER JOIN wydanie w ON w.id_wydanie = e.id_wydanie
            INNER JOIN ksiazka k ON k.id_ksiazka = w.id_ksiazka
            INNER JOIN wydawnictwo wyd ON wyd.id_wydawnictwo = w.id_wydawnictwo
            WHERE wyp.id_czytelnik = :id_czytelnik
            ORDER BY wyp.id_wypozyczenie DESC
        ";

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'id_czytelnik' => $userId,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function reservationsForUser(int $userId): array
    {
        $sql = "
            SELECT
                r.*,
                e.id_egzemplarz,
                e.forma,
                k.tytul,
                wyd.nazwa AS wydawnictwo,
                w.rok_wydania,
                w.isbn
            FROM rezerwacja r
            INNER JOIN egzemplarz e ON e.id_egzemplarz = r.id_egzemplarz
            INNER JOIN wydanie w ON w.id_wydanie = e.id_wydanie
            INNER JOIN ksiazka k ON k.id_ksiazka = w.id_ksiazka
            INNER JOIN wydawnictwo wyd ON wyd.id_wydawnictwo = w.id_wydawnictwo
            WHERE r.id_czytelnik = :id_czytelnik
            ORDER BY r.id_rezerwacja DESC
        ";

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'id_czytelnik' => $userId,
        ]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}