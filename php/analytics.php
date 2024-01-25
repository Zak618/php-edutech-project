<?php
include_once "./base/header.php";
include_once "../php/database/db.php";

if (isset($_GET['course_id'])) {
    $courseId = $_GET['course_id'];

    // Получим информацию о курсе
    $courseSql = "SELECT * FROM courses WHERE id = '$courseId'";
    $courseResult = $conn->query($courseSql);

    if ($courseResult->num_rows > 0) {
        $course = $courseResult->fetch_assoc();

        // Получим информацию о модулях
        $modulesSql = "SELECT COUNT(*) AS total_modules FROM modules WHERE course_id = '$courseId'";
        $modulesResult = $conn->query($modulesSql);
        $totalModules = $modulesResult->fetch_assoc()['total_modules'];

        // Получим информацию о уроках
        $lessonsSql = "SELECT COUNT(*) AS total_lessons FROM lessons WHERE module_id IN (SELECT id FROM modules WHERE course_id = '$courseId')";
        $lessonsResult = $conn->query($lessonsSql);
        $totalLessons = $lessonsResult->fetch_assoc()['total_lessons'];

        // Получим информацию о студентах на курсе
        $studentsSql = "SELECT COUNT(DISTINCT user_id) AS total_students FROM student_courses WHERE course_id = '$courseId'";
        $studentsResult = $conn->query($studentsSql);
        $totalStudents = $studentsResult->fetch_assoc()['total_students'];

        // Получим информацию о выданных сертификатах
        $certificatesSql = "SELECT COUNT(*) AS total_certificates FROM certificates WHERE course_name = '" . $course['title'] . "'";
        $certificatesResult = $conn->query($certificatesSql);
        $totalCertificates = $certificatesResult->fetch_assoc()['total_certificates'];

        $daysAttendance = [];
$today = date('Y-m-d');
date_default_timezone_set('Europe/Moscow'); // Устанавливаем временную зону на Московское время

for ($i = 0; $i < 5; $i++) {
    $currentDate = date('Y-m-d', strtotime("-$i days"));
    $attendanceSql = "SELECT COUNT(DISTINCT user_id) AS daily_students
                      FROM student_courses
                      WHERE course_id = '$courseId' AND DATE(join_date) = '$currentDate'";
    $attendanceResult = $conn->query($attendanceSql);
    $dailyStudents = $attendanceResult->fetch_assoc()['daily_students'];
    $daysAttendance[] = $dailyStudents;
}

// Восстанавливаем временную зону на дефолтную после использования
date_default_timezone_set('UTC');


?>

        <div class="container mt-5">
            <h1>Аналитика по курсу: <?php echo $course['title']; ?></h1>

            <ul>
                <li>Всего модулей на курсе: <?php echo $totalModules; ?></li>
                <li>Всего уроков на курсе: <?php echo $totalLessons; ?></li>
                <li>Всего учащихся на курсе: <?php echo $totalStudents; ?></li>
                <li>Выдано сертификатов: <?php echo $totalCertificates; ?></li>
            </ul>

            <canvas id="attendanceChart" width="400" height="200"></canvas>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById('attendanceChart').getContext('2d');
            
            // Здесь используем данные посещаемости по дням
            var attendanceData = {
                labels: [
        '<?php echo date_format(date_create(date('Y-m-d', strtotime("-3 days"))), 'Y-m-d'); ?>',
        '<?php echo date_format(date_create(date('Y-m-d', strtotime("-2 days"))), 'Y-m-d'); ?>',
        '<?php echo date_format(date_create(date('Y-m-d', strtotime("-1 days"))), 'Y-m-d'); ?>',
        '<?php echo date_format(date_create(date('Y-m-d', strtotime("0 days"))), 'Y-m-d'); ?>',
        '<?php echo date_format(date_create(date('Y-m-d', strtotime("+1 days"))), 'Y-m-d'); ?>'
    ],
                datasets: [{
                    label: 'Студенты, присоединившиеся к курсу',
                    data: <?php echo json_encode(array_reverse($daysAttendance)); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };

            var options = {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            };

            var attendanceChart = new Chart(ctx, {
                type: 'bar',
                data: attendanceData,
                options: options
            });
        });
    </script>
        </div>

<?php
    } else {
        echo "<p style='text-align: center;'>Курс не найден.</p>";
    }
} else {
    echo "<p style='text-align: center;'>Не указан ID курса.</p>";
}

include_once "./base/footer.php";
?>
