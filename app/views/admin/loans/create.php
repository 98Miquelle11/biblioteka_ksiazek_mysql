<?php renderHeader('Dodaj wypożyczenie'); ?>

<h2>Dodaj wypożyczenie</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <p>Popraw błędy w formularzu:</p>

        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (empty($copies)): ?>
    <p>Brak dostępnych egzemplarzy do wypożyczenia.</p>
    <p>Żeby utworzyć wypożyczenie, przynajmniej jeden egzemplarz musi mieć status <strong>dostepny</strong>.</p>
<?php elseif (empty($readers)): ?>
    <p>Brak czytelników w bazie.</p>
<?php else: ?>
    <form method="POST" action="<?= e(url('/admin/loans/create')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div>
            <label for="id_egzemplarz">Egzemplarz</label><br>
            <select id="id_egzemplarz" name="id_egzemplarz" required>
                <option value="">Wybierz egzemplarz</option>

                <?php foreach ($copies as $copy): ?>
                    <option
                        value="<?= e($copy['id_egzemplarz']) ?>"
                        <?php if ((string) ($old['id_egzemplarz'] ?? '') === (string) $copy['id_egzemplarz']): ?>
                            selected
                        <?php endif; ?>
                    >
                        ID <?= e($copy['id_egzemplarz']) ?>
                        —
                        <?= e($copy['tytul']) ?>
                        —
                        <?= e($copy['forma']) ?>
                        —
                        <?= e($copy['wydawnictwo']) ?>
                        —
                        <?= e($copy['rok_wydania']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label for="id_czytelnik">Czytelnik</label><br>
            <select id="id_czytelnik" name="id_czytelnik" required>
                <option value="">Wybierz czytelnika</option>

                <?php foreach ($readers as $reader): ?>
                    <option
                        value="<?= e($reader['id_czytelnik']) ?>"
                        <?php if ((string) ($old['id_czytelnik'] ?? '') === (string) $reader['id_czytelnik']): ?>
                            selected
                        <?php endif; ?>
                    >
                        <?= e($reader['nazwisko']) ?>
                        <?= e($reader['imie']) ?>
                        —
                        <?= e($reader['email']) ?>
                        —
                        <?= e($reader['rola']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <br>

        <div>
            <label for="data_wypozyczenie">Data wypożyczenia</label><br>
            <input
                type="date"
                id="data_wypozyczenie"
                name="data_wypozyczenie"
                value="<?= e($old['data_wypozyczenie'] ?? date('Y-m-d')) ?>"
                required
            >
        </div>

        <br>

        <div>
            <label for="przewidywana_data_zwrot">Przewidywana data zwrotu</label><br>
            <input
                type="date"
                id="przewidywana_data_zwrot"
                name="przewidywana_data_zwrot"
                value="<?= e($old['przewidywana_data_zwrot'] ?? date('Y-m-d', strtotime('+14 days'))) ?>"
                required
            >
        </div>

        <br>

        <button type="submit">Utwórz wypożyczenie</button>
    </form>
<?php endif; ?>

<p>
    <a href="<?= e(url('/admin/loans')) ?>">Wróć do listy wypożyczeń</a>
</p>

<?php renderFooter(); ?>