<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/AdminBook.php';

class AdminBookController
{
    private AdminBook $bookModel;

    public function __construct(PDO $pdo)
    {
        $this->bookModel = new AdminBook($pdo);
    }

    public function index(): void
    {
        requireAdmin();

        $books = $this->bookModel->all();

        require __DIR__ . '/../views/admin/books/index.php';
    }

    public function create(): void
    {
        requireAdmin();

        $authors = $this->bookModel->authors();
        $genres = $this->bookModel->genres();
        $publishers = $this->bookModel->publishers();

        $errors = [];
        $old = $this->emptyOldData();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireValidCsrfToken();

            $old = $this->readFormData();
            $errors = $this->validate($old);

            if (empty($errors)) {
                try {
                    $this->bookModel->create($this->normalizeData($old));

                    $_SESSION['flash_success'] = 'Książka została dodana.';
                    redirect(url('/admin/books'));
                } catch (PDOException $exception) {
                    $errors[] = 'Nie udało się dodać książki. Sprawdź, czy ISBN nie jest już użyty.';
                }
            }
        }

        require __DIR__ . '/../views/admin/books/create.php';
    }

    public function edit(): void
    {
        requireAdmin();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Nieprawidłowe ID książki.';
            redirect(url('/admin/books'));
        }

        $book = $this->bookModel->find($id);

        if (!$book) {
            $_SESSION['flash_error'] = 'Nie znaleziono książki.';
            redirect(url('/admin/books'));
        }

        $authors = $this->bookModel->authors();
        $genres = $this->bookModel->genres();
        $publishers = $this->bookModel->publishers();

        $errors = [];
        $old = [
            'tytul' => $book['tytul'] ?? '',
            'id_autor' => (string)($book['id_autor'] ?? ''),
            'id_gatunek' => (string)($book['id_gatunek'] ?? ''),
            'id_wydawnictwo' => (string)($book['wydanie']['id_wydawnictwo'] ?? ''),
            'isbn' => $book['wydanie']['isbn'] ?? '',
            'rok_wydania' => (string)($book['wydanie']['rok_wydania'] ?? ''),
            'liczba_stron' => (string)($book['wydanie']['liczba_stron'] ?? ''),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireValidCsrfToken();

            $old = $this->readFormData();
            $errors = $this->validate($old);

            if (empty($errors)) {
                try {
                    $this->bookModel->update($id, $this->normalizeData($old));

                    $_SESSION['flash_success'] = 'Książka została zaktualizowana.';
                    redirect(url('/admin/books'));
                } catch (PDOException $exception) {
                    $errors[] = 'Nie udało się zaktualizować książki. Sprawdź, czy ISBN nie jest już użyty.';
                }
            }
        }

        require __DIR__ . '/../views/admin/books/edit.php';
    }

    public function delete(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('/admin/books'));
        }

        requireValidCsrfToken();

        $id = (int)($_POST['id_ksiazka'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Nieprawidłowe ID książki.';
            redirect(url('/admin/books'));
        }

        try {
            $this->bookModel->delete($id);
            $_SESSION['flash_success'] = 'Książka została usunięta.';
        } catch (PDOException $exception) {
            $_SESSION['flash_error'] = 'Nie można usunąć książki.';
        }

        redirect(url('/admin/books'));
    }

    private function emptyOldData(): array
    {
        return [
            'tytul' => '',
            'id_autor' => '',
            'id_gatunek' => '',
            'id_wydawnictwo' => '',
            'isbn' => '',
            'rok_wydania' => '',
            'liczba_stron' => '',
        ];
    }

    private function readFormData(): array
    {
        return [
            'tytul' => trim($_POST['tytul'] ?? ''),
            'id_autor' => trim($_POST['id_autor'] ?? ''),
            'id_gatunek' => trim($_POST['id_gatunek'] ?? ''),
            'id_wydawnictwo' => trim($_POST['id_wydawnictwo'] ?? ''),
            'isbn' => trim($_POST['isbn'] ?? ''),
            'rok_wydania' => trim($_POST['rok_wydania'] ?? ''),
            'liczba_stron' => trim($_POST['liczba_stron'] ?? ''),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['tytul'] === '') {
            $errors[] = 'Tytuł książki jest wymagany.';
        }

        if (mb_strlen($data['tytul']) > 255) {
            $errors[] = 'Tytuł książki jest za długi.';
        }

        if ($data['id_autor'] === '' || (int)$data['id_autor'] <= 0) {
            $errors[] = 'Autor jest wymagany.';
        }

        if ($data['id_gatunek'] === '' || (int)$data['id_gatunek'] <= 0) {
            $errors[] = 'Gatunek jest wymagany.';
        }

        if ($data['id_wydawnictwo'] !== '' && (int)$data['id_wydawnictwo'] <= 0) {
            $errors[] = 'Wybrane wydawnictwo jest nieprawidłowe.';
        }

        if ($data['isbn'] !== '' && mb_strlen($data['isbn']) > 13) {
            $errors[] = 'ISBN może mieć maksymalnie 13 znaków.';
        }

        if ($data['rok_wydania'] !== '') {
            $year = (int)$data['rok_wydania'];

            if ($year < 1000 || $year > 2100) {
                $errors[] = 'Rok wydania musi być z zakresu 1000-2100.';
            }
        }

        if ($data['liczba_stron'] !== '') {
            $pages = (int)$data['liczba_stron'];

            if ($pages <= 0) {
                $errors[] = 'Liczba stron musi być większa od 0.';
            }
        }

        return $errors;
    }

    private function normalizeData(array $data): array
    {
        return [
            'tytul' => $data['tytul'],
            'id_autor' => (int)$data['id_autor'],
            'id_gatunek' => (int)$data['id_gatunek'],
            'id_wydawnictwo' => $data['id_wydawnictwo'] !== '' ? (int)$data['id_wydawnictwo'] : null,
            'isbn' => $data['isbn'] !== '' ? $data['isbn'] : null,
            'rok_wydania' => $data['rok_wydania'] !== '' ? (int)$data['rok_wydania'] : null,
            'liczba_stron' => $data['liczba_stron'] !== '' ? (int)$data['liczba_stron'] : null,
        ];
    }
}