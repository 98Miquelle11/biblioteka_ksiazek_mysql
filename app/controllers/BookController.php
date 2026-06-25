<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Book.php';

class BookController
{
    public static function index(PDO $pdo): void
    {
        $page = (int) ($_GET['page'] ?? 1);

        if ($page < 1) {
            $page = 1;
        }

        $limit = 10;
        $offset = ($page - 1) * $limit;

        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'gatunek' => trim($_GET['gatunek'] ?? ''),
            'forma' => trim($_GET['forma'] ?? ''),
            'rok' => trim($_GET['rok'] ?? ''),
        ];

        $genres = Book::getGenres($pdo);
        $years = Book::getYears($pdo);

        $totalBooks = Book::countAll($pdo, $filters);
        $totalPages = (int) ceil($totalBooks / $limit);

        $books = Book::getPaginated($pdo, $filters, $limit, $offset);

        require __DIR__ . '/../views/books/index.php';
    }
}