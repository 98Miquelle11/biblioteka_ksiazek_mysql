<?php renderHeader('Logowanie'); ?>

<h2>Logowanie</h2>

<?php if (!empty($registered)): ?>
    <p style="color: green;">Konto zostało utworzone. Możesz się teraz zalogować.</p>
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

<form method="POST" action="<?= e(url('/login')) ?>" id="loginForm" novalidate>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div>
        <label for="email">Email</label><br>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= e($old['email'] ?? '') ?>"
            required
        >
    </div>

    <div>
        <label for="password">Hasło</label><br>
        <input
            type="password"
            id="password"
            name="password"
            required
        >
    </div>

    <button type="submit">Zaloguj</button>
</form>

<p>Nie masz konta? <a href="<?= e(url('/register')) ?>">Zarejestruj się</a></p>

<?php renderFooter(); ?>