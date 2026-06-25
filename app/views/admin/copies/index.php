<?php
$title = 'Egzemplarze';
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
        <h1>Egzemplarze</h1>

        <p>
            <a href="<?= e(url('/admin')) ?>">Powrót do panelu administratora</a>
        </p>

        <p>
            <a href="<?= e(url('/admin/copies/create')) ?>">Dodaj egzemplarz</a>
        </p>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= e($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= e($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (empty($copies)): ?>
            <p>Brak egzemplarzy w bazie.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Książka</th>
                        <th>Wydawnictwo</th>
                        <th>Rok</th>
                        <th>ISBN</th>
                        <th>Forma</th>
                        <th>Status</th>
                        <th>Cena zakupu</th>
                        <th>Uwaga</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($copies as $copy): ?>
                        <tr>
                            <td><?= e((string)$copy['id_egzemplarz']) ?></td>
                            <td><?= e($copy['tytul']) ?></td>
                            <td><?= e($copy['wydawnictwo']) ?></td>
                            <td><?= e((string)$copy['rok_wydania']) ?></td>
                            <td><?= e($copy['isbn']) ?></td>
                            <td><?= e($copy['forma']) ?></td>
                            <td><?= e($copy['status']) ?></td>
                            <td><?= e(number_format((float)$copy['cena_zakup'], 2, ',', ' ')) ?> zł</td>
                            <td><?= e($copy['uwaga'] ?? '') ?></td>
                            <td>
                                <a href="<?= e(url('/admin/copies/edit?id=' . $copy['id_egzemplarz'])) ?>">
                                    Edytuj
                                </a>

                                <form action="<?= e(url('/admin/copies/delete')) ?>" method="post" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id_egzemplarz" value="<?= e((string)$copy['id_egzemplarz']) ?>">

                                    <button type="submit" onclick="return confirm('Czy na pewno usunąć ten egzemplarz? Usuwaj tylko dane testowe.');">
                                        Usuń
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>