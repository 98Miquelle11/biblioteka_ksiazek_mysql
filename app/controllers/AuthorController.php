<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Author.php';

class AuthorController
{
    private Author $authorModel;

    public function __construct(PDO $pdo)
    {
        $this->authorModel = new Author($pdo);
    }

    public function index(): void
    {
        requireAdmin();

        $authors = $this->authorModel->all();

        require __DIR__ . '/../views/admin/authors/index.php';
    }

    public function create(): void
    {
        requireAdmin();

        $errors = [];
        $old = [
            'imie' => '',
            'nazwisko' => '',
            'narodowosc' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireValidCsrfToken();

            $old = $this->prepareFormData($_POST);
            $errors = $this->validate($old);

            if (empty($errors)) {
                $this->authorModel->create(
                    $old['imie'],
                    $old['nazwisko'],
                    $old['narodowosc'] !== '' ? $old['narodowosc'] : null
                );

                $_SESSION['flash_success'] = 'Autor został dodany.';
                redirect(url('/admin/authors'));
            }
        }

        require __DIR__ . '/../views/admin/authors/create.php';
    }

    public function edit(): void
    {
        requireAdmin();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Nieprawidłowe ID autora.';
            redirect(url('/admin/authors'));
        }

        $author = $this->authorModel->find($id);

        if (!$author) {
            $_SESSION['flash_error'] = 'Nie znaleziono autora.';
            redirect(url('/admin/authors'));
        }

        $errors = [];
        $old = [
            'imie' => (string)($author['imie'] ?? ''),
            'nazwisko' => (string)($author['nazwisko'] ?? ''),
            'narodowosc' => (string)($author['narodowosc'] ?? ''),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireValidCsrfToken();

            $old = $this->prepareFormData($_POST);
            $errors = $this->validate($old);

            if (empty($errors)) {
                $this->authorModel->update(
                    $id,
                    $old['imie'],
                    $old['nazwisko'],
                    $old['narodowosc'] !== '' ? $old['narodowosc'] : null
                );

                $_SESSION['flash_success'] = 'Autor został zaktualizowany.';
                redirect(url('/admin/authors'));
            }
        }

        require __DIR__ . '/../views/admin/authors/edit.php';
    }

    public function delete(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('/admin/authors'));
        }

        requireValidCsrfToken();

        $id = (int)($_POST['id_autor'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Nieprawidłowe ID autora.';
            redirect(url('/admin/authors'));
        }

        try {
            $this->authorModel->delete($id);
            $_SESSION['flash_success'] = 'Autor został usunięty.';
        } catch (PDOException $exception) {
            $_SESSION['flash_error'] = 'Nie można usunąć autora, jeśli jest przypisany do książki.';
        }

        redirect(url('/admin/authors'));
    }

    private function prepareFormData(array $data): array
    {
        return [
            'imie' => trim((string)($data['imie'] ?? '')),
            'nazwisko' => trim((string)($data['nazwisko'] ?? '')),
            'narodowosc' => trim((string)($data['narodowosc'] ?? '')),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['imie'] === '') {
            $errors[] = 'Imię autora jest wymagane.';
        } elseif (mb_strlen($data['imie']) > 100) {
            $errors[] = 'Imię autora może mieć maksymalnie 100 znaków.';
        }

        if ($data['nazwisko'] === '') {
            $errors[] = 'Nazwisko autora jest wymagane.';
        } elseif (mb_strlen($data['nazwisko']) > 100) {
            $errors[] = 'Nazwisko autora może mieć maksymalnie 100 znaków.';
        }

        if (mb_strlen($data['narodowosc']) > 100) {
            $errors[] = 'Narodowość autora może mieć maksymalnie 100 znaków.';
        }

        return $errors;
    }
}