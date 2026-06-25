<?php

declare(strict_types=1);

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

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

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}

/* Ten plik zabezpiecza przed XSS (uniemożliwia cyberprzestępcom wstrzykiwanie złośliwych skryptów 
(np. JavaScript) do odwiedzanych przez użytkowników stron internetowyc), hashuje hasła, generuje 
tokeny CSRF i przekierowuje użytkownika. */