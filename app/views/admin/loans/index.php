<?php renderHeader('Wypożyczenia'); ?>

<h2>Wypożyczenia</h2>

<?php if (isset($_GET['created']) && $_GET['created'] === '1'): ?>
    <p style="color: green;">Wypożyczenie zostało utworzone.</p>
<?php endif; ?>

<?php if (isset($_GET['returned']) && $_GET['returned'] === '1'): ?>
    <p style="color: green;">Zwrot został oznaczony.</p>
<?php endif; ?>

<?php if (isset($_GET['return_error']) && $_GET['return_error'] === '1'): ?>
    <p style="color: red;">Nie udało się oznaczyć zwrotu.</p>
<?php endif; ?>

<p>
    <a href="<?= e(url('/admin/loans/create')) ?>">Dodaj wypożyczenie</a>
</p>

<?php if (empty($loans)): ?>
    <p>Brak wypożyczeń.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Książka</th>
                <th>Egzemplarz</th>
                <th>Forma</th>
                <th>Czytelnik</th>
                <th>Email</th>
                <th>Data wypożyczenia</th>
                <th>Przewidywany zwrot</th>
                <th>Rzeczywisty zwrot</th>
                <th>Status egzemplarza</th>
                <th>Akcja</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($loans as $loan): ?>
                <tr>
                    <td><?= e($loan['id_wypozyczenie']) ?></td>
                    <td><?= e($loan['tytul']) ?></td>
                    <td><?= e($loan['id_egzemplarz']) ?></td>
                    <td><?= e($loan['forma']) ?></td>
                    <td><?= e(($loan['imie'] ?? '') . ' ' . ($loan['nazwisko'] ?? '')) ?></td>
                    <td><?= e($loan['email']) ?></td>
                    <td><?= e($loan['data_wypozyczenie']) ?></td>
                    <td><?= e($loan['przewidywana_data_zwrot']) ?></td>
                    <td>
                        <?php if (empty($loan['rzeczywista_data_zwrot'])): ?>
                            Nie zwrócono
                        <?php else: ?>
                            <?= e($loan['rzeczywista_data_zwrot']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= e($loan['status_egzemplarza']) ?></td>
                    <td>
                        <?php if (empty($loan['rzeczywista_data_zwrot'])): ?>
                            <form method="POST" action="<?= e(url('/admin/loans/return')) ?>">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id_wypozyczenie" value="<?= e($loan['id_wypozyczenie']) ?>">

                                <button type="submit">Oznacz zwrot</button>
                            </form>
                        <?php else: ?>
                            Zwrócone
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p>
    <a href="<?= e(url('/admin')) ?>">Wróć do panelu admina</a>
</p>

<?php renderFooter(); ?>