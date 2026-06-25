document.addEventListener('DOMContentLoaded', function () {
    const registerForm = document.getElementById('registerForm');

    if (!registerForm) {
        return;
    }

    registerForm.addEventListener('submit', function (event) {
        const imie = document.getElementById('imie').value.trim();
        const nazwisko = document.getElementById('nazwisko').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm').value;

        let errors = [];

        if (imie === '') {
            errors.push('Imię jest wymagane.');
        }

        if (nazwisko === '') {
            errors.push('Nazwisko jest wymagane.');
        }

        if (email === '') {
            errors.push('Email jest wymagany.');
        }

        if (!email.includes('@')) {
            errors.push('Email musi zawierać znak @.');
        }

        if (password.length < 8) {
            errors.push('Hasło musi mieć minimum 8 znaków.');
        }

        if (password !== passwordConfirm) {
            errors.push('Hasła nie są takie same.');
        }

        if (errors.length > 0) {
            event.preventDefault();
            alert(errors.join('\n'));
        }
    });
});