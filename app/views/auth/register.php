<?php renderHeader('Rejestracja'); ?>

<h2>Rejestracja</h2>

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

<form method="POST" action="<?= e(url('/register')) ?>" id="registerForm" novalidate>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div>
        <label for="imie">Imię</label><br>
        <input
            type="text"
            id="imie"
            name="imie"
            value="<?= e($old['imie'] ?? '') ?>"
            required
        >
    </div>

    <div>
        <label for="nazwisko">Nazwisko</label><br>
        <input
            type="text"
            id="nazwisko"
            name="nazwisko"
            value="<?= e($old['nazwisko'] ?? '') ?>"
            required
        >
    </div>

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
        <label for="telefon">Telefon</label><br>
        <input
            type="text"
            id="telefon"
            name="telefon"
            value="<?= e($old['telefon'] ?? '') ?>"
        >
    </div>

    <div>
        <label for="password">Hasło</label><br>
        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            required
        >
    </div>

    <div>
        <label for="password_confirm">Powtórz hasło</label><br>
        <input
            type="password"
            id="password_confirm"
            name="password_confirm"
            minlength="8"
            required
        >
    </div>

    <button type="submit">Zarejestruj</button>
</form>

<p>Masz już konto? <a href="<?= e(url('/login')) ?>">Przejdź do logowania</a></p>

<?php renderFooter(); ?>