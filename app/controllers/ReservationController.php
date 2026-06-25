<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Reservation.php';

class ReservationController
{
    public static function create(PDO $pdo): void
    {
        requireLogin();
        requireValidCsrfToken();

        $copyId = (int) ($_POST['id_egzemplarz'] ?? 0);

        if ($copyId <= 0) {
            header('Location: ' . url('/books'));
            exit;
        }

        $copy = Reservation::findCopyForReservation($pdo, $copyId);

        if (!$copy) {
            header('Location: ' . url('/books'));
            exit;
        }

        $bookId = (int) $copy['id_ksiazka'];

        if ($copy['status_egzemplarza'] !== 'dostepny') {
            header('Location: ' . url('/books/show') . '?id=' . $bookId . '&reservation_error=unavailable');
            exit;
        }

        try {
            Reservation::create($pdo, $copyId, (int) currentUserId());

            header('Location: ' . url('/books/show') . '?id=' . $bookId . '&reserved=1');
            exit;
        } catch (Throwable $e) {
            header('Location: ' . url('/books/show') . '?id=' . $bookId . '&reservation_error=1');
            exit;
        }
    }
}