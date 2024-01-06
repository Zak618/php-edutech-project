<?php
include_once '../database/db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['material_id'])) {
        $material_id = $_POST['material_id'];
        $deleteSql = "DELETE FROM materials WHERE id = '$material_id'";
        if ($conn->query($deleteSql) === TRUE) {
            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['error' => 'Ошибка при удалении теста: ' . $conn->error]);
        }
    } else {
        echo json_encode(['error' => 'Не указан material_id для удаления теста']);
    }
} else {
    echo json_encode(['error' => 'Недопустимый метод запроса']);
}
?>
