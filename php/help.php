<?php
include_once "./base/header.php";
include_once "../php/database/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Обработка данных формы
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Сохранение данных в базу данных
    $query = "INSERT INTO help (name, email, message) VALUES ('$name', '$email', '$message')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo '<div class="alert alert-success" role="alert">Ваш запрос успешно отправлен!</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Ошибка при сохранении данных в базу данных.</div>';
    }
}
?>

<style>
    .f {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form {
        height: 70vh;
        background-color: #f8f9fa;
    }

    .card {
        width: 100%;
        max-width: 400px;
    }
</style>

<div class="f">
    <div class="card mt-5 form">
        <div class="card-header bg-primary text-white text-center">EDU HELP</div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label for="name">Ваше имя:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Ваш Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Сообщение:</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-3 btn-block">Отправить запрос</button>
            </form>
        </div>
    </div>
</div>

<?php
include_once "./base/footer.php";
?>
