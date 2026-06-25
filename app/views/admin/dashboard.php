<?php
$title = 'Panel administratora';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="<?= e(url('/css/style.css')) ?>">
</head>
<body>
    <main class="container">
        <h1>Panel administratora</h1>

        <p>Ta strona jest dostępna tylko dla użytkownika z rolą admin.</p>

        <section>
            <h2>Statystyki</h2>

            <div class="admin-dashboard">
                <div class="dashboard-card">
                    <h3>Liczba użytkowników</h3>
                    <p><?= e((string)$stats['users_count']) ?></p>
                </div>

                <div class="dashboard-card">
                    <h3>Liczba książek</h3>
                    <p><?= e((string)$stats['books_count']) ?></p>
                </div>

                <div class="dashboard-card">
                    <h3>Liczba egzemplarzy</h3>
                    <p><?= e((string)$stats['copies_count']) ?></p>
                </div>

                <div class="dashboard-card">
                    <h3>Aktywne rezerwacje</h3>
                    <p><?= e((string)$stats['active_reservations_count']) ?></p>
                </div>

                <div class="dashboard-card">
                    <h3>Liczba wypożyczeń</h3>
                    <p><?= e((string)$stats['loans_count']) ?></p>
                </div>

                <div class="dashboard-card">
                    <h3>Suma wpłat</h3>
                    <p><?= e(number_format((float)$stats['payments_sum'], 2, ',', ' ')) ?> zł</p>
                </div>
            </div>
        </section>

        <section>
            <h2>Zarządzanie</h2>

            <ul>
                <li><a href="<?= e(url('/admin/loans')) ?>">Wypożyczenia</a></li>
                <li><a href="<?= e(url('/admin/authors')) ?>">Autorzy</a></li>
                <li><a href="<?= e(url('/admin/publishers')) ?>">Wydawnictwa</a></li>
                <li><a href="<?= e(url('/admin/books')) ?>">Książki</a></li>
                <li><a href="<?= e(url('/admin/copies')) ?>">Egzemplarze</a></li>
            </ul>
        </section>

        <p>
            <a href="<?= e(url('/')) ?>">Powrót na stronę główną</a>
        </p>
    </main>
</body>
</html>