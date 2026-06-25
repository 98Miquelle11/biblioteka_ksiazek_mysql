<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Helpery bezpieczeństwa
|--------------------------------------------------------------------------
| Ten plik zawiera funkcje do:
| - bezpiecznego wyświetlania danych w HTML,
| - hashowania haseł,
| - sprawdzania haseł,
| - generowania tokenu CSRF,
| - sprawdzania tokenu CSRF,
| - przekierowań.
*/

/*
|--------------------------------------------------------------------------
| XSS protection
|--------------------------------------------------------------------------
| Funkcja e() zabezpiecza dane przed XSS.
|
| Zamiast:
| echo $book['tytul'];
|
| używaj:
| echo e($book['tytul']);
*/

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

/*
|--------------------------------------------------------------------------
| Hashowanie haseł
|--------------------------------------------------------------------------
| Przy rejestracji zapisujemy do bazy hash hasła,
| a nie zwykłe hasło.
*/

if (!function_exists('hashPassword')) {
    function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => 10,
        ]);
    }
}

if (!function_exists('verifyPassword')) {
    function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}

/*
|--------------------------------------------------------------------------
| CSRF protection
|--------------------------------------------------------------------------
| CSRF chroni formularze POST.
| Każdy formularz POST powinien mieć ukryte pole csrf_token.
*/

if (!function_exists('csrfToken')) {
    function csrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('verifyCsrfToken')) {
    function verifyCsrfToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

/*
|--------------------------------------------------------------------------
| Aliasy w stylu snake_case
|--------------------------------------------------------------------------
| Dzięki temu zadziałają obie wersje nazw:
| csrfToken() oraz csrf_token()
| verifyCsrfToken() oraz verify_csrf_token()
*/

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return csrfToken();
    }
}

if (!function_exists('verify_csrf_token')) {
    function verify_csrf_token(?string $token): bool
    {
        return verifyCsrfToken($token);
    }
}

/*
|--------------------------------------------------------------------------
| Wymuszenie poprawnego tokenu CSRF
|--------------------------------------------------------------------------
| Tej funkcji użyjemy później przy obsłudze formularzy POST.
*/

if (!function_exists('requireValidCsrfToken')) {
    function requireValidCsrfToken(): void
    {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(419);
            echo 'Nieprawidłowy token CSRF. Odśwież stronę i spróbuj ponownie.';
            exit;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Przekierowanie
|--------------------------------------------------------------------------
*/

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}