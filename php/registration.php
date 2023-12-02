<?php
include_once "./base/header.php";
?>


<main>
    <h2 align="center" style="margin-top: 50px;">Создайте свой EduTech ID</h2>
    <h6 align="center" style="margin-top: 20px;">Получите доступ к курсам уже сейчас.</h6>
    <form class="row g-3" style="width: 50%; margin-left: auto; margin-right: auto; margin-top: 20px;" action="./database/register_db.php" method="POST">
        <div class="col">
            <input type="text" class="form-control" placeholder="Имя" aria-label="Имя" name="name">
        </div>
        <div class="col">
            <input type="text" class="form-control" placeholder="Фамилия" aria-label="Фамилия" name="female">
        </div>

        <div>
            <input type="email" class="form-control" placeholder="E-mail" aria-label="Email" name="email">
        </div>
        <div>
            <input type="tel" class="form-control" placeholder="Номер телефона" aria-label="Телефон" name="phone">
        </div>

        
        <select class="form-select" style="width: 97.7%; margin-left: auto; margin-right: auto;" name="role">
            <option value="1" class="form-control">Ученик</option>
            <option value="2" class="form-control">Преподаватель</option>
        </select>

        <div>
            <input type="password" class="form-control" placeholder="Пароль" aria-label="Пароль" name="password">
        </div>
        <div>
            <input type="password" class="form-control" placeholder="Повторите пароль" aria-label="Пароль">
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

<?php
include_once "./base/footer.php";
?>