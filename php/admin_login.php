<?php
session_start();
include_once "../php/database/db.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка учетных данных
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // SQL-запрос для извлечения данных из таблицы admin
    $sql = "SELECT * FROM admin WHERE name = '$input_username'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $admin_username = $row['name'];
        $admin_password = $row['password'];

        // Проверка верности учетных данных
        if ($input_username == $admin_username && $input_password == $admin_password) {
            // Верные учетные данные
            $_SESSION['admin'] = true;
            header('Location: admin.php');
            exit();
        } else {
            // Неверные учетные данные
            $error_message = 'Неверное имя пользователя или пароль';
        }
    } else {
        // Пользователь с указанным именем не найден
        $error_message = 'Неверное имя пользователя или пароль';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход для администратора</title>
    <!-- Подключение Bootstrap (замените ссылку на актуальную) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-container label {
            font-weight: bold;
        }

        .login-container button {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Вход для администратора</h2>

        <?php if (isset($error_message)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="username">Имя пользователя:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Войти</button>
        </form>
    </div>

    <!-- Подключение Bootstrap JS (замените ссылку на актуальную) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
