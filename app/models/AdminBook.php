<?php

declare(strict_types=1);

class AdminBook
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT
                k.id_ksiazka,
                k.tytul,
                GROUP_CONCAT(DISTINCT CONCAT(a.imie, " ", a.nazwisko) SEPARATOR ", ") AS autorzy,
                GROUP_CONCAT(DISTINCT g.nazwa SEPARATOR ", ") AS gatunki,
                w.nazwa AS wydawnictwo,
                wd.isbn,
                wd.rok_wydania,
                wd.liczba_stron
             FROM ksiazka k
             LEFT JOIN autor_ksiazka ak ON ak.id_ksiazka = k.id_ksiazka
             LEFT JOIN autor a ON a.id_autor = ak.id_autor
             LEFT JOIN gatunek_ksiazka gk ON gk.id_ksiazka = k.id_ksiazka
             LEFT JOIN gatunek g ON g.id_gatunek = gk.id_gatunek
             LEFT JOIN wydanie wd ON wd.id_wydanie = (
                SELECT MIN(wd2.id_wydanie)
                FROM wydanie wd2
                WHERE wd2.id_ksiazka = k.id_ksiazka
             )
             LEFT JOIN wydawnictwo w ON w.id_wydawnictwo = wd.id_wydawnictwo
             GROUP BY
                k.id_ksiazka,
                k.tytul,
                w.nazwa,
                wd.isbn,
                wd.rok_wydania,
                wd.liczba_stron
             ORDER BY k.tytul ASC'
        );

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_ksiazka, tytul
             FROM ksiazka
             WHERE id_ksiazka = :id_ksiazka'
        );

        $stmt->execute([
            'id_ksiazka' => $id,
        ]);

        $book = $stmt->fetch();

        if (!$book) {
            return null;
        }

        $book['id_autor'] = $this->findFirstAuthorId($id);
        $book['id_gatunek'] = $this->findFirstGenreId($id);
        $book['wydanie'] = $this->findFirstEdition($id);

        return $book;
    }

    public function authors(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id_autor, imie, nazwisko
             FROM autor
             ORDER BY nazwisko ASC, imie ASC'
        );

        return $stmt->fetchAll();
    }

    public function genres(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id_gatunek, nazwa
             FROM gatunek
             ORDER BY nazwa ASC'
        );

        return $stmt->fetchAll();
    }

    public function publishers(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id_wydawnictwo, nazwa
             FROM wydawnictwo
             ORDER BY nazwa ASC'
        );

        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO ksiazka (tytul)
                 VALUES (:tytul)'
            );

            $stmt->execute([
                'tytul' => $data['tytul'],
            ]);

            $bookId = (int)$this->pdo->lastInsertId();

            $this->insertAuthorRelation($bookId, (int)$data['id_autor']);
            $this->insertGenreRelation($bookId, (int)$data['id_gatunek']);
            $this->insertEdition($bookId, $data);

            $this->pdo->commit();

            return true;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function update(int $id, array $data): bool
    {
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                'UPDATE ksiazka
                 SET tytul = :tytul
                 WHERE id_ksiazka = :id_ksiazka'
            );

            $stmt->execute([
                'id_ksiazka' => $id,
                'tytul' => $data['tytul'],
            ]);

            $stmt = $this->pdo->prepare(
                'DELETE FROM autor_ksiazka
                 WHERE id_ksiazka = :id_ksiazka'
            );

            $stmt->execute([
                'id_ksiazka' => $id,
            ]);

            $stmt = $this->pdo->prepare(
                'DELETE FROM gatunek_ksiazka
                 WHERE id_ksiazka = :id_ksiazka'
            );

            $stmt->execute([
                'id_ksiazka' => $id,
            ]);

            $this->insertAuthorRelation($id, (int)$data['id_autor']);
            $this->insertGenreRelation($id, (int)$data['id_gatunek']);

            $edition = $this->findFirstEdition($id);

            if ($edition) {
                $this->updateEdition((int)$edition['id_wydanie'], $data);
            } else {
                $this->insertEdition($id, $data);
            }

            $this->pdo->commit();

            return true;
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM ksiazka
             WHERE id_ksiazka = :id_ksiazka'
        );

        return $stmt->execute([
            'id_ksiazka' => $id,
        ]);
    }

    private function findFirstAuthorId(int $bookId): ?int
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_autor
             FROM autor_ksiazka
             WHERE id_ksiazka = :id_ksiazka
             LIMIT 1'
        );

        $stmt->execute([
            'id_ksiazka' => $bookId,
        ]);

        $id = $stmt->fetchColumn();

        return $id !== false ? (int)$id : null;
    }

    private function findFirstGenreId(int $bookId): ?int
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_gatunek
             FROM gatunek_ksiazka
             WHERE id_ksiazka = :id_ksiazka
             LIMIT 1'
        );

        $stmt->execute([
            'id_ksiazka' => $bookId,
        ]);

        $id = $stmt->fetchColumn();

        return $id !== false ? (int)$id : null;
    }

    private function findFirstEdition(int $bookId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT
                id_wydanie,
                id_ksiazka,
                id_wydawnictwo,
                isbn,
                rok_wydania,
                liczba_stron
             FROM wydanie
             WHERE id_ksiazka = :id_ksiazka
             ORDER BY id_wydanie ASC
             LIMIT 1'
        );

        $stmt->execute([
            'id_ksiazka' => $bookId,
        ]);

        $edition = $stmt->fetch();

        return $edition ?: null;
    }

    private function insertAuthorRelation(int $bookId, int $authorId): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO autor_ksiazka (id_ksiazka, id_autor)
             VALUES (:id_ksiazka, :id_autor)'
        );

        $stmt->execute([
            'id_ksiazka' => $bookId,
            'id_autor' => $authorId,
        ]);
    }

    private function insertGenreRelation(int $bookId, int $genreId): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO gatunek_ksiazka (id_ksiazka, id_gatunek)
             VALUES (:id_ksiazka, :id_gatunek)'
        );

        $stmt->execute([
            'id_ksiazka' => $bookId,
            'id_gatunek' => $genreId,
        ]);
    }

    private function insertEdition(int $bookId, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO wydanie (
                id_ksiazka,
                id_wydawnictwo,
                isbn,
                rok_wydania,
                liczba_stron
             )
             VALUES (
                :id_ksiazka,
                :id_wydawnictwo,
                :isbn,
                :rok_wydania,
                :liczba_stron
             )'
        );

        $stmt->execute([
            'id_ksiazka' => $bookId,
            'id_wydawnictwo' => $data['id_wydawnictwo'],
            'isbn' => $data['isbn'],
            'rok_wydania' => $data['rok_wydania'],
            'liczba_stron' => $data['liczba_stron'],
        ]);
    }

    private function updateEdition(int $editionId, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE wydanie
             SET id_wydawnictwo = :id_wydawnictwo,
                 isbn = :isbn,
                 rok_wydania = :rok_wydania,
                 liczba_stron = :liczba_stron
             WHERE id_wydanie = :id_wydanie'
        );

        $stmt->execute([
            'id_wydanie' => $editionId,
            'id_wydawnictwo' => $data['id_wydawnictwo'],
            'isbn' => $data['isbn'],
            'rok_wydania' => $data['rok_wydania'],
            'liczba_stron' => $data['liczba_stron'],
        ]);
    }
}