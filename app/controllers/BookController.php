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

        if ($totalPages > 0 && $page > $totalPages) {
            $page = $totalPages;
        }

        $offset = ($page - 1) * $limit;

        $books = Book::getPaginated($pdo, $filters, $limit, $offset);

        require __DIR__ . '/../views/books/index.php';
    }

    public static function show(PDO $pdo): void
    {
        $bookId = (int) ($_GET['id'] ?? 0);

        if ($bookId <= 0) {
            http_response_code(404);
            renderHeader('404');
            echo '<h2>404 - Nie znaleziono książki</h2>';
            echo '<p><a href="' . e(url('/books')) . '">Wróć do katalogu</a></p>';
            renderFooter();
            return;
        }

        $book = Book::findById($pdo, $bookId);

        if (!$book) {
            http_response_code(404);
            renderHeader('404');
            echo '<h2>404 - Nie znaleziono książki</h2>';
            echo '<p><a href="' . e(url('/books')) . '">Wróć do katalogu</a></p>';
            renderFooter();
            return;
        }

        $copies = Book::getCopiesByBookId($pdo, $bookId);

        require __DIR__ . '/../views/books/show.php';
    }
}