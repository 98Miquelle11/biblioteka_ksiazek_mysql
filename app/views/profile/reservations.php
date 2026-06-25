<?php
$title = 'Moje rezerwacje';
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
        <h1>Moje rezerwacje</h1>

        <p>
            <a href="<?= e(url('/profile')) ?>">Powrót do profilu</a>
        </p>

        <?php if (empty($reservations)): ?>
            <p>Nie masz jeszcze żadnych rezerwacji.</p>
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
                        <th>Data rezerwacji</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <?php
                            $reservationDate = $reservation['data_rezerwacji']
                                ?? $reservation['data_rezerwacja']
                                ?? '';

                            $status = $reservation['status_rezerwacja']
                                ?? $reservation['status']
                                ?? '';
                        ?>

                        <tr>
                            <td><?= e((string)$reservation['id_rezerwacja']) ?></td>
                            <td><?= e($reservation['tytul']) ?></td>
                            <td><?= e($reservation['wydawnictwo']) ?></td>
                            <td><?= e((string)$reservation['rok_wydania']) ?></td>
                            <td><?= e($reservation['isbn']) ?></td>
                            <td><?= e($reservation['forma']) ?></td>
                            <td><?= e((string)$reservationDate) ?></td>
                            <td><?= e((string)$status) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>