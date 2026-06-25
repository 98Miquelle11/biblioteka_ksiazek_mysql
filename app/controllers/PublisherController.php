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

            $old = $this->prepareFormData($_POST);
            $errors = $this->validate($old);

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
            'nazwa' => (string)($publisher['nazwa'] ?? ''),
            'siedziba' => (string)($publisher['siedziba'] ?? ''),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireValidCsrfToken();

            $old = $this->prepareFormData($_POST);
            $errors = $this->validate($old);

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

    private function prepareFormData(array $data): array
    {
        return [
            'nazwa' => trim((string)($data['nazwa'] ?? '')),
            'siedziba' => trim((string)($data['siedziba'] ?? '')),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['nazwa'] === '') {
            $errors[] = 'Nazwa wydawnictwa jest wymagana.';
        } elseif (mb_strlen($data['nazwa']) > 150) {
            $errors[] = 'Nazwa wydawnictwa może mieć maksymalnie 150 znaków.';
        }

        if (mb_strlen($data['siedziba']) > 150) {
            $errors[] = 'Siedziba wydawnictwa może mieć maksymalnie 150 znaków.';
        }

        return $errors;
    }
}