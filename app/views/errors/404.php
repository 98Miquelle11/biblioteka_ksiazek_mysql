<?php
renderHeader('404 - Nie znaleziono strony');
?>

<section class="error-page">
    <h2>404 - Nie znaleziono strony</h2>

    <p>Adres, który próbujesz otworzyć, nie istnieje.</p>

    <p>
        <a href="<?= e(url('/')) ?>">Wróć na stronę główną</a>
    </p>
</section>

<?php
renderFooter();