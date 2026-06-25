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

        $imie = trim((string)($_POST['imie'] ?? ''));
        $nazwisko = trim((string)($_POST['nazwisko'] ?? ''));
        $email = mb_strtolower(trim((string)($_POST['email'] ?? '')));
        $telefon = trim((string)($_POST['telefon'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $passwordConfirm = (string)($_POST['password_confirm'] ?? '');

        $errors = [];

        $old = [
            'imie' => $imie,
            'nazwisko' => $nazwisko,
            'email' => $email,
            'telefon' => $telefon,
        ];

        if ($imie === '') {
            $errors[] = 'Imię jest wymagane.';
        } elseif (mb_strlen($imie) > 100) {
            $errors[] = 'Imię może mieć maksymalnie 100 znaków.';
        }

        if ($nazwisko === '') {
            $errors[] = 'Nazwisko jest wymagane.';
        } elseif (mb_strlen($nazwisko) > 100) {
            $errors[] = 'Nazwisko może mieć maksymalnie 100 znaków.';
        }

        if ($email === '') {
            $errors[] = 'Email jest wymagany.';
        } elseif (mb_strlen($email) > 255) {
            $errors[] = 'Email może mieć maksymalnie 255 znaków.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email ma niepoprawny format.';
        }

        if ($telefon !== '') {
            $phonePattern = '/^[0-9+\-\s]{7,20}$/';

            if (!preg_match($phonePattern, $telefon)) {
                $errors[] = 'Telefon może zawierać tylko cyfry, spacje, plus i myślniki oraz mieć od 7 do 20 znaków.';
            }
        }

        if ($password === '') {
            $errors[] = 'Hasło jest wymagane.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Hasło musi mieć minimum 8 znaków.';
        } elseif (strlen($password) > 255) {
            $errors[] = 'Hasło jest za długie.';
        }

        if ($passwordConfirm === '') {
            $errors[] = 'Powtórzenie hasła jest wymagane.';
        } elseif ($password !== $passwordConfirm) {
            $errors[] = 'Hasła nie są takie same.';
        }

        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && User::findByEmail($pdo, $email)) {
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

        $email = mb_strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');

        $errors = [];

        $old = [
            'email' => $email,
        ];

        $registered = false;

        if ($email === '') {
            $errors[] = 'Email jest wymagany.';
        } elseif (mb_strlen($email) > 255) {
            $errors[] = 'Email może mieć maksymalnie 255 znaków.';
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

        clearFailedLogins($pdo, (int)$user['id_czytelnik']);
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