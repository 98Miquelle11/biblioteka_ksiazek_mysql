<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/User.php';

class ProfileController
{
    public static function show(PDO $pdo): void
    {
        requireLogin();

        $user = User::findById($pdo, currentUserId());

        if (!$user) {
            logoutUser();
            header('Location: ' . url('/login'));
            exit;
        }

        require __DIR__ . '/../views/profile/index.php';
    }

    public static function edit(PDO $pdo): void
    {
        requireLogin();

        $user = User::findById($pdo, currentUserId());

        if (!$user) {
            logoutUser();
            header('Location: ' . url('/login'));
            exit;
        }

        $errors = [];

        $old = [
            'imie' => $user['imie'] ?? '',
            'nazwisko' => $user['nazwisko'] ?? '',
            'email' => $user['email'] ?? '',
            'telefon' => $user['telefon'] ?? '',
        ];

        require __DIR__ . '/../views/profile/edit.php';
    }

    public static function update(PDO $pdo): void
    {
        requireLogin();
        requireValidCsrfToken();

        $userId = currentUserId();

        $imie = trim($_POST['imie'] ?? '');
        $nazwisko = trim($_POST['nazwisko'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefon = trim($_POST['telefon'] ?? '');

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

        $existingUser = User::findByEmail($pdo, $email);

        if ($existingUser && (int) $existingUser['id_czytelnik'] !== $userId) {
            $errors[] = 'Ten adres email jest już zajęty.';
        }

        if (!empty($errors)) {
            require __DIR__ . '/../views/profile/edit.php';
            return;
        }

        User::updateProfile($pdo, $userId, [
            'imie' => $imie,
            'nazwisko' => $nazwisko,
            'email' => $email,
            'telefon' => $telefon,
        ]);

        $_SESSION['user_email'] = $email;

        header('Location: ' . url('/profile') . '?updated=1');
        exit;
    }

    public static function passwordForm(): void
    {
        requireLogin();

        $errors = [];
        $changed = isset($_GET['changed']) && $_GET['changed'] === '1';

        require __DIR__ . '/../views/profile/password.php';
    }

    public static function updatePassword(PDO $pdo): void
    {
        requireLogin();
        requireValidCsrfToken();

        $user = User::findById($pdo, currentUserId());

        if (!$user) {
            logoutUser();
            header('Location: ' . url('/login'));
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $newPasswordConfirm = $_POST['new_password_confirm'] ?? '';

        $errors = [];
        $changed = false;

        if ($currentPassword === '') {
            $errors[] = 'Obecne hasło jest wymagane.';
        }

        if ($newPassword === '') {
            $errors[] = 'Nowe hasło jest wymagane.';
        } elseif (strlen($newPassword) < 8) {
            $errors[] = 'Nowe hasło musi mieć minimum 8 znaków.';
        }

        if ($newPassword !== $newPasswordConfirm) {
            $errors[] = 'Nowe hasła nie są takie same.';
        }

        if (!verifyPassword($currentPassword, $user['password_hash'])) {
            $errors[] = 'Obecne hasło jest nieprawidłowe.';
        }

        if (!empty($errors)) {
            require __DIR__ . '/../views/profile/password.php';
            return;
        }

        User::updatePassword($pdo, currentUserId(), hashPassword($newPassword));

        header('Location: ' . url('/profile/password') . '?changed=1');
        exit;
    }
}