<?php

declare(strict_types=1);

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Książki - panel administratora</title>
    <link rel="stylesheet" href="/biblioteka_ksiazek_mysql/public/css/style.css">
</head>
<body>
    <main>
        <h1>Książki</h1>

        <p>
            <a href="/biblioteka_ksiazek_mysql/public/admin">Powrót do panelu admina</a>
        </p>

        <p>
            <a href="/biblioteka_ksiazek_mysql/public/admin/books/create">Dodaj książkę</a>
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

        <?php if (empty($books)): ?>
            <p>Brak książek w bazie.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tytuł</th>
                        <th>Autorzy</th>
                        <th>Gatunki</th>
                        <th>Wydawnictwo</th>
                        <th>ISBN</th>
                        <th>Rok</th>
                        <th>Liczba stron</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?= e((string)$book['id_ksiazka']) ?></td>
                            <td><?= e($book['tytul']) ?></td>
                            <td><?= e($book['autorzy'] ?? '') ?></td>
                            <td><?= e($book['gatunki'] ?? '') ?></td>
                            <td><?= e($book['wydawnictwo'] ?? '') ?></td>
                            <td><?= e($book['isbn'] ?? '') ?></td>
                            <td><?= e((string)($book['rok_wydania'] ?? '')) ?></td>
                            <td><?= e((string)($book['liczba_stron'] ?? '')) ?></td>
                            <td>
                                <a href="/biblioteka_ksiazek_mysql/public/admin/books/edit?id=<?= e((string)$book['id_ksiazka']) ?>">
                                    Edytuj
                                </a>

                                <form
                                    method="post"
                                    action="/biblioteka_ksiazek_mysql/public/admin/books/delete"
                                    style="display: inline;"
                                    onsubmit="return confirm('Czy na pewno chcesz usunąć tę książkę? Usunięte zostaną też jej wydania i egzemplarze.');"
                                >
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id_ksiazka" value="<?= e((string)$book['id_ksiazka']) ?>">
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