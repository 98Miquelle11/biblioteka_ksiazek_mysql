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
            $errors = $this->validate($old, $authors, $genres, $publishers);

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
            'tytul' => (string)($book['tytul'] ?? ''),
            'id_autor' => (string)($book['id_autor'] ?? ''),
            'id_gatunek' => (string)($book['id_gatunek'] ?? ''),
            'id_wydawnictwo' => (string)($book['wydanie']['id_wydawnictwo'] ?? ''),
            'isbn' => (string)($book['wydanie']['isbn'] ?? ''),
            'rok_wydania' => (string)($book['wydanie']['rok_wydania'] ?? ''),
            'liczba_stron' => (string)($book['wydanie']['liczba_stron'] ?? ''),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireValidCsrfToken();

            $old = $this->readFormData();
            $errors = $this->validate($old, $authors, $genres, $publishers);

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
            'tytul' => trim((string)($_POST['tytul'] ?? '')),
            'id_autor' => trim((string)($_POST['id_autor'] ?? '')),
            'id_gatunek' => trim((string)($_POST['id_gatunek'] ?? '')),
            'id_wydawnictwo' => trim((string)($_POST['id_wydawnictwo'] ?? '')),
            'isbn' => trim((string)($_POST['isbn'] ?? '')),
            'rok_wydania' => trim((string)($_POST['rok_wydania'] ?? '')),
            'liczba_stron' => trim((string)($_POST['liczba_stron'] ?? '')),
        ];
    }

    private function validate(array $data, array $authors, array $genres, array $publishers): array
    {
        $errors = [];

        if ($data['tytul'] === '') {
            $errors[] = 'Tytuł książki jest wymagany.';
        } elseif (mb_strlen($data['tytul']) > 255) {
            $errors[] = 'Tytuł książki może mieć maksymalnie 255 znaków.';
        }

        if ($data['id_autor'] === '') {
            $errors[] = 'Autor jest wymagany.';
        } elseif (!$this->isPositiveInteger($data['id_autor'])) {
            $errors[] = 'Wybrany autor jest nieprawidłowy.';
        } elseif (!$this->idExistsInList((int)$data['id_autor'], $authors, 'id_autor')) {
            $errors[] = 'Wybrany autor nie istnieje.';
        }

        if ($data['id_gatunek'] === '') {
            $errors[] = 'Gatunek jest wymagany.';
        } elseif (!$this->isPositiveInteger($data['id_gatunek'])) {
            $errors[] = 'Wybrany gatunek jest nieprawidłowy.';
        } elseif (!$this->idExistsInList((int)$data['id_gatunek'], $genres, 'id_gatunek')) {
            $errors[] = 'Wybrany gatunek nie istnieje.';
        }

        if ($data['id_wydawnictwo'] !== '') {
            if (!$this->isPositiveInteger($data['id_wydawnictwo'])) {
                $errors[] = 'Wybrane wydawnictwo jest nieprawidłowe.';
            } elseif (!$this->idExistsInList((int)$data['id_wydawnictwo'], $publishers, 'id_wydawnictwo')) {
                $errors[] = 'Wybrane wydawnictwo nie istnieje.';
            }
        }

        if ($data['isbn'] !== '') {
            if (mb_strlen($data['isbn']) > 13) {
                $errors[] = 'ISBN może mieć maksymalnie 13 znaków.';
            }

            if (!preg_match('/^[0-9Xx\-]+$/', $data['isbn'])) {
                $errors[] = 'ISBN może zawierać tylko cyfry, znak X oraz myślniki.';
            }
        }

        if ($data['rok_wydania'] !== '') {
            if (!$this->isPositiveInteger($data['rok_wydania'])) {
                $errors[] = 'Rok wydania musi być liczbą całkowitą.';
            } else {
                $year = (int)$data['rok_wydania'];

                if ($year < 1000 || $year > 2100) {
                    $errors[] = 'Rok wydania musi być z zakresu 1000-2100.';
                }
            }
        }

        if ($data['liczba_stron'] !== '') {
            if (!$this->isPositiveInteger($data['liczba_stron'])) {
                $errors[] = 'Liczba stron musi być liczbą całkowitą większą od 0.';
            } else {
                $pages = (int)$data['liczba_stron'];

                if ($pages > 10000) {
                    $errors[] = 'Liczba stron nie może być większa niż 10000.';
                }
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

    private function isPositiveInteger(string $value): bool
    {
        return ctype_digit($value) && (int)$value > 0;
    }

    private function idExistsInList(int $id, array $items, string $idField): bool
    {
        foreach ($items as $item) {
            if ((int)($item[$idField] ?? 0) === $id) {
                return true;
            }
        }

        return false;
    }
}