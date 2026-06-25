<?php

declare(strict_types=1);

/* Ten plik odpowiada za operacje na tabeli "czytelnik". */

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
}