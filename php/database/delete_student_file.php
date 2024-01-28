<?php
include_once "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $submission_id = $_POST['submission_id'];

    // Get file path before deleting from the database
    $getFileSql = "SELECT file_path FROM submitted_assignments WHERE id = ?";
    $getFileStmt = $conn->prepare($getFileSql);
    $getFileStmt->bind_param("i", $submission_id);
    $getFileStmt->execute();
    $getFileResult = $getFileStmt->get_result();

    if ($getFileResult->num_rows > 0) {
        $fileRow = $getFileResult->fetch_assoc();
        $filePath = $fileRow['file_path'];

        // Delete the record from submitted_assignments
        $deleteSubmissionSql = "DELETE FROM submitted_assignments WHERE id = ?";
        $deleteSubmissionStmt = $conn->prepare($deleteSubmissionSql);
        $deleteSubmissionStmt->bind_param("i", $submission_id);

        if ($deleteSubmissionStmt->execute()) {
            // Delete the file from the server
            if (unlink($filePath)) {
                echo "Файл успешно удален.";
            } else {
                echo "Ошибка при удалении файла.";
            }
        } else {
            echo "Ошибка при удалении записи из базы данных.";
        }
    } else {
        echo "Файл не найден.";
    }
} else {
    echo "Недопустимый метод запроса.";
}

?>
