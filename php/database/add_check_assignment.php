<?php
include_once "db.php";
include_once "../base/header.php";
$student_id = $id;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assignment_id = $_POST['assignment_id'];

    // Получаем информацию о задании
    $assignmentSql = "SELECT * FROM assignments WHERE id = ?";
    $assignmentStmt = $conn->prepare($assignmentSql);
    $assignmentStmt->bind_param("i", $assignment_id);
    $assignmentStmt->execute();
    $assignmentResult = $assignmentStmt->get_result();

    if ($assignmentResult->num_rows > 0) {
        $assignmentRow = $assignmentResult->fetch_assoc();

        // Обработка загрузки файла
        $fileInput = $_FILES['fileInput'];

        if ($fileInput['error'] === UPLOAD_ERR_OK) {
            // Генерируем уникальное имя файла
            $fileName = uniqid() . '_' . $fileInput['name'];

            // Путь к папке, куда будем сохранять файл
            $uploadDirectory = '../../uploads/works_students/';

            // Полный путь к файлу
            $filePath = $uploadDirectory . $fileName;

            // Перемещаем файл в указанную папку
            if (move_uploaded_file($fileInput['tmp_name'], $filePath)) {
                // Сохраняем только путь к файлу в базе данных с использованием prepared statement
                $insertFileSql = "INSERT INTO submitted_assignments (assignment_id, student_id, file_path) VALUES (?, ?, ?)";
                $insertFileStmt = $conn->prepare($insertFileSql);
                $insertFileStmt->bind_param("iis", $assignment_id, $student_id, $filePath);
                $insertFileStmt->execute();

                // Уведомляем преподавателя о новой работе
                $assignmentTitle = $assignmentRow['title'];
                $notificationMessage = "Поступила новая работа для проверки по заданию: $assignmentTitle";

                $teacherId = $assignmentRow['teacher_id'];

                $insertNotificationSql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
                $insertNotificationStmt = $conn->prepare($insertNotificationSql);
                $insertNotificationStmt->bind_param("is", $teacherId, $notificationMessage);
                $insertNotificationStmt->execute();
?>
                <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="successModalLabel">Успешно!</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="lead">Файл успешно загружен и отправлен на проверку.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                            </div>
                        </div>
                    </div>
                </div>


                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                <script>
                    var successModal = new bootstrap.Modal(document.getElementById("successModal"));
                    successModal.show();
                </script>
<?php } else {
                echo "Произошла ошибка при перемещении файла.";
            }
        } else {
            echo "Произошла ошибка при загрузке файла.";
        }
    } else {
        echo "Задание не найдено.";
    }
} else {
    echo "Недопустимый метод запроса.";
}

include_once '../base/footer.php';
?>