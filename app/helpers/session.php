<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin(): void
    {
        if (!isLoggedIn()) {
            header('Location: /biblioteka_ksiazek_mysql/public/login');
            exit;
        }
    }
}

if (!function_exists('requireAdmin')) {
    function requireAdmin(): void
    {
        if (!isLoggedIn() || !isAdmin()) {
            http_response_code(403);
            echo 'Brak dostępu.';
            exit;
        }
    }
}

if (!function_exists('loginUser')) {
    function loginUser(array $user): void
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id_czytelnik'];
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
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}

/* Ten plik sprawdza, czy użytkownik jest zalogowany, czy ma rolę administratora,
wymusza logowanie, loguje i wylogowuje użytkownika. */