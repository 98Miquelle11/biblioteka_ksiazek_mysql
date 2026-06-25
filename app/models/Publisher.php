<?php

declare(strict_types=1);

class Publisher
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id_wydawnictwo, nazwa, siedziba
             FROM wydawnictwo
             ORDER BY nazwa ASC'
        );

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_wydawnictwo, nazwa, siedziba
             FROM wydawnictwo
             WHERE id_wydawnictwo = :id_wydawnictwo'
        );

        $stmt->execute([
            'id_wydawnictwo' => $id,
        ]);

        $publisher = $stmt->fetch();

        return $publisher ?: null;
    }

    public function create(string $nazwa, ?string $siedziba): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO wydawnictwo (nazwa, siedziba)
             VALUES (:nazwa, :siedziba)'
        );

        return $stmt->execute([
            'nazwa' => $nazwa,
            'siedziba' => $siedziba,
        ]);
    }

    public function update(int $id, string $nazwa, ?string $siedziba): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE wydawnictwo
             SET nazwa = :nazwa,
                 siedziba = :siedziba
             WHERE id_wydawnictwo = :id_wydawnictwo'
        );

        return $stmt->execute([
            'id_wydawnictwo' => $id,
            'nazwa' => $nazwa,
            'siedziba' => $siedziba,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM wydawnictwo
             WHERE id_wydawnictwo = :id_wydawnictwo'
        );

        return $stmt->execute([
            'id_wydawnictwo' => $id,
        ]);
    }
}