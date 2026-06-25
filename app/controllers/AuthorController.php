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

            $old['imie'] = trim($_POST['imie'] ?? '');
            $old['nazwisko'] = trim($_POST['nazwisko'] ?? '');
            $old['narodowosc'] = trim($_POST['narodowosc'] ?? '');

            if ($old['imie'] === '') {
                $errors[] = 'Imię autora jest wymagane.';
            }

            if ($old['nazwisko'] === '') {
                $errors[] = 'Nazwisko autora jest wymagane.';
            }

            if (mb_strlen($old['imie']) > 100) {
                $errors[] = 'Imię autora jest za długie.';
            }

            if (mb_strlen($old['nazwisko']) > 100) {
                $errors[] = 'Nazwisko autora jest za długie.';
            }

            if (mb_strlen($old['narodowosc']) > 100) {
                $errors[] = 'Narodowość autora jest za długa.';
            }

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
            'imie' => $author['imie'] ?? '',
            'nazwisko' => $author['nazwisko'] ?? '',
            'narodowosc' => $author['narodowosc'] ?? '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireValidCsrfToken();

            $old['imie'] = trim($_POST['imie'] ?? '');
            $old['nazwisko'] = trim($_POST['nazwisko'] ?? '');
            $old['narodowosc'] = trim($_POST['narodowosc'] ?? '');

            if ($old['imie'] === '') {
                $errors[] = 'Imię autora jest wymagane.';
            }

            if ($old['nazwisko'] === '') {
                $errors[] = 'Nazwisko autora jest wymagane.';
            }

            if (mb_strlen($old['imie']) > 100) {
                $errors[] = 'Imię autora jest za długie.';
            }

            if (mb_strlen($old['nazwisko']) > 100) {
                $errors[] = 'Nazwisko autora jest za długie.';
            }

            if (mb_strlen($old['narodowosc']) > 100) {
                $errors[] = 'Narodowość autora jest za długa.';
            }

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
}