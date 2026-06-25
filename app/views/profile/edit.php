<?php renderHeader('Edycja profilu'); ?>

<h2>Edycja profilu</h2>

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

<form method="POST" action="<?= e(url('/profile/edit')) ?>">
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

    <button type="submit">Zapisz zmiany</button>
</form>

<p>
    <a href="<?= e(url('/profile')) ?>">Wróć do profilu</a>
</p>

<?php renderFooter(); ?>