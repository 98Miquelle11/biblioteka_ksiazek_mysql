<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Główny plik aplikacji
|--------------------------------------------------------------------------
| Ten plik jest punktem wejścia do aplikacji.
| Przeglądarka trafia tutaj, a ten plik decyduje,
| jaką stronę pokazać użytkownikowi.
*/

require_once __DIR__ . '/../app/helpers/security.php';
require_once __DIR__ . '/../app/helpers/session.php';
require_once __DIR__ . '/../app/helpers/login_attempts.php';

$pdo = require __DIR__ . '/../config/database.php';

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/BookController.php';
require_once __DIR__ . '/../app/controllers/ReservationController.php';
require_once __DIR__ . '/../app/controllers/LoanController.php';

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
*/

function url(string $path = ''): string
{
    global $basePath;

    if ($path === '' || $path === '/') {
        return $basePath;
    }

    return $basePath . $path;
}

/*
|--------------------------------------------------------------------------
| Pobranie aktualnej ścieżki URL
|--------------------------------------------------------------------------
*/

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($requestUri === false) {
    $requestUri = '/';
}

$route = str_replace($basePath, '', $requestUri);

if ($route === '') {
    $route = '/';
}

if ($route !== '/') {
    $route = rtrim($route, '/');
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
                    <a href="<?= e(url('/profile/edit')) ?>">Edytuj profil</a>
                    <a href="<?= e(url('/profile/password')) ?>">Zmień hasło</a>

                    <?php if (isAdmin()): ?>
                        <a href="<?= e(url('/admin')) ?>">Panel admina</a>
                        <a href="<?= e(url('/admin/loans')) ?>">Wypożyczenia</a>
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

        <script src="<?= e(url('/js/validation.js')) ?>"></script>
    </body>
    </html>
    <?php
}

/*
|--------------------------------------------------------------------------
| Routing
|--------------------------------------------------------------------------
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
        BookController::index($pdo);
        break;

    case '/books/show':
        BookController::show($pdo);
        break;

    case '/reservations/create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ReservationController::create($pdo);
        } else {
            header('Location: ' . url('/books'));
            exit;
        }
        break;

    case '/register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthController::register($pdo);
        } else {
            AuthController::showRegisterForm();
        }
        break;

    case '/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthController::login($pdo);
        } else {
            AuthController::showLoginForm();
        }
        break;

    case '/logout':
        AuthController::logout();
        break;

    case '/profile':
        ProfileController::show($pdo);
        break;

    case '/profile/edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ProfileController::update($pdo);
        } else {
            ProfileController::edit($pdo);
        }
        break;

    case '/profile/password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ProfileController::updatePassword($pdo);
        } else {
            ProfileController::passwordForm();
        }
        break;

    case '/admin':
        requireAdmin();

        renderHeader('Panel administratora');
        ?>
        <h2>Panel administratora</h2>
        <p>Ta strona jest dostępna tylko dla użytkownika z rolą admin.</p>

        <ul>
            <li><a href="<?= e(url('/admin/loans')) ?>">Wypożyczenia</a></li>
        </ul>
        <?php
        renderFooter();
        break;

    case '/admin/loans':
        LoanController::index($pdo);
        break;

    case '/admin/loans/create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            LoanController::store($pdo);
        } else {
            LoanController::createForm($pdo);
        }
        break;

    case '/admin/loans/return':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            LoanController::returnLoan($pdo);
        } else {
            header('Location: ' . url('/admin/loans'));
            exit;
        }
        break;

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