<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Obsługa sesji użytkownika
|--------------------------------------------------------------------------
| Ten plik odpowiada za:
| - start sesji,
| - sprawdzanie, czy użytkownik jest zalogowany,
| - sprawdzanie, czy użytkownik jest adminem,
| - zapis danych użytkownika do sesji po logowaniu,
| - usunięcie sesji po wylogowaniu.
*/

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool
    {
        return !empty($_SESSION['user_id']);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}

if (!function_exists('currentUserId')) {
    function currentUserId(): ?int
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return (int) $_SESSION['user_id'];
    }
}

if (!function_exists('currentUserRole')) {
    function currentUserRole(): ?string
    {
        return $_SESSION['user_role'] ?? null;
    }
}

if (!function_exists('currentUserEmail')) {
    function currentUserEmail(): ?string
    {
        return $_SESSION['user_email'] ?? null;
    }
}

if (!function_exists('loginUser')) {
    function loginUser(array $user): void
    {
        if (
            !isset($user['id_czytelnik']) ||
            !isset($user['rola']) ||
            !isset($user['email'])
        ) {
            throw new InvalidArgumentException('Brakuje danych użytkownika do zapisania sesji.');
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['id_czytelnik'];
        $_SESSION['user_role'] = $user['rola'];
        $_SESSION['user_email'] = $user['email'];
    }
}

if (!function_exists('logoutUser')) {
    function logoutUser(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }

        session_destroy();
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin(): void
    {
        if (!isLoggedIn()) {
            if (function_exists('url')) {
                header('Location: ' . url('/login'));
            } else {
                header('Location: /biblioteka_ksiazek_mysql/public/login');
            }

            exit;
        }
    }
}

if (!function_exists('requireAdmin')) {
    function requireAdmin(): void
    {
        if (!isLoggedIn()) {
            if (function_exists('url')) {
                header('Location: ' . url('/login'));
            } else {
                header('Location: /biblioteka_ksiazek_mysql/public/login');
            }

            exit;
        }

        if (!isAdmin()) {
            http_response_code(403);
            echo 'Brak dostępu.';
            exit;
        }
    }
}