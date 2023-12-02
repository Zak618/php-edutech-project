<?php
include_once "./base/header.php";
?>


<main>
    <h2 align="center" style="margin-top: 50px;">Войти в EduTech</h2>
    <h6 align="center" style="margin-top: 20px;">Получите доступ к курсам уже сейчас.</h6>
    <form class="row g-3" style="width: 50%; margin-left: auto; margin-right: auto; margin-top: 20px;" action="./database/login_db.php" method="POST">


        <div>
            <input type="email" class="form-control" placeholder="E-mail" aria-label="Email" name="email">
        </div>


        <div>
            <input type="password" class="form-control" placeholder="Пароль" aria-label="Пароль" name="password">
        </div>

        <a align="center" href="./registration.php" class="link-secondary">Нажмите, если еще не зарегистрировались</a>

        <button type="submit" class="btn btn-primary" style="width: 30%; margin-left: auto; margin-right: auto; border-radius: 10px;">Войти</button>
    </form>
</main>

<?php
include_once "./base/footer.php";
?>