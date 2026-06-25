<?php renderHeader('Profil użytkownika'); ?>

<h2>Profil użytkownika</h2>

<?php if (isset($_GET['updated']) && $_GET['updated'] === '1'): ?>
    <p style="color: green;">Dane profilu zostały zaktualizowane.</p>
<?php endif; ?>

<table border="1" cellpadding="8">
    <tr>
        <th>Imię</th>
        <td><?= e($user['imie']) ?></td>
    </tr>
    <tr>
        <th>Nazwisko</th>
        <td><?= e($user['nazwisko']) ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?= e($user['email']) ?></td>
    </tr>
    <tr>
        <th>Telefon</th>
        <td><?= e($user['telefon']) ?></td>
    </tr>
    <tr>
        <th>Rola</th>
        <td><?= e($user['rola']) ?></td>
    </tr>
    <tr>
        <th>Saldo</th>
        <td><?= e($user['saldo']) ?> zł</td>
    </tr>
    <tr>
        <th>Data rejestracji</th>
        <td><?= e($user['data_rejestracja']) ?></td>
    </tr>
</table>

<p>
    <a href="<?= e(url('/profile/edit')) ?>">Edytuj profil</a>
</p>

<p>
    <a href="<?= e(url('/profile/password')) ?>">Zmień hasło</a>
</p>

<?php renderFooter(); ?>