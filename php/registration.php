<?php
include_once "./base/header.php";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<main>
<h2 align="center" style="margin-top: 50px;">Создайте свой EduTech ID</h2>
    <h6 align="center" style="margin-top: 20px;">Получите доступ к курсам уже сейчас.</h6>
    <form id="registrationForm" class="row g-3" style="width: 50%; margin-left: auto; margin-right: auto; margin-top: 20px;" action="./database/register_db.php" method="POST">
        <div class="col">
            <input type="text" class="form-control" placeholder="Имя" aria-label="Имя" name="name">
            <div class="error" style="color: red;"></div>
        </div>
        <div class="col">
            <input type="text" class="form-control" placeholder="Фамилия" aria-label="Фамилия" name="female">
            <div class="error" style="color: red;"></div>
        </div>
        <div>
            <input type="email" class="form-control" placeholder="E-mail" aria-label="Email" name="email">
            <div class="error" style="color: red;"></div>
        </div>
        <div>
            <input type="tel" class="form-control" placeholder="Номер телефона" aria-label="Телефон" name="phone" id="phone">
            <div class="error" style="color: red;"></div>
        </div>
        <select class="form-select" style="width: 97.7%; margin-left: auto; margin-right: auto;" name="role">
            <option value="1" class="form-control">Ученик</option>
            <option value="2" class="form-control">Преподаватель</option>
        </select>
        <div>
            <input type="password" class="form-control" placeholder="Пароль" aria-label="Пароль" name="password">
            <div class="error" style="color: red;"></div>
        </div>
        <div>
            <input type="password" class="form-control" placeholder="Повторите пароль" aria-label="Пароль" name="confirm_password">
            <div class="error" style="color: red;"></div>
        </div>
        <div style="display: flex; justify-content: center;">
            <input class="form-check-input" type="checkbox" style="margin-right: 5px;">
            <label class="form-check-label">
                Я согласен на обработку <a href="#">персональных данных</a>
            </label>
        </div>
        <button type="submit" class="btn btn-primary" style="width: 30%; margin-left: auto; margin-right: auto; border-radius: 10px;">Зарегистрироваться</button>
    </form>
</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('registrationForm');
    var inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="tel"]');
    var password = form.querySelector('input[name="password"]');
    var confirmPassword = form.querySelector('input[name="confirm_password"]');
    var phoneInput = document.querySelector('#phone');
    var iti = window.intlTelInput(phoneInput, {
        initialCountry: "auto",
        geoIpLookup: function(callback) {
            fetch('https://ipinfo.io/json?token=<YOUR_TOKEN_HERE>')
                .then(response => response.json())
                .then(data => callback(data.country))
                .catch(() => callback('us'));
        },
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    });

    function validateInput(input) {
        var errorDiv = input.nextElementSibling;
        if (input.name === 'name' || input.name === 'female') {
            if (/\d/.test(input.value)) {
                input.style.borderColor = 'red';
                errorDiv.textContent = 'Имя и фамилия не должны содержать цифры.';
            } else {
                input.style.borderColor = '';
                errorDiv.textContent = '';
            }
        }
        if (input === password || input === confirmPassword) {
            if (password.value !== confirmPassword.value) {
                password.style.borderColor = 'red';
                password.nextElementSibling.textContent = 'Пароли не совпадают.';
                confirmPassword.style.borderColor = 'red';
                confirmPassword.nextElementSibling.textContent = 'Пароли не совпадают.';
            } else {
                password.style.borderColor = '';
                password.nextElementSibling.textContent = '';
                confirmPassword.style.borderColor = '';
                confirmPassword.nextElementSibling.textContent = '';
            }
        }
    }

    inputs.forEach(function(input) {
        input.addEventListener('input', function() {
            validateInput(input);
        });
    });

    form.addEventListener('submit', function(event) {
        inputs.forEach(function(input) {
            validateInput(input);
        });
        if (Array.from(inputs).some(input => input.style.borderColor === 'red')) {
            event.preventDefault(); // Остановить отправку формы, если есть ошибки
        }
    });
});
</script>
<style>
    .iti {
        width: 100%;
    }
    .error {
        font-size: 0.8em;
    }
</style>

<?php
include_once "./base/footer.php";
?>
