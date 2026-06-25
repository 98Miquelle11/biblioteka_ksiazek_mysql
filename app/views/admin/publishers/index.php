<?php

declare(strict_types=1);

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wydawnictwa - panel administratora</title>
    <link rel="stylesheet" href="/biblioteka_ksiazek_mysql/public/css/style.css">
</head>
<body>
    <main>
        <h1>Wydawnictwa</h1>

        <p>
            <a href="/biblioteka_ksiazek_mysql/public/admin">Powrót do panelu admina</a>
        </p>

        <p>
            <a href="/biblioteka_ksiazek_mysql/public/admin/publishers/create">Dodaj wydawnictwo</a>
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

        <?php if (empty($publishers)): ?>
            <p>Brak wydawnictw w bazie.</p>
        <?php else: ?>
            <table border="1" cellpadding="8" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nazwa</th>
                        <th>Siedziba</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($publishers as $publisher): ?>
                        <tr>
                            <td><?= e((string)$publisher['id_wydawnictwo']) ?></td>
                            <td><?= e($publisher['nazwa']) ?></td>
                            <td><?= e($publisher['siedziba'] ?? '') ?></td>
                            <td>
                                <a href="/biblioteka_ksiazek_mysql/public/admin/publishers/edit?id=<?= e((string)$publisher['id_wydawnictwo']) ?>">
                                    Edytuj
                                </a>

                                <form
                                    method="post"
                                    action="/biblioteka_ksiazek_mysql/public/admin/publishers/delete"
                                    style="display: inline;"
                                    onsubmit="return confirm('Czy na pewno chcesz usunąć to wydawnictwo?');"
                                >
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="id_wydawnictwo" value="<?= e((string)$publisher['id_wydawnictwo']) ?>">
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