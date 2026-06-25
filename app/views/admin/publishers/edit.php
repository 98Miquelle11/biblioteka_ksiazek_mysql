<?php

declare(strict_types=1);

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edytuj wydawnictwo</title>
    <link rel="stylesheet" href="/biblioteka_ksiazek_mysql/public/css/style.css">
</head>
<body>
    <main>
        <h1>Edytuj wydawnictwo</h1>

        <p>
            <a href="/biblioteka_ksiazek_mysql/public/admin/publishers">Powrót do listy wydawnictw</a>
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

        <form method="post" action="/biblioteka_ksiazek_mysql/public/admin/publishers/edit?id=<?= e((string)$publisher['id_wydawnictwo']) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <div>
                <label for="nazwa">Nazwa:</label><br>
                <input
                    type="text"
                    id="nazwa"
                    name="nazwa"
                    value="<?= e($old['nazwa'] ?? '') ?>"
                    required
                >
            </div>

            <br>

            <div>
                <label for="siedziba">Siedziba:</label><br>
                <input
                    type="text"
                    id="siedziba"
                    name="siedziba"
                    value="<?= e($old['siedziba'] ?? '') ?>"
                >
            </div>

            <br>

            <button type="submit">Zapisz zmiany</button>
        </form>
    </main>
</body>
</html>