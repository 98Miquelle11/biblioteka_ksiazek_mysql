<?php

declare(strict_types=1);

class Book
{
    public static function getGenres(PDO $pdo): array
    {
        $stmt = $pdo->query("
            SELECT id_gatunek, nazwa
            FROM gatunek
            ORDER BY nazwa ASC
        ");

        return $stmt->fetchAll();
    }

    public static function getYears(PDO $pdo): array
    {
        $stmt = $pdo->query("
            SELECT DISTINCT rok_wydania
            FROM wydanie
            WHERE rok_wydania IS NOT NULL
            ORDER BY rok_wydania DESC
        ");

        return $stmt->fetchAll();
    }

    public static function countAll(PDO $pdo, array $filters): int
    {
        $sql = "
            SELECT COUNT(DISTINCT egzemplarz.id_egzemplarz) AS total
            FROM egzemplarz
            INNER JOIN wydanie 
                ON egzemplarz.id_wydanie = wydanie.id_wydanie
            INNER JOIN ksiazka 
                ON wydanie.id_ksiazka = ksiazka.id_ksiazka
            INNER JOIN wydawnictwo 
                ON wydanie.id_wydawnictwo = wydawnictwo.id_wydawnictwo
            INNER JOIN status_egzemplarz 
                ON egzemplarz.id_status_egzemplarz = status_egzemplarz.id_status_egzemplarz
            LEFT JOIN autor_ksiazka 
                ON ksiazka.id_ksiazka = autor_ksiazka.id_ksiazka
            LEFT JOIN autor 
                ON autor_ksiazka.id_autor = autor.id_autor
            LEFT JOIN gatunek_ksiazka 
                ON ksiazka.id_ksiazka = gatunek_ksiazka.id_ksiazka
            LEFT JOIN gatunek 
                ON gatunek_ksiazka.id_gatunek = gatunek.id_gatunek
            WHERE 1 = 1
        ";

        $params = [];

        self::applyFilters($sql, $params, $filters);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch();

        return (int) ($result['total'] ?? 0);
    }

    public static function getPaginated(PDO $pdo, array $filters, int $limit, int $offset): array
    {
        $sql = "
            SELECT
                egzemplarz.id_egzemplarz,
                ksiazka.id_ksiazka,
                ksiazka.tytul,
                GROUP_CONCAT(DISTINCT CONCAT(autor.imie, ' ', autor.nazwisko) ORDER BY autor.nazwisko SEPARATOR ', ') AS autorzy,
                GROUP_CONCAT(DISTINCT gatunek.nazwa ORDER BY gatunek.nazwa SEPARATOR ', ') AS gatunki,
                wydawnictwo.nazwa AS wydawnictwo,
                wydanie.rok_wydania,
                egzemplarz.forma,
                status_egzemplarz.nazwa AS status_egzemplarza
            FROM egzemplarz
            INNER JOIN wydanie 
                ON egzemplarz.id_wydanie = wydanie.id_wydanie
            INNER JOIN ksiazka 
                ON wydanie.id_ksiazka = ksiazka.id_ksiazka
            INNER JOIN wydawnictwo 
                ON wydanie.id_wydawnictwo = wydawnictwo.id_wydawnictwo
            INNER JOIN status_egzemplarz 
                ON egzemplarz.id_status_egzemplarz = status_egzemplarz.id_status_egzemplarz
            LEFT JOIN autor_ksiazka 
                ON ksiazka.id_ksiazka = autor_ksiazka.id_ksiazka
            LEFT JOIN autor 
                ON autor_ksiazka.id_autor = autor.id_autor
            LEFT JOIN gatunek_ksiazka 
                ON ksiazka.id_ksiazka = gatunek_ksiazka.id_ksiazka
            LEFT JOIN gatunek 
                ON gatunek_ksiazka.id_gatunek = gatunek.id_gatunek
            WHERE 1 = 1
        ";

        $params = [];

        self::applyFilters($sql, $params, $filters);

        $sql .= "
            GROUP BY
                egzemplarz.id_egzemplarz,
                ksiazka.id_ksiazka,
                ksiazka.tytul,
                wydawnictwo.nazwa,
                wydanie.rok_wydania,
                egzemplarz.forma,
                status_egzemplarz.nazwa
            ORDER BY ksiazka.tytul ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            if (is_int($value)) {
                $stmt->bindValue(':' . $key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
            }
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    private static function applyFilters(string &$sql, array &$params, array $filters): void
    {
        if (!empty($filters['q'])) {
            $sql .= "
                AND (
                    ksiazka.tytul LIKE :q_title
                    OR autor.imie LIKE :q_author_first_name
                    OR autor.nazwisko LIKE :q_author_last_name
                    OR CONCAT(autor.imie, ' ', autor.nazwisko) LIKE :q_author_full_name
                )
            ";

            $searchValue = '%' . $filters['q'] . '%';

            $params['q_title'] = $searchValue;
            $params['q_author_first_name'] = $searchValue;
            $params['q_author_last_name'] = $searchValue;
            $params['q_author_full_name'] = $searchValue;
        }

        if (!empty($filters['gatunek'])) {
            $sql .= " AND gatunek.id_gatunek = :gatunek";
            $params['gatunek'] = (int) $filters['gatunek'];
        }

        if (!empty($filters['forma'])) {
            $sql .= " AND egzemplarz.forma = :forma";
            $params['forma'] = $filters['forma'];
        }

        if (!empty($filters['rok'])) {
            $sql .= " AND wydanie.rok_wydania = :rok";
            $params['rok'] = (int) $filters['rok'];
        }
    }
}