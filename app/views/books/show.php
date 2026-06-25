<?php renderHeader('Szczegóły książki'); ?>

<h2>Szczegóły książki</h2>

<?php if (isset($_GET['reserved']) && $_GET['reserved'] === '1'): ?>
    <p style="color: green;">Rezerwacja została utworzona.</p>
<?php endif; ?>

<?php if (isset($_GET['reservation_error']) && $_GET['reservation_error'] === 'unavailable'): ?>
    <p style="color: red;">Ten egzemplarz nie jest już dostępny.</p>
<?php endif; ?>

<?php if (isset($_GET['reservation_error']) && $_GET['reservation_error'] === '1'): ?>
    <p style="color: red;">Nie udało się utworzyć rezerwacji.</p>
<?php endif; ?>

<table border="1" cellpadding="8">
    <tr>
        <th>Tytuł</th>
        <td><?= e($book['tytul'] ?? '') ?></td>
    </tr>
    <tr>
        <th>Autor</th>
        <td><?= e($book['autorzy'] ?? 'Brak danych') ?></td>
    </tr>
    <tr>
        <th>Gatunek</th>
        <td><?= e($book['gatunki'] ?? 'Brak danych') ?></td>
    </tr>
</table>

<h3>Egzemplarze</h3>

<?php if (empty($copies)): ?>
    <p>Brak egzemplarzy tej książki.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID egzemplarza</th>
                <th>Wydawnictwo</th>
                <th>ISBN</th>
                <th>Rok wydania</th>
                <th>Liczba stron</th>
                <th>Forma</th>
                <th>Status</th>
                <th>Akcja</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($copies as $copy): ?>
                <tr>
                    <td><?= e($copy['id_egzemplarz'] ?? '') ?></td>
                    <td><?= e($copy['wydawnictwo'] ?? '') ?></td>
                    <td><?= e($copy['isbn'] ?? '') ?></td>
                    <td><?= e($copy['rok_wydania'] ?? '') ?></td>
                    <td><?= e($copy['liczba_stron'] ?? '') ?></td>
                    <td><?= e($copy['forma'] ?? '') ?></td>
                    <td><?= e($copy['status_egzemplarza'] ?? '') ?></td>
                    <td>
                        <?php if (($copy['status_egzemplarza'] ?? '') === 'dostepny'): ?>
                            <?php if (isLoggedIn()): ?>
                                <form method="POST" action="<?= e(url('/reservations/create')) ?>">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id_egzemplarz" value="<?= e($copy['id_egzemplarz']) ?>">

                                    <button type="submit">Zarezerwuj</button>
                                </form>
                            <?php else: ?>
                                <a href="<?= e(url('/login')) ?>">Zaloguj się, aby zarezerwować</a>
                            <?php endif; ?>
                        <?php else: ?>
                            Brak akcji
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p>
    <a href="<?= e(url('/books')) ?>">Wróć do katalogu</a>
</p>

<?php renderFooter(); ?>