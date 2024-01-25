<?php
session_start();
$role = null; // Инициализируем переменную $role

if (isset($_SESSION['role'])) {
    // Пользователь авторизован
    $role = $_SESSION['role'];
    $name = $_SESSION['name'];
    $email = $_SESSION['email'];
    $female = $_SESSION['female'];
    $image = $_SESSION['image'];
    $password = $_SESSION['password'];
    $image = $_SESSION['image'];
    $id = $_SESSION['id'];
    if ($role == 2) {
        $teacher_id = $id;
    }
    function logout()
    {
        session_destroy();

        header("Location: ../../../diploma-project/php/start.php");
        exit();
    }

    // Проверка, был ли выполнен запрос на выход
    if (isset($_GET['logout'])) {
        logout();
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTech</title>
    <link rel="stylesheet" href="../css/start.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>


<body>
    <div class="header">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="../../../diploma-project/php/catalog.php">EduTech</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="../../../diploma-project/php/catalog.php">Каталог</a>
                        </li>
                        <?php if ($role == 1) { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../../../diploma-project/php/my_favourate_course.php">Моё обучение</a>
                            </li>
                        <?php } else if ($role == 2) { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../../../diploma-project/php/my_work_courses.php">Преподавание</a>
                            </li>
                        <?php } ?>

                    </ul>
                    
                        <form class="d-flex" role="search" method="GET" action="../php/search_results.php">
                            <input class="form-control me-2" type="search" placeholder="Найти курс" aria-label="Search" name="search">
                            <button class="btn btn-outline-success" type="submit">Поиск</button>
                        </form>
                        <form class="d-flex" role="search">
                        <?php if ($role == 1 || $role == 2) { ?>
                            <div class="dropdown">
                                <a class="btn btn-outline-success dropdown-toggle" role="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin-left: 5px;">
                                    <?php echo $name; ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="../../../diploma-project/php/profile.php">Профиль</a>
                                    <a class="dropdown-item" href="../../../diploma-project/php/settings_profile.php">Настройки</a>
                                    <a class="dropdown-item" href="?logout=true">Выйти</a>
                                </ul>
                            </div>
                        <?php } else { ?>
                            <a class="btn btn btn-dark" style="margin-left: 10px;" href="../../../diploma-project/php/login.php">Войти</a>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </nav>
    </div>