<?php
renderHeader('500 - Błąd aplikacji');
?>

<section class="error-page">
    <h2>500 - Błąd aplikacji</h2>

    <p>Wystąpił błąd po stronie aplikacji.</p>
    <p>Spróbuj ponownie później albo wróć na stronę główną.</p>

    <p>
        <a href="<?= e(url('/')) ?>">Wróć na stronę główną</a>
    </p>
</section>

<?php
renderFooter();