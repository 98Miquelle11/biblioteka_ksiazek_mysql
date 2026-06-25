<?php

declare(strict_types=1);

class User
{
    public static function findByEmail(PDO $pdo, string $email): ?array
    {
        $stmt = $pdo->prepare("
            SELECT *
            FROM czytelnik
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            'email' => $email,
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function findById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare("
            SELECT *
            FROM czytelnik
            WHERE id_czytelnik = :id_czytelnik
            LIMIT 1
        ");

        $stmt->execute([
            'id_czytelnik' => $id,
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function create(PDO $pdo, array $data): bool
    {
        $stmt = $pdo->prepare("
            INSERT INTO czytelnik (
                imie,
                nazwisko,
                email,
                password_hash,
                rola,
                telefon,
                saldo,
                nieudane_logowania,
                blokada_do
            ) VALUES (
                :imie,
                :nazwisko,
                :email,
                :password_hash,
                'user',
                :telefon,
                0.00,
                0,
                NULL
            )
        ");

        return $stmt->execute([
            'imie' => $data['imie'],
            'nazwisko' => $data['nazwisko'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'telefon' => $data['telefon'],
        ]);
    }

    public static function updateProfile(PDO $pdo, int $id, array $data): bool
    {
        $stmt = $pdo->prepare("
            UPDATE czytelnik
            SET 
                imie = :imie,
                nazwisko = :nazwisko,
                email = :email,
                telefon = :telefon
            WHERE id_czytelnik = :id_czytelnik
        ");

        return $stmt->execute([
            'imie' => $data['imie'],
            'nazwisko' => $data['nazwisko'],
            'email' => $data['email'],
            'telefon' => $data['telefon'],
            'id_czytelnik' => $id,
        ]);
    }

    public static function updatePassword(PDO $pdo, int $id, string $passwordHash): bool
    {
        $stmt = $pdo->prepare("
            UPDATE czytelnik
            SET password_hash = :password_hash
            WHERE id_czytelnik = :id_czytelnik
        ");

        return $stmt->execute([
            'password_hash' => $passwordHash,
            'id_czytelnik' => $id,
        ]);
    }
}