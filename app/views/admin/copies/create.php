<?php
$title = 'Dodaj egzemplarz';
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
        <h1>Dodaj egzemplarz</h1>

        <p>
            <a href="<?= e(url('/admin/copies')) ?>">Powrót do listy egzemplarzy</a>
        </p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= e(url('/admin/copies/create')) ?>" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <div>
                <label for="id_wydanie">Wydanie książki</label>
                <select id="id_wydanie" name="id_wydanie" required>
                    <option value="">-- wybierz wydanie --</option>

                    <?php foreach ($editions as $edition): ?>
                        <?php
                            $label = $edition['tytul']
                                . ' — ' . $edition['wydawnictwo']
                                . ' — ' . $edition['rok_wydania']
                                . ' — ISBN: ' . $edition['isbn'];
                        ?>
                        <option
                            value="<?= e((string)$edition['id_wydanie']) ?>"
                            <?= (int)$copy['id_wydanie'] === (int)$edition['id_wydanie'] ? 'selected' : '' ?>
                        >
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="forma">Forma</label>
                <select id="forma" name="forma" required>
                    <option value="fizyczna" <?= $copy['forma'] === 'fizyczna' ? 'selected' : '' ?>>fizyczna</option>
                    <option value="ebook" <?= $copy['forma'] === 'ebook' ? 'selected' : '' ?>>ebook</option>
                    <option value="audiobook" <?= $copy['forma'] === 'audiobook' ? 'selected' : '' ?>>audiobook</option>
                </select>
            </div>

            <div>
                <label for="id_status_egzemplarz">Status</label>
                <select id="id_status_egzemplarz" name="id_status_egzemplarz" required>
                    <option value="">-- wybierz status --</option>

                    <?php foreach ($statuses as $status): ?>
                        <option
                            value="<?= e((string)$status['id_status_egzemplarz']) ?>"
                            <?= (int)$copy['id_status_egzemplarz'] === (int)$status['id_status_egzemplarz'] ? 'selected' : '' ?>
                        >
                            <?= e($status['nazwa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="cena_zakup">Cena zakupu</label>
                <input
                    type="number"
                    id="cena_zakup"
                    name="cena_zakup"
                    step="0.01"
                    min="0"
                    value="<?= e((string)$copy['cena_zakup']) ?>"
                    required
                >
            </div>

            <div>
                <label for="uwaga">Uwaga</label>
                <textarea id="uwaga" name="uwaga" maxlength="255"><?= e($copy['uwaga']) ?></textarea>
            </div>

            <button type="submit">Dodaj egzemplarz</button>
        </form>
    </main>
</body>
</html>