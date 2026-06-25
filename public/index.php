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

error_reporting(E_ALL);
ini_set('display_errors', '0');

set_error_handler(function (
    int $severity,
    string $message,
    string $file,
    int $line
): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

require_once __DIR__ . '/../app/helpers/security.php';
require_once __DIR__ . '/../app/helpers/session.php';
require_once __DIR__ . '/../app/helpers/login_attempts.php';

$pdo = require __DIR__ . '/../config/database.php';

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';
require_once __DIR__ . '/../app/controllers/ProfileActivityController.php';
require_once __DIR__ . '/../app/controllers/BookController.php';
require_once __DIR__ . '/../app/controllers/ReservationController.php';
require_once __DIR__ . '/../app/controllers/LoanController.php';
require_once __DIR__ . '/../app/controllers/AuthorController.php';
require_once __DIR__ . '/../app/controllers/PublisherController.php';
require_once __DIR__ . '/../app/controllers/AdminBookController.php';
require_once __DIR__ . '/../app/controllers/AdminCopyController.php';
require_once __DIR__ . '/../app/controllers/AdminDashboardController.php';

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
| Logowanie błędów
|--------------------------------------------------------------------------
*/

function logApplicationError(Throwable $exception): void
{
    $logsDirectory = __DIR__ . '/../logs';
    $logFile = $logsDirectory . '/errors.log';

    if (!is_dir($logsDirectory)) {
        mkdir($logsDirectory, 0777, true);
    }

    $message = '[' . date('Y-m-d H:i:s') . '] '
        . get_class($exception) . ': '
        . $exception->getMessage()
        . ' in ' . $exception->getFile()
        . ':' . $exception->getLine()
        . PHP_EOL
        . $exception->getTraceAsString()
        . PHP_EOL
        . str_repeat('-', 80)
        . PHP_EOL;

    file_put_contents($logFile, $message, FILE_APPEND);
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
                    <a href="<?= e(url('/profile/loans')) ?>">Moje wypożyczenia</a>
                    <a href="<?= e(url('/profile/reservations')) ?>">Moje rezerwacje</a>

                    <?php if (isAdmin()): ?>
                        <a href="<?= e(url('/admin')) ?>">Panel admina</a>
                        <a href="<?= e(url('/admin/loans')) ?>">Wypożyczenia</a>
                        <a href="<?= e(url('/admin/authors')) ?>">Autorzy</a>
                        <a href="<?= e(url('/admin/publishers')) ?>">Wydawnictwa</a>
                        <a href="<?= e(url('/admin/books')) ?>">Książki</a>
                        <a href="<?= e(url('/admin/copies')) ?>">Egzemplarze</a>
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

try {
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

        case '/profile/loans':
            $profileActivityController = new ProfileActivityController($pdo);
            $profileActivityController->loans();
            break;

        case '/profile/reservations':
            $profileActivityController = new ProfileActivityController($pdo);
            $profileActivityController->reservations();
            break;

        case '/admin':
            $adminDashboardController = new AdminDashboardController($pdo);
            $adminDashboardController->index();
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

        case '/admin/authors':
            $authorController = new AuthorController($pdo);
            $authorController->index();
            break;

        case '/admin/authors/create':
            $authorController = new AuthorController($pdo);
            $authorController->create();
            break;

        case '/admin/authors/edit':
            $authorController = new AuthorController($pdo);
            $authorController->edit();
            break;

        case '/admin/authors/delete':
            $authorController = new AuthorController($pdo);
            $authorController->delete();
            break;

        case '/admin/publishers':
            $publisherController = new PublisherController($pdo);
            $publisherController->index();
            break;

        case '/admin/publishers/create':
            $publisherController = new PublisherController($pdo);
            $publisherController->create();
            break;

        case '/admin/publishers/edit':
            $publisherController = new PublisherController($pdo);
            $publisherController->edit();
            break;

        case '/admin/publishers/delete':
            $publisherController = new PublisherController($pdo);
            $publisherController->delete();
            break;

        case '/admin/books':
            $adminBookController = new AdminBookController($pdo);
            $adminBookController->index();
            break;

        case '/admin/books/create':
            $adminBookController = new AdminBookController($pdo);
            $adminBookController->create();
            break;

        case '/admin/books/edit':
            $adminBookController = new AdminBookController($pdo);
            $adminBookController->edit();
            break;

        case '/admin/books/delete':
            $adminBookController = new AdminBookController($pdo);
            $adminBookController->delete();
            break;

        case '/admin/copies':
            $adminCopyController = new AdminCopyController($pdo);
            $adminCopyController->index();
            break;

        case '/admin/copies/create':
            $adminCopyController = new AdminCopyController($pdo);
            $adminCopyController->create();
            break;

        case '/admin/copies/edit':
            $adminCopyController = new AdminCopyController($pdo);
            $adminCopyController->edit();
            break;

        case '/admin/copies/delete':
            $adminCopyController = new AdminCopyController($pdo);
            $adminCopyController->delete();
            break;

        default:
            http_response_code(404);
            require __DIR__ . '/../app/views/errors/404.php';
            break;
    }
} catch (Throwable $exception) {
    logApplicationError($exception);

    if (!headers_sent()) {
        http_response_code(500);
    }

    require __DIR__ . '/../app/views/errors/500.php';
}