<?php
$title = 'Moje wypożyczenia';
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
        <h1>Moje wypożyczenia</h1>

        <p>
            <a href="<?= e(url('/profile')) ?>">Powrót do profilu</a>
        </p>

        <?php if (empty($loans)): ?>
            <p>Nie masz jeszcze żadnych wypożyczeń.</p>
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
                        <th>Data wypożyczenia</th>
                        <th>Planowany zwrot</th>
                        <th>Rzeczywisty zwrot</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan): ?>
                        <?php
                            $loanDate = $loan['data_wypozyczenia']
                                ?? $loan['data_wypozyczenie']
                                ?? '';

                            $plannedReturnDate = $loan['planowana_data_zwrot']
                                ?? $loan['planowana_data_zwrotu']
                                ?? $loan['termin_zwrotu']
                                ?? '';

                            $realReturnDate = $loan['rzeczywista_data_zwrot']
                                ?? $loan['rzeczywista_data_zwrotu']
                                ?? '';

                            $status = empty($realReturnDate) ? 'aktywne' : 'zwrócone';
                        ?>

                        <tr>
                            <td><?= e((string)$loan['id_wypozyczenie']) ?></td>
                            <td><?= e($loan['tytul']) ?></td>
                            <td><?= e($loan['wydawnictwo']) ?></td>
                            <td><?= e((string)$loan['rok_wydania']) ?></td>
                            <td><?= e($loan['isbn']) ?></td>
                            <td><?= e($loan['forma']) ?></td>
                            <td><?= e((string)$loanDate) ?></td>
                            <td><?= e((string)$plannedReturnDate) ?></td>
                            <td><?= e((string)$realReturnDate) ?></td>
                            <td><?= e($status) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>