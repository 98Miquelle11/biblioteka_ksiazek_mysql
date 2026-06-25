<?php

declare(strict_types=1);

/* Ten plik obsługuje rejestrację: waliduje dane, sprawdza email, hashuje hasło
i zapisuje użytkownika. */

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
}