<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ooops</title>
    <!-- Подключение Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Стиль для размещения изображения и текста */
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        /* Стиль для кнопки входа */
        .btn-login {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Изображение слева -->
        <img src="../image/start/oops.svg" alt="Error Image" style="max-width: 100%; height: auto;">

        <!-- Текст и кнопка справа -->
        <div class="ml-4">
            <h1>Кажется, Вы захотели попасть на другую страницу, но забыли войти в систему. Сделать это сейчас:</h1>
            <a href="login.php" class="btn btn-primary btn-login">Войти</a>
        </div>
    </div>

    <!-- Подключение Bootstrap JS (необходим для работы компонентов, если они используются) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
