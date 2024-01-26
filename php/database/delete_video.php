<?php
// Подключение к базе данных и другие необходимые настройки
include_once "./db.php";

// Проверка наличия POST-параметра material_id
if (isset($_POST['material_id'])) {
    $materialId = $_POST['material_id'];

    // Удаление видео из базы данных
    $deleteVideoSql = "DELETE FROM materials WHERE id = '$materialId' AND type = 'video'";
    if ($conn->query($deleteVideoSql) === TRUE) {
        $response = ['status' => 'success'];
    } else {
        $response = ['status' => 'error', 'message' => 'Ошибка удаления видео из базы данных: ' . $conn->error];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Не указан material_id для удаления видео.'];
}

// Возвращаем JSON-ответ
header('Content-Type: application/json');
echo json_encode($response);
?>
