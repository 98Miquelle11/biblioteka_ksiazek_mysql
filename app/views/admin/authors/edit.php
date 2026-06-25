<?php

declare(strict_types=1);

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edytuj autora</title>
    <link rel="stylesheet" href="/biblioteka_ksiazek_mysql/public/css/style.css">
</head>
<body>
    <main>
        <h1>Edytuj autora</h1>

        <p>
            <a href="/biblioteka_ksiazek_mysql/public/admin/authors">Powrót do listy autorów</a>
        </p>

        <?php if (!empty($errors)): ?>
            <div style="color: red;">
                <p>Popraw błędy:</p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/biblioteka_ksiazek_mysql/public/admin/authors/edit?id=<?= e((string)$author['id_autor']) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <div>
                <label for="imie">Imię:</label><br>
                <input
                    type="text"
                    id="imie"
                    name="imie"
                    value="<?= e($old['imie'] ?? '') ?>"
                    required
                >
            </div>

            <br>

            <div>
                <label for="nazwisko">Nazwisko:</label><br>
                <input
                    type="text"
                    id="nazwisko"
                    name="nazwisko"
                    value="<?= e($old['nazwisko'] ?? '') ?>"
                    required
                >
            </div>

            <br>

            <div>
                <label for="narodowosc">Narodowość:</label><br>
                <input
                    type="text"
                    id="narodowosc"
                    name="narodowosc"
                    value="<?= e($old['narodowosc'] ?? '') ?>"
                >
            </div>

            <br>

            <button type="submit">Zapisz zmiany</button>
        </form>
    </main>
</body>
</html>