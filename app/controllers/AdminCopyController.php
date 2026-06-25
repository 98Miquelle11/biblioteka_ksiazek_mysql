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
            $errors = $this->validate($copy, $editions, $statuses);

            if (empty($errors)) {
                $this->copyModel->create($this->normalizeData($copy));

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

        $copy = [
            'id_wydanie' => (string)($existingCopy['id_wydanie'] ?? ''),
            'forma' => (string)($existingCopy['forma'] ?? 'fizyczna'),
            'id_status_egzemplarz' => (string)($existingCopy['id_status_egzemplarz'] ?? ''),
            'cena_zakup' => (string)($existingCopy['cena_zakup'] ?? ''),
            'uwaga' => (string)($existingCopy['uwaga'] ?? ''),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            requireValidCsrfToken();

            $copy = $this->prepareFormData($_POST);
            $errors = $this->validate($copy, $editions, $statuses);

            if (empty($errors)) {
                $this->copyModel->update($id, $this->normalizeData($copy));

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
            'id_wydanie' => trim((string)($data['id_wydanie'] ?? '')),
            'forma' => trim((string)($data['forma'] ?? '')),
            'id_status_egzemplarz' => trim((string)($data['id_status_egzemplarz'] ?? '')),
            'cena_zakup' => $price,
            'uwaga' => trim((string)($data['uwaga'] ?? '')),
        ];
    }

    private function validate(array $copy, array $editions, array $statuses): array
    {
        $errors = [];

        if ($copy['id_wydanie'] === '') {
            $errors[] = 'Wybierz wydanie książki.';
        } elseif (!$this->isPositiveInteger($copy['id_wydanie'])) {
            $errors[] = 'Wybrane wydanie książki jest nieprawidłowe.';
        } elseif (!$this->idExistsInList((int)$copy['id_wydanie'], $editions, 'id_wydanie')) {
            $errors[] = 'Wybrane wydanie książki nie istnieje.';
        }

        $allowedForms = ['fizyczna', 'ebook', 'audiobook'];

        if ($copy['forma'] === '') {
            $errors[] = 'Wybierz formę egzemplarza.';
        } elseif (!in_array($copy['forma'], $allowedForms, true)) {
            $errors[] = 'Wybierz poprawną formę egzemplarza.';
        }

        if ($copy['id_status_egzemplarz'] === '') {
            $errors[] = 'Wybierz status egzemplarza.';
        } elseif (!$this->isPositiveInteger($copy['id_status_egzemplarz'])) {
            $errors[] = 'Wybrany status egzemplarza jest nieprawidłowy.';
        } elseif (!$this->idExistsInList((int)$copy['id_status_egzemplarz'], $statuses, 'id_status_egzemplarz')) {
            $errors[] = 'Wybrany status egzemplarza nie istnieje.';
        }

        if ($copy['cena_zakup'] === '') {
            $errors[] = 'Podaj cenę zakupu.';
        } elseif (!preg_match('/^\d+(\.\d{1,2})?$/', $copy['cena_zakup'])) {
            $errors[] = 'Cena zakupu musi być liczbą z maksymalnie dwoma miejscami po przecinku.';
        } elseif ((float)$copy['cena_zakup'] < 0) {
            $errors[] = 'Cena zakupu musi być większa lub równa 0.';
        } elseif ((float)$copy['cena_zakup'] > 999999.99) {
            $errors[] = 'Cena zakupu jest za wysoka.';
        }

        if (mb_strlen($copy['uwaga']) > 255) {
            $errors[] = 'Uwaga może mieć maksymalnie 255 znaków.';
        }

        return $errors;
    }

    private function normalizeData(array $copy): array
    {
        return [
            'id_wydanie' => (int)$copy['id_wydanie'],
            'forma' => $copy['forma'],
            'id_status_egzemplarz' => (int)$copy['id_status_egzemplarz'],
            'cena_zakup' => number_format((float)$copy['cena_zakup'], 2, '.', ''),
            'uwaga' => $copy['uwaga'],
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