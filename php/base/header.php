<?php
session_start();
$role = null;

if (isset($_SESSION['role'])) {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
    /* Добавьте стили для уведомлений */
.notification-item {
    padding: 10px;
    margin-bottom: 10px;
    background-color: #f8d7da; /* Цвет фона для уведомлений об ошибке (можете изменить под свой дизайн) */
    color: #721c24; /* Цвет текста для уведомлений об ошибке (можете изменить под свой дизайн) */
    border: 1px solid #f5c6cb; /* Цвет границы для уведомлений об ошибке (можете изменить под свой дизайн) */
    border-radius: 5px;
}

/* Стили для модального окна с уведомлениями */
#notificationsModalBody {
    max-height: 300px; /* Максимальная высота модального окна с уведомлениями (можете изменить под свой дизайн) */
    overflow-y: auto; /* Добавление прокрутки, если уведомлений больше, чем можно отобразить */
}

/* Добавьте стиль для иконки уведомлений в панели навигации */
#notifications-icon.text-danger {
    color: #dc3545; /* Цвет иконки уведомлений (можете изменить под свой дизайн) */
}

</style>
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

                        <!-- Добавим иконку уведомлений для преподавателя -->
                        <?php if ($role == 2) { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#" id="notifications-icon" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                                    <i class="fas fa-bell"></i>
                                </a>
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


    <script>
    document.addEventListener('DOMContentLoaded', function () {
    // Получение уведомлений с сервера
    function getNotifications() {
        return fetch('../../../diploma-project/php/notifications.php?teacher_id=<?php echo $teacher_id; ?>')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            });
    }

    // Функция для отображения уведомлений
    function displayNotifications(notifications) {
        var notificationsModalBody = document.getElementById('notificationsModalBody');
        notificationsModalBody.innerHTML = '';

        if (notifications.length > 0) {
            notifications.forEach(notification => {
                var notificationItem = document.createElement('div');
                notificationItem.className = 'notification-item';

                // Преобразуйте timestamp в объект Date и затем отформатируйте его
                var date = new Date(notification.creation_time * 1000); // умножьте на 1000, так как JavaScript использует миллисекунды
                var formattedDate = date.toLocaleString();

                notificationItem.innerHTML = `
                    <p>${notification.message}</p>
                    <small>${formattedDate}</small>
                `;
                notificationsModalBody.appendChild(notificationItem);
            });

            $('#notificationsModal').modal('show');
        }
    }


    // Проверка наличия уведомлений и отображение иконки
    getNotifications()
        .then(notifications => {
            console.log('Fetched notifications:', notifications); // Log the fetched notifications for debugging

            if (notifications && Array.isArray(notifications)) {
                document.getElementById('notifications-icon').classList.add('text-danger');
                document.getElementById('notifications-icon').addEventListener('click', function () {
                    displayNotifications(notifications);
                });
            } else {
                console.error('Invalid response format or no notifications found.');
            }
        })
        .catch(error => {
            console.error('Error fetching or processing notifications:', error.message);
        });
});


</script>


    <!-- Модальное окно для уведомлений -->
    <div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationsModalLabel">Уведомления</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="notificationsModalBody">
                    <!-- Здесь отобразятся уведомления -->
                </div>
            </div>
        </div>
    </div>
</body>

</html>
