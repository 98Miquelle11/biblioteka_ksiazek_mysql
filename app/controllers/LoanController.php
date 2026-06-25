<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Loan.php';

class LoanController
{
    public static function index(PDO $pdo): void
    {
        requireAdmin();

        $loans = Loan::getAll($pdo);

        require __DIR__ . '/../views/admin/loans/index.php';
    }

    public static function createForm(PDO $pdo): void
    {
        requireAdmin();

        $errors = [];

        $old = [
            'id_egzemplarz' => '',
            'id_czytelnik' => '',
            'data_wypozyczenie' => date('Y-m-d'),
            'przewidywana_data_zwrot' => date('Y-m-d', strtotime('+14 days')),
        ];

        $copies = Loan::getAvailableCopies($pdo);
        $readers = Loan::getReaders($pdo);

        require __DIR__ . '/../views/admin/loans/create.php';
    }

    public static function store(PDO $pdo): void
    {
        requireAdmin();
        requireValidCsrfToken();

        $copies = Loan::getAvailableCopies($pdo);
        $readers = Loan::getReaders($pdo);

        $old = self::readFormData();
        $errors = self::validate($old, $copies, $readers);

        if (!empty($errors)) {
            require __DIR__ . '/../views/admin/loans/create.php';
            return;
        }

        try {
            Loan::create(
                $pdo,
                (int)$old['id_egzemplarz'],
                (int)$old['id_czytelnik'],
                $old['data_wypozyczenie'],
                $old['przewidywana_data_zwrot']
            );

            header('Location: ' . url('/admin/loans') . '?created=1');
            exit;
        } catch (Throwable $e) {
            $errors[] = 'Nie udało się utworzyć wypożyczenia. Sprawdź, czy egzemplarz nadal jest dostępny.';

            $copies = Loan::getAvailableCopies($pdo);
            $readers = Loan::getReaders($pdo);

            require __DIR__ . '/../views/admin/loans/create.php';
            return;
        }
    }

    public static function returnLoan(PDO $pdo): void
    {
        requireAdmin();
        requireValidCsrfToken();

        $loanId = (int)($_POST['id_wypozyczenie'] ?? 0);

        if ($loanId <= 0) {
            header('Location: ' . url('/admin/loans') . '?return_error=1');
            exit;
        }

        $loan = Loan::findActiveById($pdo, $loanId);

        if (!$loan) {
            header('Location: ' . url('/admin/loans') . '?return_error=1');
            exit;
        }

        try {
            Loan::markReturned($pdo, $loanId);

            header('Location: ' . url('/admin/loans') . '?returned=1');
            exit;
        } catch (Throwable $e) {
            header('Location: ' . url('/admin/loans') . '?return_error=1');
            exit;
        }
    }

    private static function readFormData(): array
    {
        return [
            'id_egzemplarz' => trim((string)($_POST['id_egzemplarz'] ?? '')),
            'id_czytelnik' => trim((string)($_POST['id_czytelnik'] ?? '')),
            'data_wypozyczenie' => trim((string)($_POST['data_wypozyczenie'] ?? '')),
            'przewidywana_data_zwrot' => trim((string)($_POST['przewidywana_data_zwrot'] ?? '')),
        ];
    }

    private static function validate(array $data, array $copies, array $readers): array
    {
        $errors = [];

        if ($data['id_egzemplarz'] === '') {
            $errors[] = 'Wybierz egzemplarz do wypożyczenia.';
        } elseif (!self::isPositiveInteger($data['id_egzemplarz'])) {
            $errors[] = 'Wybrany egzemplarz jest nieprawidłowy.';
        } elseif (!self::idExistsInList((int)$data['id_egzemplarz'], $copies, 'id_egzemplarz')) {
            $errors[] = 'Wybrany egzemplarz nie istnieje albo nie jest już dostępny.';
        }

        if ($data['id_czytelnik'] === '') {
            $errors[] = 'Wybierz czytelnika.';
        } elseif (!self::isPositiveInteger($data['id_czytelnik'])) {
            $errors[] = 'Wybrany czytelnik jest nieprawidłowy.';
        } elseif (!self::idExistsInList((int)$data['id_czytelnik'], $readers, 'id_czytelnik')) {
            $errors[] = 'Wybrany czytelnik nie istnieje.';
        }

        if ($data['data_wypozyczenie'] === '') {
            $errors[] = 'Data wypożyczenia jest wymagana.';
        } elseif (!self::isValidDate($data['data_wypozyczenie'])) {
            $errors[] = 'Data wypożyczenia ma niepoprawny format.';
        }

        if ($data['przewidywana_data_zwrot'] === '') {
            $errors[] = 'Przewidywana data zwrotu jest wymagana.';
        } elseif (!self::isValidDate($data['przewidywana_data_zwrot'])) {
            $errors[] = 'Przewidywana data zwrotu ma niepoprawny format.';
        }

        if (
            self::isValidDate($data['data_wypozyczenie'])
            && self::isValidDate($data['przewidywana_data_zwrot'])
        ) {
            $loanDate = new DateTimeImmutable($data['data_wypozyczenie']);
            $expectedReturnDate = new DateTimeImmutable($data['przewidywana_data_zwrot']);

            if ($expectedReturnDate < $loanDate) {
                $errors[] = 'Przewidywana data zwrotu nie może być wcześniejsza niż data wypożyczenia.';
            }

            if ($expectedReturnDate > $loanDate->modify('+365 days')) {
                $errors[] = 'Przewidywana data zwrotu nie może być późniejsza niż 365 dni od daty wypożyczenia.';
            }
        }

        return $errors;
    }

    private static function isPositiveInteger(string $value): bool
    {
        return ctype_digit($value) && (int)$value > 0;
    }

    private static function isValidDate(string $date): bool
    {
        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d', $date);

        return $dateTime instanceof DateTimeImmutable
            && $dateTime->format('Y-m-d') === $date;
    }

    private static function idExistsInList(int $id, array $items, string $idField): bool
    {
        foreach ($items as $item) {
            if ((int)($item[$idField] ?? 0) === $id) {
                return true;
            }
        }

        return false;
    }
}