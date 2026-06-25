document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            clearValidationMessages(form);

            let isValid = validateForm(form);

            if (form.id === 'registerForm') {
                const isRegisterValid = validateRegisterForm(form);

                if (!isRegisterValid) {
                    isValid = false;
                }
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    });
});

function validateForm(form) {
    let isValid = true;

    const fields = form.querySelectorAll('input, select, textarea');

    fields.forEach(function (field) {
        if (field.type === 'hidden' || field.disabled) {
            return;
        }

        const value = field.value.trim();

        if (field.hasAttribute('required') && value === '') {
            showError(field, 'To pole jest wymagane.');
            isValid = false;
            return;
        }

        if (field.type === 'email' && value !== '' && !isValidEmail(value)) {
            showError(field, 'Podaj poprawny adres e-mail.');
            isValid = false;
            return;
        }

        if (field.type === 'number' && value !== '') {
            const numberValue = Number(value);

            if (Number.isNaN(numberValue)) {
                showError(field, 'Podaj poprawną liczbę.');
                isValid = false;
                return;
            }

            if (field.min !== '' && numberValue < Number(field.min)) {
                showError(field, 'Wartość jest za mała.');
                isValid = false;
                return;
            }

            if (field.max !== '' && numberValue > Number(field.max)) {
                showError(field, 'Wartość jest za duża.');
                isValid = false;
                return;
            }
        }

        if (field.maxLength > 0 && value.length > field.maxLength) {
            showError(field, 'Wpisano za dużo znaków.');
            isValid = false;
        }
    });

    return isValid;
}

function validateRegisterForm(form) {
    let isValid = true;

    const imie = document.getElementById('imie');
    const nazwisko = document.getElementById('nazwisko');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');

    if (imie && imie.value.trim() === '') {
        showError(imie, 'Imię jest wymagane.');
        isValid = false;
    }

    if (nazwisko && nazwisko.value.trim() === '') {
        showError(nazwisko, 'Nazwisko jest wymagane.');
        isValid = false;
    }

    if (email && email.value.trim() === '') {
        showError(email, 'Email jest wymagany.');
        isValid = false;
    } else if (email && !email.value.includes('@')) {
        showError(email, 'Email musi zawierać znak @.');
        isValid = false;
    }

    if (password && password.value.length < 8) {
        showError(password, 'Hasło musi mieć minimum 8 znaków.');
        isValid = false;
    }

    if (
        password &&
        passwordConfirm &&
        password.value !== passwordConfirm.value
    ) {
        showError(passwordConfirm, 'Hasła nie są takie same.');
        isValid = false;
    }

    return isValid;
}

function showError(field, message) {
    field.classList.add('is-invalid');

    const messageElement = document.createElement('div');
    messageElement.className = 'validation-message';
    messageElement.textContent = message;

    field.insertAdjacentElement('afterend', messageElement);
}

function clearValidationMessages(form) {
    const invalidFields = form.querySelectorAll('.is-invalid');
    const messages = form.querySelectorAll('.validation-message');

    invalidFields.forEach(function (field) {
        field.classList.remove('is-invalid');
    });

    messages.forEach(function (message) {
        message.remove();
    });
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}