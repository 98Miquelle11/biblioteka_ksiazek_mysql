<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/User.php';

class AuthController
{
    public static function showRegisterForm(): void
    {
        $errors = [];
        $old = [
            'imie' => '',
            'nazwisko' => '',
            'email' => '',
            'telefon' => '',
        ];

        require __DIR__ . '/../views/auth/register.php';
    }

    public static function register(PDO $pdo): void
    {
        requireValidCsrfToken();

        $imie = trim($_POST['imie'] ?? '');
        $nazwisko = trim($_POST['nazwisko'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefon = trim($_POST['telefon'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        $errors = [];

        $old = [
            'imie' => $imie,
            'nazwisko' => $nazwisko,
            'email' => $email,
            'telefon' => $telefon,
        ];

        if ($imie === '') {
            $errors[] = 'Imię jest wymagane.';
        }

        if ($nazwisko === '') {
            $errors[] = 'Nazwisko jest wymagane.';
        }

        if ($email === '') {
            $errors[] = 'Email jest wymagany.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email ma niepoprawny format.';
        }

        if ($password === '') {
            $errors[] = 'Hasło jest wymagane.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Hasło musi mieć minimum 8 znaków.';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Hasła nie są takie same.';
        }

        if ($email !== '' && User::findByEmail($pdo, $email)) {
            $errors[] = 'Konto z takim adresem email już istnieje.';
        }

        if (!empty($errors)) {
            require __DIR__ . '/../views/auth/register.php';
            return;
        }

        User::create($pdo, [
            'imie' => $imie,
            'nazwisko' => $nazwisko,
            'email' => $email,
            'telefon' => $telefon,
            'password_hash' => hashPassword($password),
        ]);

        header('Location: ' . url('/login') . '?registered=1');
        exit;
    }

    public static function showLoginForm(): void
    {
        $errors = [];
        $old = [
            'email' => '',
        ];

        $registered = isset($_GET['registered']) && $_GET['registered'] === '1';

        require __DIR__ . '/../views/auth/login.php';
    }

    public static function login(PDO $pdo): void
    {
        requireValidCsrfToken();

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];

        $old = [
            'email' => $email,
        ];

        $registered = false;

        if ($email === '') {
            $errors[] = 'Email jest wymagany.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email ma niepoprawny format.';
        }

        if ($password === '') {
            $errors[] = 'Hasło jest wymagane.';
        }

        if (!empty($errors)) {
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        $user = User::findByEmail($pdo, $email);

        if (!$user) {
            $errors[] = 'Nieprawidłowy email lub hasło.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        if (isAccountBlocked($user)) {
            $errors[] = getAccountBlockMessage($user);
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        if (!verifyPassword($password, $user['password_hash'])) {
            registerFailedLogin($pdo, $user);

            $errors[] = 'Nieprawidłowy email lub hasło.';
            require __DIR__ . '/../views/auth/login.php';
            return;
        }

        clearFailedLogins($pdo, (int) $user['id_czytelnik']);
        loginUser($user);

        if ($user['rola'] === 'admin') {
            header('Location: ' . url('/admin'));
            exit;
        }

        header('Location: ' . url('/profile'));
        exit;
    }

    public static function logout(): void
    {
        logoutUser();

        header('Location: ' . url('/login'));
        exit;
    }
}