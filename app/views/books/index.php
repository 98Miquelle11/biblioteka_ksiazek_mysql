<?php renderHeader('Katalog książek'); ?>

<h2>Katalog książek</h2>

<form method="GET" action="<?= e(url('/books')) ?>">
    <div>
        <label for="q">Szukaj po tytule albo autorze</label><br>
        <input
            type="text"
            id="q"
            name="q"
            value="<?= e($filters['q'] ?? '') ?>"
            placeholder="np. lalka, prus"
        >
    </div>

    <div>
        <label for="gatunek">Gatunek</label><br>
        <select id="gatunek" name="gatunek">
            <option value="">Wszystkie</option>

            <?php foreach ($genres as $genre): ?>
                <option
                    value="<?= e($genre['id_gatunek']) ?>"
                    <?php if ((string) ($filters['gatunek'] ?? '') === (string) $genre['id_gatunek']): ?>
                        selected
                    <?php endif; ?>
                >
                    <?= e($genre['nazwa']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="forma">Forma egzemplarza</label><br>
        <select id="forma" name="forma">
            <option value="">Wszystkie</option>

            <?php foreach (['fizyczna', 'ebook', 'audiobook'] as $forma): ?>
                <option
                    value="<?= e($forma) ?>"
                    <?php if (($filters['forma'] ?? '') === $forma): ?>
                        selected
                    <?php endif; ?>
                >
                    <?= e($forma) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="rok">Rok wydania</label><br>
        <select id="rok" name="rok">
            <option value="">Wszystkie</option>

            <?php foreach ($years as $year): ?>
                <option
                    value="<?= e($year['rok_wydania']) ?>"
                    <?php if ((string) ($filters['rok'] ?? '') === (string) $year['rok_wydania']): ?>
                        selected
                    <?php endif; ?>
                >
                    <?= e($year['rok_wydania']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <br>

    <button type="submit">Szukaj</button>
    <a href="<?= e(url('/books')) ?>">Wyczyść filtry</a>
</form>

<hr>

<p>Liczba znalezionych egzemplarzy: <?= e($totalBooks) ?></p>

<?php if (empty($books)): ?>
    <p>Brak wyników dla podanych filtrów.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Tytuł</th>
                <th>Autor</th>
                <th>Gatunek</th>
                <th>Wydawnictwo</th>
                <th>Rok wydania</th>
                <th>Forma</th>
                <th>Status</th>
                <th>Szczegóły</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= e($book['tytul']) ?></td>
                    <td><?= e($book['autorzy'] ?? 'Brak danych') ?></td>
                    <td><?= e($book['gatunki'] ?? 'Brak danych') ?></td>
                    <td><?= e($book['wydawnictwo']) ?></td>
                    <td><?= e($book['rok_wydania']) ?></td>
                    <td><?= e($book['forma']) ?></td>
                    <td><?= e($book['status_egzemplarza']) ?></td>
                    <td>
                        <a href="<?= e(url('/books/show') . '?id=' . $book['id_ksiazka']) ?>">
                            Zobacz
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if ($totalPages > 1): ?>
    <nav>
        <p>Strony:</p>

        <?php $queryParams = $_GET; ?>

        <?php if ($page > 1): ?>
            <?php $queryParams['page'] = $page - 1; ?>
            <a href="<?= e(url('/books') . '?' . http_build_query($queryParams)) ?>">Poprzednia</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php $queryParams['page'] = $i; ?>

            <?php if ($i === $page): ?>
                <strong><?= e($i) ?></strong>
            <?php else: ?>
                <a href="<?= e(url('/books') . '?' . http_build_query($queryParams)) ?>"><?= e($i) ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <?php $queryParams['page'] = $page + 1; ?>
            <a href="<?= e(url('/books') . '?' . http_build_query($queryParams)) ?>">Następna</a>
        <?php endif; ?>
    </nav>
<?php endif; ?>

<?php renderFooter(); ?>