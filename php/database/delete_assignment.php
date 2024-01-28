<?php
include_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assignment_id = $_POST['assignment_id'];

    // Get file paths before deleting from the database
    $getFilesSql = "SELECT file_path FROM submitted_assignments WHERE assignment_id = ?";
    $getFilesStmt = $conn->prepare($getFilesSql);
    $getFilesStmt->bind_param("i", $assignment_id);
    $getFilesStmt->execute();
    $getFilesResult = $getFilesStmt->get_result();

    while ($fileRow = $getFilesResult->fetch_assoc()) {
        $filePath = $fileRow['file_path'];

        // Delete the file from the server
        if (unlink($filePath)) {
            echo "Файл успешно удален.";
        } else {
            echo "Ошибка при удалении файла.";
        }
    }

    // Delete the assignment record from the database
    $deleteAssignmentSql = "DELETE FROM assignments WHERE id = ?";
    $deleteAssignmentStmt = $conn->prepare($deleteAssignmentSql);
    $deleteAssignmentStmt->bind_param("i", $assignment_id);

    if ($deleteAssignmentStmt->execute()) {
        echo "Задание успешно удалено.";
    } else {
        echo "Ошибка при удалении задания из базы данных.";
    }
} else {
    echo "Недопустимый метод запроса.";
}
?>
