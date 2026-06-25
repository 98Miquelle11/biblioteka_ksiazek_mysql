<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Główny plik aplikacji
|--------------------------------------------------------------------------
| Ten plik jest punktem wejścia do aplikacji.
| To znaczy: przeglądarka trafia tutaj, a ten plik decyduje,
| jaką stronę pokazać użytkownikowi.
*/

require_once __DIR__ . '/../app/helpers/security.php';
require_once __DIR__ . '/../app/helpers/session.php';

/*
|--------------------------------------------------------------------------
| Podstawowe ustawienia
|--------------------------------------------------------------------------
*/

$basePath = '/biblioteka_ksiazek_mysql/public';

/*
|--------------------------------------------------------------------------
| Funkcja do tworzenia linków
|--------------------------------------------------------------------------
| Dzięki temu nie musimy wszędzie ręcznie pisać:
| /biblioteka_ksiazek_mysql/public
*/

function url(string $path = ''): string
{
    global $basePath;

    if ($path === '/') {
        return $basePath;
    }

    return $basePath . $path;
}

/*
|--------------------------------------------------------------------------
| Pobranie aktualnej ścieżki URL
|--------------------------------------------------------------------------
| Przykład:
| http://localhost/biblioteka_ksiazek_mysql/public/books
|
| Z tego zostanie wyciągnięte:
| /books
*/

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($requestUri === false) {
    $requestUri = '/';
}

$route = str_replace($basePath, '', $requestUri);

if ($route === '') {
    $route = '/';
}

/*
|--------------------------------------------------------------------------
| Funkcje pomocnicze do wyświetlania strony
|--------------------------------------------------------------------------
*/

function renderHeader(string $title): void
{
    ?>
    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($title) ?> - Biblioteka</title>
        <link rel="stylesheet" href="<?= e(url('/css/style.css')) ?>">
    </head>
    <body>
        <header>
            <h1>Biblioteka książek</h1>

            <nav>
                <a href="<?= e(url('/')) ?>">Strona główna</a>
                <a href="<?= e(url('/books')) ?>">Katalog książek</a>

                <?php if (isLoggedIn()): ?>
                    <a href="<?= e(url('/profile')) ?>">Profil</a>

                    <?php if (isAdmin()): ?>
                        <a href="<?= e(url('/admin')) ?>">Panel admina</a>
                    <?php endif; ?>

                    <a href="<?= e(url('/logout')) ?>">Wyloguj</a>
                <?php else: ?>
                    <a href="<?= e(url('/login')) ?>">Logowanie</a>
                    <a href="<?= e(url('/register')) ?>">Rejestracja</a>
                <?php endif; ?>
            </nav>
        </header>

        <main>
    <?php
}

function renderFooter(): void
{
    ?>
        </main>

        <footer>
            <p>&copy; <?= date('Y') ?> Biblioteka książek</p>
        </footer>
    </body>
    </html>
    <?php
}

/*
|--------------------------------------------------------------------------
| Routing
|--------------------------------------------------------------------------
| Tutaj decydujemy, co pokazać dla konkretnego adresu.
*/

switch ($route) {
    case '/':
        renderHeader('Strona główna');
        ?>
        <h2>Strona główna</h2>
        <p>To jest projekt biblioteki książek w PHP i MySQL.</p>
        <p>Ten plik działa jako główny router aplikacji.</p>
        <?php
        renderFooter();
        break;

    case '/books':
        renderHeader('Katalog książek');
        ?>
        <h2>Katalog książek</h2>
        <p>Tutaj później pojawi się lista książek z bazy danych.</p>
        <?php
        renderFooter();
        break;

    case '/login':
        renderHeader('Logowanie');
        ?>
        <h2>Logowanie</h2>

        <form method="POST" action="<?= e(url('/login')) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <div>
                <label for="email">Email</label><br>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="password">Hasło</label><br>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Zaloguj</button>
        </form>

        <p>Obsługę logowania dodamy w kolejnym kroku.</p>
        <?php
        renderFooter();
        break;

    case '/register':
        renderHeader('Rejestracja');
        ?>
        <h2>Rejestracja</h2>

        <form method="POST" action="<?= e(url('/register')) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <div>
                <label for="imie">Imię</label><br>
                <input type="text" id="imie" name="imie" required>
            </div>

            <div>
                <label for="nazwisko">Nazwisko</label><br>
                <input type="text" id="nazwisko" name="nazwisko" required>
            </div>

            <div>
                <label for="email">Email</label><br>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="telefon">Telefon</label><br>
                <input type="text" id="telefon" name="telefon">
            </div>

            <div>
                <label for="password">Hasło</label><br>
                <input type="password" id="password" name="password" required>
            </div>

            <div>
                <label for="password_confirm">Powtórz hasło</label><br>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>

            <button type="submit">Zarejestruj</button>
        </form>

        <p>Obsługę rejestracji dodamy w kolejnym kroku.</p>
        <?php
        renderFooter();
        break;

    case '/profile':
        requireLogin();

        renderHeader('Profil użytkownika');
        ?>
        <h2>Profil użytkownika</h2>
        <p>Ta strona będzie dostępna tylko po zalogowaniu.</p>
        <p>Email użytkownika z sesji: <?= e($_SESSION['user_email'] ?? '') ?></p>
        <?php
        renderFooter();
        break;

    case '/admin':
        requireAdmin();

        renderHeader('Panel administratora');
        ?>
        <h2>Panel administratora</h2>
        <p>Ta strona będzie dostępna tylko dla użytkownika z rolą admin.</p>
        <?php
        renderFooter();
        break;

    case '/logout':
        logoutUser();
        header('Location: ' . url('/login'));
        exit;

    default:
        http_response_code(404);

        renderHeader('404');
        ?>
        <h2>404 - Nie znaleziono strony</h2>
        <p>Adres, który próbujesz otworzyć, nie istnieje.</p>
        <p><a href="<?= e(url('/')) ?>">Wróć na stronę główną</a></p>
        <?php
        renderFooter();
        break;
}