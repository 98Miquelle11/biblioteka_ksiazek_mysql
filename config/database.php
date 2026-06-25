<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

loadEnv(__DIR__ . '/.env');

if (!function_exists('getPDO')) {
    function getPDO(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');
        $database = env('DB_NAME', 'biblioteka');
        $username = env('DB_USER', 'root');
        $password = env('DB_PASS', '');
        $charset = env('DB_CHARSET', 'utf8mb4');

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}"; /* Połącz się z MySQL, z bazą biblioteka, użyj utf8mb4 */

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, /* Jeśli baza zwróci błąd, PHP ma go pokazać jako wyjątek */
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false, /* Używaj prawdziwych prepared statements z MySQL */
        ];

        $pdo = new PDO($dsn, $username, $password, $options);

        return $pdo;
    }
}

$pdo = getPDO();

return $pdo;
/* Ten plik konfiguruje połączenie z bazą danych MySQL i zwraca obiekt PDO (PHP Data Objects). */