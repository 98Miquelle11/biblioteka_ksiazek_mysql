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

        $userId = (int)currentUserId();

        $imie = trim((string)($_POST['imie'] ?? ''));
        $nazwisko = trim((string)($_POST['nazwisko'] ?? ''));
        $email = mb_strtolower(trim((string)($_POST['email'] ?? '')));
        $telefon = trim((string)($_POST['telefon'] ?? ''));

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

        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $existingUser = User::findByEmail($pdo, $email);

            if ($existingUser && (int)$existingUser['id_czytelnik'] !== $userId) {
                $errors[] = 'Ten adres email jest już zajęty.';
            }
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

        $currentPassword = (string)($_POST['current_password'] ?? '');
        $newPassword = (string)($_POST['new_password'] ?? '');
        $newPasswordConfirm = (string)($_POST['new_password_confirm'] ?? '');

        $errors = [];
        $changed = false;

        if ($currentPassword === '') {
            $errors[] = 'Obecne hasło jest wymagane.';
        }

        if ($newPassword === '') {
            $errors[] = 'Nowe hasło jest wymagane.';
        } elseif (strlen($newPassword) < 8) {
            $errors[] = 'Nowe hasło musi mieć minimum 8 znaków.';
        } elseif (strlen($newPassword) > 255) {
            $errors[] = 'Nowe hasło jest za długie.';
        }

        if ($newPasswordConfirm === '') {
            $errors[] = 'Powtórzenie nowego hasła jest wymagane.';
        } elseif ($newPassword !== $newPasswordConfirm) {
            $errors[] = 'Nowe hasła nie są takie same.';
        }

        if ($currentPassword !== '' && !verifyPassword($currentPassword, $user['password_hash'])) {
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