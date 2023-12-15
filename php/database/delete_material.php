<?php
include_once "../database/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем ID материала из AJAX-запроса
    $material_id = $_POST['material_id'];

    // Удаляем материал из базы данных
    $deleteSql = "DELETE FROM materials WHERE id = '$material_id'";
    $deleteResult = $conn->query($deleteSql);

    if ($deleteResult) {
        // Отправляем успешный статус
        echo json_encode(['status' => 'success']);
    } else {
        // В случае ошибки отправляем сообщение об ошибке
        echo json_encode(['status' => 'error', 'message' => 'Произошла ошибка при удалении материала.']);
    }
} else {
    // В случае неверного типа запроса отправляем сообщение об ошибке
    echo json_encode(['status' => 'error', 'message' => 'Неверный тип запроса.']);
}
?>
