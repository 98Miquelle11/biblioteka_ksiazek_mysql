<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Blokada konta po błędnych logowaniach
|--------------------------------------------------------------------------
| Ten plik obsługuje zabezpieczenie:
| - po błędnym haśle zwiększa licznik nieudanych logowań,
| - po 5 błędnych próbach blokuje konto na 15 minut,
| - po poprawnym logowaniu zeruje licznik i usuwa blokadę.
*/

if (!function_exists('isAccountBlocked')) {
    function isAccountBlocked(array $user): bool
    {
        if (empty($user['blokada_do'])) {
            return false;
        }

        $blockUntil = strtotime((string) $user['blokada_do']);

        if ($blockUntil === false) {
            return false;
        }

        return $blockUntil > time();
    }
}

if (!function_exists('getAccountBlockMessage')) {
    function getAccountBlockMessage(array $user): string
    {
        if (empty($user['blokada_do'])) {
            return 'Konto nie jest zablokowane.';
        }

        return 'Konto jest tymczasowo zablokowane do: ' . $user['blokada_do'];
    }
}

if (!function_exists('registerFailedLogin')) {
    function registerFailedLogin(PDO $pdo, array $user): void
    {
        $userId = (int) $user['id_czytelnik'];
        $failedAttempts = (int) ($user['nieudane_logowania'] ?? 0);
        $newFailedAttempts = $failedAttempts + 1;

        if ($newFailedAttempts >= 5) {
            $stmt = $pdo->prepare("
                UPDATE czytelnik
                SET 
                    nieudane_logowania = :nieudane_logowania,
                    blokada_do = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                WHERE id_czytelnik = :id_czytelnik
            ");

            $stmt->execute([
                'nieudane_logowania' => $newFailedAttempts,
                'id_czytelnik' => $userId,
            ]);

            return;
        }

        $stmt = $pdo->prepare("
            UPDATE czytelnik
            SET nieudane_logowania = :nieudane_logowania
            WHERE id_czytelnik = :id_czytelnik
        ");

        $stmt->execute([
            'nieudane_logowania' => $newFailedAttempts,
            'id_czytelnik' => $userId,
        ]);
    }
}

if (!function_exists('clearFailedLogins')) {
    function clearFailedLogins(PDO $pdo, int $userId): void
    {
        $stmt = $pdo->prepare("
            UPDATE czytelnik
            SET 
                nieudane_logowania = 0,
                blokada_do = NULL
            WHERE id_czytelnik = :id_czytelnik
        ");

        $stmt->execute([
            'id_czytelnik' => $userId,
        ]);
    }
}