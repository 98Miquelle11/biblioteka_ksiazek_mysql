<?php

declare(strict_types=1);

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Autorzy - panel administratora</title>
    <link rel="stylesheet" href="/biblioteka_ksiazek_mysql/public/css/style.css">
</head>
<body>
    <main>
        <h1>Autorzy</h1>

        <p>
            <a href="/biblioteka_ksiazek_mysql/public/admin">Powrót do panelu admina</a>
        </p>

        <p>
            <a href="/biblioteka_ksiazek_mysql/public/admin/authors/create">Dodaj autora</a>
        </p>

        <?php if (!empty($_SESSION['flash_success'])): ?>
            <p style="color: green;">
                <?= e($_SESSION['flash_success']) ?>
            </p>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <p style="color: red;">
                <?= e($_SESSION['flash_error']) ?>
            </p>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <?php if (empty($authors)): ?>
            <p>Brak autorów w bazie.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Narodowość</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($authors as $author): ?>
                        <tr>
                            <td><?= e((string)$author['id_autor']) ?></td>
                            <td><?= e($author['imie']) ?></td>
                            <td><?= e($author['nazwisko']) ?></td>
                            <td><?= e($author['narodowosc'] ?? '') ?></td>
                            <td>
                                <a href="/biblioteka_ksiazek_mysql/public/admin/authors/edit?id=<?= e((string)$author['id_autor']) ?>">
                                    Edytuj
                                </a>

                                <form
                                    method="post"
                                    action="/biblioteka_ksiazek_mysql/public/admin/authors/delete"
                                    style="display: inline;"
                                    onsubmit="return confirm('Czy na pewno chcesz usunąć tego autora?');"
                                >
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id_autor" value="<?= e((string)$author['id_autor']) ?>">
                                    <button type="submit">Usuń</button>
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