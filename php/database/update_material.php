<?php
include_once "../database/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из AJAX-запроса
    $material_id = $_POST['material_id'];
    $new_content = $_POST['materialContent'];

    // Обновляем текст материала в базе данных
    $updateSql = "UPDATE materials SET content = '$new_content' WHERE id = '$material_id'";
    $updateResult = $conn->query($updateSql);

    if ($updateResult) {
        // Отправляем успешный статус
        echo json_encode(['status' => 'success']);
        exit;
    } else {
        // В случае ошибки отправляем сообщение об ошибке
        echo json_encode(['status' => 'error', 'message' => 'Произошла ошибка при обновлении материала.']);
        exit;
    }
} else {
    // В случае неверного типа запроса отправляем сообщение об ошибке
    echo json_encode(['status' => 'error', 'message' => 'Неверный тип запроса.']);
    exit;
}
?>
