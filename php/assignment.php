<?php
include_once "../../diploma-project/php/base/header.php";
include_once "../php/database/db.php";

// Получаем информацию о задании
if (isset($_GET['assignment_id'])) {
    $assignment_id = $_GET['assignment_id'];

    $assignmentSql = "SELECT * FROM assignments WHERE id = '$assignment_id'";
    $assignmentResult = $conn->query($assignmentSql);

    if ($assignmentResult->num_rows > 0) {
        $assignmentRow = $assignmentResult->fetch_assoc();
        $lesson_id = $assignmentRow['lesson_id'];

        // Получаем все задания в уроке, упорядоченные по ID
        $allAssignmentsSql = "SELECT * FROM assignments WHERE lesson_id = '$lesson_id' ORDER BY id";
        $allAssignmentsResult = $conn->query($allAssignmentsSql);
        $allAssignments = [];
        while ($row = $allAssignmentsResult->fetch_assoc()) {
            $allAssignments[] = $row;
        }

        // Находим текущий индекс задания в массиве
        $currentIndex = array_search($assignmentRow, $allAssignments);

        // Получаем предыдущее задание
        $prevAssignment = null;
        for ($i = $currentIndex - 1; $i >= 0; $i--) {
            $prevAssignment = $allAssignments[$i];
            break;
        }

        // Получаем следующее задание
        $nextAssignment = null;
        for ($i = $currentIndex + 1; $i < count($allAssignments); $i++) {
            $nextAssignment = $allAssignments[$i];
            break;
        }
?>
        <!-- Навигационная панель -->
        <div class="container mt-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Навигация по заданиям
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills">
                        <?php
                        foreach ($allAssignments as $index => $navAssignment) {
                            $navAssignmentLink = 'assignment.php?assignment_id=' . $navAssignment['id'];

                            $navItemClass = 'nav-item';
                            $navLinkClass = 'nav-link';

                            if ($navAssignment['id'] == $assignment_id) {
                                // Текущий элемент
                                $navItemClass .= ' active';
                            }

                        ?>
                            <li class="<?php echo $navItemClass; ?>">
                                <a class="<?php echo $navLinkClass; ?>" href="<?php echo $navAssignmentLink; ?>">
                                    <?php echo $navAssignment['title']; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="card mb-3">
                <div class="card-body">
                    <h1 class="card-title"><?php echo $assignmentRow['title']; ?></h1>
                    <p class="card-text"><?php echo $assignmentRow['description']; ?></p>
                    <p class="card-text">Максимальные баллы: <?php echo $assignmentRow['points']; ?></p>

                    <?php
                    // Display download link if file path exists
                    if (!empty($assignmentRow['file_path'])) {
                        $fileDownloadPath = '../uploads/' . basename($assignmentRow['file_path']);
                        echo '<p class="card-text">Файл учителя: <a href="' . $fileDownloadPath . '" download>Скачать</a></p>';
                    }
                    if ($role == '2') {
                        echo '<form action="../php/database/delete_assignment.php" method="post" style="margin-top:15px;">';
                            echo '<input type="hidden" name="assignment_id" value="' . $assignment_id . '">';
                            echo '<button type="submit" class="btn btn-danger">Удалить задание</button>';
                            echo '</form>';
                    }
                    // Display information about student submission
                    $student_id = $id;
                    $role = $_SESSION['role'];

                    if ($role == '1') {
                        // Студент
                        $studentSubmissionSql = "SELECT * FROM submitted_assignments WHERE assignment_id = '$assignment_id' AND student_id = '$student_id'";
                        $studentSubmissionResult = $conn->query($studentSubmissionSql);

                        if ($studentSubmissionResult->num_rows > 0) {
                            $studentSubmissionRow = $studentSubmissionResult->fetch_assoc();
                            $studentFilePath = '../uploads/works_students/' . basename($studentSubmissionRow['file_path']);
                            echo '<p class="card-text">Ваш файл: <a href="' . $studentFilePath . '" download>Скачать</a></p>';

                            // Add a form for deleting the student's file
                            echo '<form action="../php/database/delete_student_file.php" method="post">';
                            echo '<input type="hidden" name="submission_id" value="' . $studentSubmissionRow['id'] . '">';
                            echo '<button type="submit" class="btn btn-danger">Удалить ваш файл</button>';
                            echo '</form>';

                            // Display points awarded to the student
                            echo '<p class="card-text">Баллы: ';
                            $pointsSql = "SELECT points_awarded FROM student_points WHERE assignment_id = '$assignment_id' AND student_id = '$student_id'";
                            $pointsResult = $conn->query($pointsSql);

                            if ($pointsResult->num_rows > 0) {
                                $pointsRow = $pointsResult->fetch_assoc();
                                echo $pointsRow['points_awarded'];
                            } else {
                                echo 'На проверке';
                            }
                            echo '</p>';
                        } else {
                            // Если студент не загрузил файл, отображаем форму для загрузки
                            echo '<form action="../php/database/add_check_assignment.php" method="post" enctype="multipart/form-data">';
                            echo '<input type="hidden" name="assignment_id" value="' . $assignment_id . '">';
                            echo '<div class="mb-3">';
                            echo '<label for="fileInput" class="form-label">Загрузите файл:</label>';
                            echo '<input type="file" class="form-control" name="fileInput" id="fileInput" required>';
                            echo '</div>';
                            echo '<button type="submit" class="btn btn-primary">Отправить задание</button>';
                            echo '</form>';
                        }
                    } elseif ($role == '2') {
                        // Преподаватель
                        echo '<h2>Работы студентов:</h2>';
                        $studentSubmissionsSql = "SELECT * FROM submitted_assignments WHERE assignment_id = '$assignment_id'";
                        $studentSubmissionsResult = $conn->query($studentSubmissionsSql);

                        while ($studentSubmissionRow = $studentSubmissionsResult->fetch_assoc()) {
                            $student_id = $studentSubmissionRow['student_id'];
                            $studentFilePath = '../uploads/works_students/' . basename($studentSubmissionRow['file_path']);

                            // Вывод информации о работе студента
                            echo '<p>Студент: ' . $student_id . '</p>';
                            echo '<p>Файл студента: <a href="' . $studentFilePath . '" download>Скачать</a></p>';

                            // Проверка, были ли выставлены баллы
                            $checkPointsSql = "SELECT points_awarded FROM student_points WHERE assignment_id = '$assignment_id' AND student_id = '$student_id'";
                            $checkPointsResult = $conn->query($checkPointsSql);

                            if ($checkPointsResult->num_rows > 0) {
                                // Если баллы уже были проставлены, отображаем информацию и форму для редактирования
                                $pointsRow = $checkPointsResult->fetch_assoc();
                                $awardedPoints = $pointsRow['points_awarded'];

                                echo '<p>Выставленные баллы: ' . $awardedPoints . '</p>';
                                echo '<form action="../php/database/edit_student_points.php" method="post">';
                                echo '<input type="hidden" name="assignment_id" value="' . $assignment_id . '">';
                                echo '<input type="hidden" name="student_id" value="' . $student_id . '">';
                                echo '<label for="editPointsInput">Редактировать баллы:</label>';
                                echo '<input type="number" name="edit_points" id="editPointsInput" min="0" max="' . $assignmentRow['points'] . '" value="' . $awardedPoints . '" required>';
                                echo '<button type="submit" class="btn btn-primary">Сохранить изменения</button>';
                                echo '</form>';
                            } else {
                                // Если баллы еще не были проставлены, отображаем форму для выставления баллов
                                echo '<form action="../php/database/add_student_points.php" method="post">';
                                echo '<input type="hidden" name="assignment_id" value="' . $assignment_id . '">';
                                echo '<input type="hidden" name="student_id" value="' . $student_id . '">';
                                echo '<label for="pointsInput">Баллы:</label>';
                                echo '<input type="number" name="points_awarded" id="pointsInput" min="0" max="' . $assignmentRow['points'] . '" required>';
                                echo '<button type="submit" class="btn btn-primary">Выставить баллы</button>';
                                echo '</form>';
                            }
                        }
                    }

                    ?>
                </div>
            </div>

            <div class="mt-3">
                <?php
                if ($prevAssignment) {
                    echo "<a class='btn btn-secondary' href='assignment.php?assignment_id=" . $prevAssignment['id'] . "'>Назад</a>";
                }
                if ($nextAssignment) {
                    echo "<a class='btn btn-secondary float-end' href='assignment.php?assignment_id=" . $nextAssignment['id'] . "'>Далее</a>";
                }
                ?>
            </div>
        </div>

<?php
    } else {
        echo "Задание не найдено.";
    }
} else {
    echo "Не указан ID задания.";
}

include_once "../../diploma-project/php/base/footer.php";
?>
