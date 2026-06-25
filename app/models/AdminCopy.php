<?php

declare(strict_types=1);

class AdminCopy
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $sql = "
            SELECT
                e.id_egzemplarz,
                e.id_wydanie,
                e.forma,
                e.id_status_egzemplarz,
                e.cena_zakup,
                e.uwaga,
                k.tytul,
                w.isbn,
                w.rok_wydania,
                wyd.nazwa AS wydawnictwo,
                s.nazwa AS status
            FROM egzemplarz e
            INNER JOIN wydanie w ON w.id_wydanie = e.id_wydanie
            INNER JOIN ksiazka k ON k.id_ksiazka = w.id_ksiazka
            INNER JOIN wydawnictwo wyd ON wyd.id_wydawnictwo = w.id_wydawnictwo
            INNER JOIN status_egzemplarz s ON s.id_status_egzemplarz = e.id_status_egzemplarz
            ORDER BY e.id_egzemplarz DESC
        ";

        $statement = $this->pdo->query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $sql = "
            SELECT
                e.id_egzemplarz,
                e.id_wydanie,
                e.forma,
                e.id_status_egzemplarz,
                e.cena_zakup,
                e.uwaga
            FROM egzemplarz e
            WHERE e.id_egzemplarz = :id_egzemplarz
        ";

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'id_egzemplarz' => $id,
        ]);

        $copy = $statement->fetch(PDO::FETCH_ASSOC);

        return $copy ?: null;
    }

    public function editionsForSelect(): array
    {
        $sql = "
            SELECT
                w.id_wydanie,
                k.tytul,
                wyd.nazwa AS wydawnictwo,
                w.rok_wydania,
                w.isbn
            FROM wydanie w
            INNER JOIN ksiazka k ON k.id_ksiazka = w.id_ksiazka
            INNER JOIN wydawnictwo wyd ON wyd.id_wydawnictwo = w.id_wydawnictwo
            ORDER BY k.tytul ASC, w.rok_wydania DESC
        ";

        $statement = $this->pdo->query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function statusesForSelect(): array
    {
        $sql = "
            SELECT
                id_status_egzemplarz,
                nazwa
            FROM status_egzemplarz
            ORDER BY id_status_egzemplarz ASC
        ";

        $statement = $this->pdo->query($sql);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): void
    {
        $sql = "
            INSERT INTO egzemplarz (
                id_wydanie,
                forma,
                id_status_egzemplarz,
                cena_zakup,
                uwaga
            ) VALUES (
                :id_wydanie,
                :forma,
                :id_status_egzemplarz,
                :cena_zakup,
                :uwaga
            )
        ";

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'id_wydanie' => $data['id_wydanie'],
            'forma' => $data['forma'],
            'id_status_egzemplarz' => $data['id_status_egzemplarz'],
            'cena_zakup' => $data['cena_zakup'],
            'uwaga' => $data['uwaga'],
        ]);
    }

    public function update(int $id, array $data): void
    {
        $sql = "
            UPDATE egzemplarz
            SET
                id_wydanie = :id_wydanie,
                forma = :forma,
                id_status_egzemplarz = :id_status_egzemplarz,
                cena_zakup = :cena_zakup,
                uwaga = :uwaga
            WHERE id_egzemplarz = :id_egzemplarz
        ";

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'id_wydanie' => $data['id_wydanie'],
            'forma' => $data['forma'],
            'id_status_egzemplarz' => $data['id_status_egzemplarz'],
            'cena_zakup' => $data['cena_zakup'],
            'uwaga' => $data['uwaga'],
            'id_egzemplarz' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $sql = "
            DELETE FROM egzemplarz
            WHERE id_egzemplarz = :id_egzemplarz
        ";

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'id_egzemplarz' => $id,
        ]);
    }
}