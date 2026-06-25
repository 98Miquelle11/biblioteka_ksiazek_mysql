<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Publisher.php';

class PublisherController
{
    private Publisher $publisherModel;

    public function __construct(PDO $pdo)
    {
        $this->publisherModel = new Publisher($pdo);
    }

    public function index(): void
    {
        requireAdmin();

        $publishers = $this->publisherModel->all();

        require __DIR__ . '/../views/admin/publishers/index.php';
    }

    public function create(): void
    {
        requireAdmin();

        $errors = [];
        $old = [
            'nazwa' => '',
            'siedziba' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireValidCsrfToken();

            $old['nazwa'] = trim($_POST['nazwa'] ?? '');
            $old['siedziba'] = trim($_POST['siedziba'] ?? '');

            if ($old['nazwa'] === '') {
                $errors[] = 'Nazwa wydawnictwa jest wymagana.';
            }

            if (mb_strlen($old['nazwa']) > 150) {
                $errors[] = 'Nazwa wydawnictwa jest za długa.';
            }

            if (mb_strlen($old['siedziba']) > 150) {
                $errors[] = 'Siedziba wydawnictwa jest za długa.';
            }

            if (empty($errors)) {
                $this->publisherModel->create(
                    $old['nazwa'],
                    $old['siedziba'] !== '' ? $old['siedziba'] : null
                );

                $_SESSION['flash_success'] = 'Wydawnictwo zostało dodane.';
                redirect(url('/admin/publishers'));
            }
        }

        require __DIR__ . '/../views/admin/publishers/create.php';
    }

    public function edit(): void
    {
        requireAdmin();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Nieprawidłowe ID wydawnictwa.';
            redirect(url('/admin/publishers'));
        }

        $publisher = $this->publisherModel->find($id);

        if (!$publisher) {
            $_SESSION['flash_error'] = 'Nie znaleziono wydawnictwa.';
            redirect(url('/admin/publishers'));
        }

        $errors = [];
        $old = [
            'nazwa' => $publisher['nazwa'] ?? '',
            'siedziba' => $publisher['siedziba'] ?? '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireValidCsrfToken();

            $old['nazwa'] = trim($_POST['nazwa'] ?? '');
            $old['siedziba'] = trim($_POST['siedziba'] ?? '');

            if ($old['nazwa'] === '') {
                $errors[] = 'Nazwa wydawnictwa jest wymagana.';
            }

            if (mb_strlen($old['nazwa']) > 150) {
                $errors[] = 'Nazwa wydawnictwa jest za długa.';
            }

            if (mb_strlen($old['siedziba']) > 150) {
                $errors[] = 'Siedziba wydawnictwa jest za długa.';
            }

            if (empty($errors)) {
                $this->publisherModel->update(
                    $id,
                    $old['nazwa'],
                    $old['siedziba'] !== '' ? $old['siedziba'] : null
                );

                $_SESSION['flash_success'] = 'Wydawnictwo zostało zaktualizowane.';
                redirect(url('/admin/publishers'));
            }
        }

        require __DIR__ . '/../views/admin/publishers/edit.php';
    }

    public function delete(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('/admin/publishers'));
        }

        requireValidCsrfToken();

        $id = (int)($_POST['id_wydawnictwo'] ?? 0);

        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Nieprawidłowe ID wydawnictwa.';
            redirect(url('/admin/publishers'));
        }

        try {
            $this->publisherModel->delete($id);
            $_SESSION['flash_success'] = 'Wydawnictwo zostało usunięte.';
        } catch (PDOException $exception) {
            $_SESSION['flash_error'] = 'Nie można usunąć wydawnictwa, jeśli jest przypisane do książki.';
        }

        redirect(url('/admin/publishers'));
    }
}