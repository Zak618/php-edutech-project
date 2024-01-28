<?php
session_start();
include './database/db.php';

// Проверка авторизации и роли пользователя
if (isset($_SESSION['role']) && $_SESSION['role'] == 2) {
    $teacher_id = $_SESSION['id'];

    // Запрос для получения уведомлений
    $sql = "SELECT id, message, creation_time FROM notifications WHERE user_id = $teacher_id ORDER BY creation_time DESC";
    $result = $conn->query($sql);

    $notifications = array();

    if ($result->num_rows > 0) {
        // Преобразуем результат в массив уведомлений
        while ($row = $result->fetch_assoc()) {
            $notifications[] = array(
                'id' => $row['id'],
                'message' => $row['message'],
                'creation_time' => strtotime($row['creation_time'])
            );
        }
    }

    // Возвращаем уведомления в формате JSON
    echo json_encode($notifications, JSON_UNESCAPED_UNICODE);
} else {
    // Если пользователь не авторизован или не является преподавателем
    echo json_encode(array()); // Пустой массив, если уведомлений нет или пользователь не является преподавателем
}

$conn->close();
?>
