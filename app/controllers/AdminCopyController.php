<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/AdminCopy.php';

class AdminCopyController
{
    private AdminCopy $copyModel;

    public function __construct(PDO $pdo)
    {
        $this->copyModel = new AdminCopy($pdo);
    }

    public function index(): void
    {
        requireAdmin();

        $copies = $this->copyModel->all();

        require __DIR__ . '/../views/admin/copies/index.php';
    }

    public function create(): void
    {
        requireAdmin();

        $errors = [];
        $editions = $this->copyModel->editionsForSelect();
        $statuses = $this->copyModel->statusesForSelect();

        $copy = [
            'id_wydanie' => '',
            'forma' => 'fizyczna',
            'id_status_egzemplarz' => '',
            'cena_zakup' => '',
            'uwaga' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireValidCsrfToken();

            $copy = $this->prepareFormData($_POST);
            $errors = $this->validate($copy);

            if (empty($errors)) {
                $this->copyModel->create($copy);

                $_SESSION['success'] = 'Egzemplarz został dodany.';
                redirect(url('/admin/copies'));
            }
        }

        require __DIR__ . '/../views/admin/copies/create.php';
    }

    public function edit(): void
    {
        requireAdmin();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'Nieprawidłowe ID egzemplarza.';
            redirect(url('/admin/copies'));
        }

        $existingCopy = $this->copyModel->find($id);

        if ($existingCopy === null) {
            $_SESSION['error'] = 'Nie znaleziono egzemplarza.';
            redirect(url('/admin/copies'));
        }

        $errors = [];
        $editions = $this->copyModel->editionsForSelect();
        $statuses = $this->copyModel->statusesForSelect();
        $copy = $existingCopy;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireValidCsrfToken();

            $copy = $this->prepareFormData($_POST);
            $errors = $this->validate($copy);

            if (empty($errors)) {
                $this->copyModel->update($id, $copy);

                $_SESSION['success'] = 'Egzemplarz został zaktualizowany.';
                redirect(url('/admin/copies'));
            }
        }

        require __DIR__ . '/../views/admin/copies/edit.php';
    }

    public function delete(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('/admin/copies'));
        }

        requireValidCsrfToken();

        $id = (int)($_POST['id_egzemplarz'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'Nieprawidłowe ID egzemplarza.';
            redirect(url('/admin/copies'));
        }

        try {
            $this->copyModel->delete($id);
            $_SESSION['success'] = 'Egzemplarz został usunięty.';
        } catch (PDOException $exception) {
            $_SESSION['error'] = 'Nie udało się usunąć egzemplarza. Może być powiązany z wypożyczeniem, rezerwacją albo logiem.';
        }

        redirect(url('/admin/copies'));
    }

    private function prepareFormData(array $data): array
    {
        $price = str_replace(',', '.', trim((string)($data['cena_zakup'] ?? '')));

        return [
            'id_wydanie' => (int)($data['id_wydanie'] ?? 0),
            'forma' => trim((string)($data['forma'] ?? '')),
            'id_status_egzemplarz' => (int)($data['id_status_egzemplarz'] ?? 0),
            'cena_zakup' => $price,
            'uwaga' => trim((string)($data['uwaga'] ?? '')),
        ];
    }

    private function validate(array $copy): array
    {
        $errors = [];

        if ($copy['id_wydanie'] <= 0) {
            $errors[] = 'Wybierz wydanie książki.';
        }

        $allowedForms = ['fizyczna', 'ebook', 'audiobook'];

        if (!in_array($copy['forma'], $allowedForms, true)) {
            $errors[] = 'Wybierz poprawną formę egzemplarza.';
        }

        if ($copy['id_status_egzemplarz'] <= 0) {
            $errors[] = 'Wybierz status egzemplarza.';
        }

        if ($copy['cena_zakup'] === '') {
            $errors[] = 'Podaj cenę zakupu.';
        } elseif (!is_numeric($copy['cena_zakup']) || (float)$copy['cena_zakup'] < 0) {
            $errors[] = 'Cena zakupu musi być liczbą większą lub równą 0.';
        }

        if (mb_strlen($copy['uwaga']) > 255) {
            $errors[] = 'Uwaga może mieć maksymalnie 255 znaków.';
        }

        return $errors;
    }
}