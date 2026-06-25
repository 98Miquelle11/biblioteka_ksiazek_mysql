<?php

declare(strict_types=1);

if (!function_exists('loadEnv')) {
    function loadEnv(string $path): void
    {
        if (!file_exists($path)) {
            throw new RuntimeException("Brak pliku .env: " . $path);
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            throw new RuntimeException("Nie można odczytać pliku .env.");
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || $line[0] === '#') {
                continue;
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);

            $name = trim($name);
            $value = trim($value);
            $value = trim($value, "\"'");

            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            putenv($name . '=' . $value);
        }
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = getenv($key);

        if ($value !== false) {
            return $value;
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        return $default;
    }
}
/* Ten plik czyta dane z pliku .env i pozwala np na pobranie nazwy bazy danych przez env('DB_NAME').*/