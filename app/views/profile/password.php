<?php renderHeader('Zmiana hasła'); ?>

<h2>Zmiana hasła</h2>

<?php if (!empty($changed)): ?>
    <p style="color: green;">Hasło zostało zmienione.</p>
<?php endif; ?>

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

<form method="POST" action="<?= e(url('/profile/password')) ?>">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div>
        <label for="current_password">Obecne hasło</label><br>
        <input
            type="password"
            id="current_password"
            name="current_password"
            required
        >
    </div>

    <div>
        <label for="new_password">Nowe hasło</label><br>
        <input
            type="password"
            id="new_password"
            name="new_password"
            minlength="8"
            required
        >
    </div>

    <div>
        <label for="new_password_confirm">Powtórz nowe hasło</label><br>
        <input
            type="password"
            id="new_password_confirm"
            name="new_password_confirm"
            minlength="8"
            required
        >
    </div>

    <button type="submit">Zmień hasło</button>
</form>

<p>
    <a href="<?= e(url('/profile')) ?>">Wróć do profilu</a>
</p>

<?php renderFooter(); ?>