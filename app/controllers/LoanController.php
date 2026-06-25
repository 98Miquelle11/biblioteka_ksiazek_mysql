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

        $copyId = (int) ($_POST['id_egzemplarz'] ?? 0);
        $readerId = (int) ($_POST['id_czytelnik'] ?? 0);
        $loanDate = trim($_POST['data_wypozyczenie'] ?? '');
        $expectedReturnDate = trim($_POST['przewidywana_data_zwrot'] ?? '');

        $errors = [];

        $old = [
            'id_egzemplarz' => (string) $copyId,
            'id_czytelnik' => (string) $readerId,
            'data_wypozyczenie' => $loanDate,
            'przewidywana_data_zwrot' => $expectedReturnDate,
        ];

        if ($copyId <= 0) {
            $errors[] = 'Wybierz egzemplarz do wypożyczenia.';
        }

        if ($readerId <= 0) {
            $errors[] = 'Wybierz czytelnika.';
        }

        if ($loanDate === '') {
            $errors[] = 'Data wypożyczenia jest wymagana.';
        }

        if ($expectedReturnDate === '') {
            $errors[] = 'Przewidywana data zwrotu jest wymagana.';
        }

        if (
            $loanDate !== ''
            && $expectedReturnDate !== ''
            && strtotime($expectedReturnDate) < strtotime($loanDate)
        ) {
            $errors[] = 'Przewidywana data zwrotu nie może być wcześniejsza niż data wypożyczenia.';
        }

        if (!empty($errors)) {
            $copies = Loan::getAvailableCopies($pdo);
            $readers = Loan::getReaders($pdo);

            require __DIR__ . '/../views/admin/loans/create.php';
            return;
        }

        try {
            Loan::create($pdo, $copyId, $readerId, $loanDate, $expectedReturnDate);

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

        $loanId = (int) ($_POST['id_wypozyczenie'] ?? 0);

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
}