<?php
include_once "../php/database/db.php";

// Запрос к базе данных для получения всех заявок
$query = "SELECT * FROM help";
$result = mysqli_query($conn, $query);

// Удаление курса
if (isset($_POST['delete_course'])) {
    $course_id_to_delete = $_POST['course_id_to_delete'];
    $sql_delete_course = "DELETE FROM courses WHERE id = $course_id_to_delete";
    mysqli_query($conn, $sql_delete_course);
    // Дополнительные действия, например, перенаправление на admin.php
    header('Location: admin.php');
    exit();
}

// Редактирование курса
if (isset($_POST['edit_course'])) {
    $course_id_to_edit = $_POST['course_id_to_edit'];
    $new_title = $_POST['new_title'];
    $new_description = $_POST['new_description'];
    $sql_edit_course = "UPDATE courses SET title = '$new_title', description = '$new_description' WHERE id = $course_id_to_edit";
    mysqli_query($conn, $sql_edit_course);
    // Дополнительные действия, например, перенаправление на admin.php
    header('Location: admin.php');
    exit();
}

// Удаление пользователя
if (isset($_POST['delete_user'])) {
    $user_id_to_delete = $_POST['user_id_to_delete'];
    $sql_delete_user = "DELETE FROM student WHERE id = $user_id_to_delete";
    mysqli_query($conn, $sql_delete_user);
    // Дополнительные действия, например, перенаправление на admin.php
    header('Location: admin.php');
    exit();
}

// Управление комментариями курса
if (isset($_POST['delete_comment'])) {
    $comment_id_to_delete = $_POST['comment_id_to_delete'];
    $sql_delete_comment = "DELETE FROM reviews WHERE id = $comment_id_to_delete";
    mysqli_query($conn, $sql_delete_comment);
    // Дополнительные действия, например, перенаправление на admin.php
    header('Location: admin.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Подключение Bootstrap (замените ссылку на актуальную) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 20px;
        }

        .container {
            margin-top: 50px;
        }

        .jumbotron {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 10px;
        }

        .table-container {
            margin-top: 20px;
        }

        .table-container table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table-container th, .table-container td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .table-container th {
            background-color: #f2f2f2;
        }

        .btn-action {
            margin-right: 10px;
        }

        .btn-action:last-child {
            margin-right: 0;
        }

        .form-control {
            margin-bottom: 10px;
        }

        .btn-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="jumbotron">
            <h1 class="display-4">Добро пожаловать, Админ!</h1>
            <p class="lead">Это ваш личный кабинет администратора.</p>
        </div>

        <div class="table-container">
            <h2>Все заявки</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Сообщение</th>
                        <th>Дата создания</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Выводим данные из запроса
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td>{$row['message']}</td>";
                        echo "<td>{$row['created_at']}</td>";
                        echo "<td>
                                <form method='post'>
                                    <input type='hidden' name='comment_id_to_delete' value='{$row['id']}'>
                                    <button type='submit' class='btn btn-danger btn-action' name='delete_comment'>Удалить</button>
                                </form>
                            </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="container">
        <div class="btn-container">
            <form method="post">
                <input type="hidden" name="course_id_to_delete" value="{course_id}">
                <button type="submit" class="btn btn-danger btn-action" name="delete_course">Удалить курс</button>
            </form>

            <!-- Блок для редактирования курса -->
            <form method="post">
                <input type="hidden" name="course_id_to_edit" value="{course_id}">
                <input type="text" name="new_title" class="form-control" placeholder="Новое название курса" required>
                <textarea name="new_description" class="form-control" placeholder="Новое описание курса" required></textarea>
                <button type="submit" class="btn btn-primary btn-action" name="edit_course">Редактировать курс</button>
            </form>

            <!-- Блок для удаления пользователя -->
            <form method="post">
                <input type="hidden" name="user_id_to_delete" value="{user_id}">
                <button type="submit" class="btn btn-danger btn-action" name="delete_user">Удалить пользователя</button>
            </form>
        </div>
    </div>

    <!-- Подключение Bootstrap JS (замените ссылку на актуальную) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
