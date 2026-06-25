<?php

declare(strict_types=1);

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edytuj książkę</title>
    <link rel="stylesheet" href="/biblioteka_ksiazek_mysql/public/css/style.css">
</head>
<body>
    <main>
        <h1>Edytuj książkę</h1>

        <p>
            <a href="/biblioteka_ksiazek_mysql/public/admin/books">Powrót do listy książek</a>
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

        <form method="post" action="/biblioteka_ksiazek_mysql/public/admin/books/edit?id=<?= e((string)$book['id_ksiazka']) ?>">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <div>
                <label for="tytul">Tytuł:</label><br>
                <input
                    type="text"
                    id="tytul"
                    name="tytul"
                    value="<?= e($old['tytul'] ?? '') ?>"
                    required
                >
            </div>

            <br>

            <div>
                <label for="id_autor">Autor:</label><br>
                <select id="id_autor" name="id_autor" required>
                    <option value="">-- wybierz autora --</option>
                    <?php foreach ($authors as $author): ?>
                        <option
                            value="<?= e((string)$author['id_autor']) ?>"
                            <?= (string)$author['id_autor'] === (string)($old['id_autor'] ?? '') ? 'selected' : '' ?>
                        >
                            <?= e($author['imie'] . ' ' . $author['nazwisko']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <br>

            <div>
                <label for="id_gatunek">Gatunek:</label><br>
                <select id="id_gatunek" name="id_gatunek" required>
                    <option value="">-- wybierz gatunek --</option>
                    <?php foreach ($genres as $genre): ?>
                        <option
                            value="<?= e((string)$genre['id_gatunek']) ?>"
                            <?= (string)$genre['id_gatunek'] === (string)($old['id_gatunek'] ?? '') ? 'selected' : '' ?>
                        >
                            <?= e($genre['nazwa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <br>

            <div>
                <label for="id_wydawnictwo">Wydawnictwo:</label><br>
                <select id="id_wydawnictwo" name="id_wydawnictwo">
                    <option value="">-- brak / nieznane --</option>
                    <?php foreach ($publishers as $publisher): ?>
                        <option
                            value="<?= e((string)$publisher['id_wydawnictwo']) ?>"
                            <?= (string)$publisher['id_wydawnictwo'] === (string)($old['id_wydawnictwo'] ?? '') ? 'selected' : '' ?>
                        >
                            <?= e($publisher['nazwa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <br>

            <div>
                <label for="isbn">ISBN:</label><br>
                <input
                    type="text"
                    id="isbn"
                    name="isbn"
                    maxlength="13"
                    value="<?= e($old['isbn'] ?? '') ?>"
                >
            </div>

            <br>

            <div>
                <label for="rok_wydania">Rok wydania:</label><br>
                <input
                    type="number"
                    id="rok_wydania"
                    name="rok_wydania"
                    min="1000"
                    max="2100"
                    value="<?= e($old['rok_wydania'] ?? '') ?>"
                >
            </div>

            <br>

            <div>
                <label for="liczba_stron">Liczba stron:</label><br>
                <input
                    type="number"
                    id="liczba_stron"
                    name="liczba_stron"
                    min="1"
                    value="<?= e($old['liczba_stron'] ?? '') ?>"
                >
            </div>

            <br>

            <button type="submit">Zapisz zmiany</button>
        </form>
    </main>
</body>
</html>