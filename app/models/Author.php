<?php

declare(strict_types=1);

class Author
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id_autor, imie, nazwisko, narodowosc
             FROM autor
             ORDER BY nazwisko ASC, imie ASC'
        );

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_autor, imie, nazwisko, narodowosc
             FROM autor
             WHERE id_autor = :id_autor'
        );

        $stmt->execute([
            'id_autor' => $id,
        ]);

        $author = $stmt->fetch();

        return $author ?: null;
    }

    public function create(string $imie, string $nazwisko, ?string $narodowosc): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO autor (imie, nazwisko, narodowosc)
             VALUES (:imie, :nazwisko, :narodowosc)'
        );

        return $stmt->execute([
            'imie' => $imie,
            'nazwisko' => $nazwisko,
            'narodowosc' => $narodowosc,
        ]);
    }

    public function update(int $id, string $imie, string $nazwisko, ?string $narodowosc): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE autor
             SET imie = :imie,
                 nazwisko = :nazwisko,
                 narodowosc = :narodowosc
             WHERE id_autor = :id_autor'
        );

        return $stmt->execute([
            'id_autor' => $id,
            'imie' => $imie,
            'nazwisko' => $nazwisko,
            'narodowosc' => $narodowosc,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM autor
             WHERE id_autor = :id_autor'
        );

        return $stmt->execute([
            'id_autor' => $id,
        ]);
    }
}