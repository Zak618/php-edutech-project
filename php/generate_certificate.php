<?php
include_once "./base/header.php";
include_once "../php/database/db.php";

// Проверяем, есть ли GET-параметр course_id
if (!isset($_GET['course_id'])) {
    header("Location: catalog.php");
    exit();
}

$courseId = $_GET['course_id'];
$currentUserId = $id;

// Получаем информацию о курсе
$courseSql = "SELECT * FROM courses WHERE id = '$courseId'";
$courseResult = $conn->query($courseSql);

if ($courseResult->num_rows > 0) {
    $course = $courseResult->fetch_assoc();
} else {
    // Если курс не найден, редиректим на страницу каталога или другую нужную страницу
    header("Location: catalog.php");
    exit();
}

// Получаем информацию о модулях
$modulesSql = "SELECT * FROM modules WHERE course_id = '$courseId'";
$modulesResult = $conn->query($modulesSql);
$modules = [];

while ($moduleRow = $modulesResult->fetch_assoc()) {
    $modules[] = $moduleRow;
}

// Получаем информацию о уроках для каждого модуля
$lessons = [];
foreach ($modules as $module) {
    $moduleId = $module['id'];
    $lessonsSql = "SELECT * FROM lessons WHERE module_id = '$moduleId'";
    $lessonsResult = $conn->query($lessonsSql);

    while ($lessonRow = $lessonsResult->fetch_assoc()) {
        $lessons[] = $lessonRow;
    }
}

// Получаем информацию о прогрессе студента
$totalPointsSql = "SELECT SUM(points) AS total_points FROM progress WHERE student_id = '$currentUserId' AND material_id IN (SELECT id FROM materials WHERE lesson_id IN (SELECT id FROM lessons WHERE module_id IN (SELECT id FROM modules WHERE course_id = '$courseId')))";
$totalPointsResult = $conn->query($totalPointsSql);
$totalPointsRow = $totalPointsResult->fetch_assoc();
$totalPoints = $totalPointsRow['total_points'];

$totalPossiblePointsSql = "SELECT SUM(points) AS total_possible_points FROM materials WHERE lesson_id IN (SELECT id FROM lessons WHERE module_id IN (SELECT id FROM modules WHERE course_id = '$courseId'))";
$totalPossiblePointsResult = $conn->query($totalPossiblePointsSql);
$totalPossiblePointsRow = $totalPossiblePointsResult->fetch_assoc();
$totalPossiblePoints = $totalPossiblePointsRow['total_possible_points'];

$progressPercentage = ($totalPoints / $totalPossiblePoints) * 100;

// Получаем информацию о студенте
$studentInfoSql = "SELECT * FROM student WHERE id = '$currentUserId'";
$studentInfoResult = $conn->query($studentInfoSql);

if ($studentInfoResult->num_rows > 0) {
    $studentInfo = $studentInfoResult->fetch_assoc();
} else {
    // Если информация о студенте не найдена, редиректим на страницу каталога или другую нужную страницу
    header("Location: catalog.php");
    exit();
}

// Указываем путь к файлу шрифта
$fontPath = 'arial.ttf';

// Создаем изображение сертификата
$certificateImage = imagecreatetruecolor(800, 600);
$backgroundColor = imagecolorallocate($certificateImage, 255, 255, 255);
$textColor = imagecolorallocate($certificateImage, 0, 0, 0);
$borderColor = imagecolorallocate($certificateImage, 0, 0, 0);

// Заполняем изображение белым фоном
imagefilledrectangle($certificateImage, 0, 0, 800, 600, $backgroundColor);

// Рисуем рамку вокруг сертификата
imagerectangle($certificateImage, 0, 0, 799, 599, $borderColor);

// Указываем путь к файлу изображения
$imagePath = '../image/profile/EduTech.jpg'; 
$imagePath1 = '../image/profile/pechat.jpg'; 
// Загружаем изображение
$logoImage = imagecreatefromjpeg($imagePath);
$logoImage1 = imagecreatefromjpeg($imagePath1);

// Копируем изображение на сертификат
imagecopy($certificateImage, $logoImage, 50, 20, 0, 0, imagesx($logoImage), imagesy($logoImage));
imagecopy($certificateImage, $logoImage1, 550, 350, 0, 0, imagesx($logoImage1), imagesy($logoImage1));
// Используем выбранный шрифт
imagettftext($certificateImage, 28, 0, 290, 60, $textColor, $fontPath, 'СЕРТИФИКАТ');
imagettftext($certificateImage, 20, 0, 50, 150, $textColor, $fontPath, 'Выдан студенту:');
imagettftext($certificateImage, 24, 0, 300, 150, $textColor, $fontPath, $studentInfo['name']);
imagettftext($certificateImage, 20, 0, 50, 250, $textColor, $fontPath, 'Курс:');
imagettftext($certificateImage, 18, 0, 120, 250, $textColor, $fontPath, $course['title']);
imagettftext($certificateImage, 20, 0, 50, 350, $textColor, $fontPath, 'Набрано баллов:');
imagettftext($certificateImage, 24, 0, 300, 350, $textColor, $fontPath, $totalPoints);
imagettftext($certificateImage, 20, 0, 50, 450, $textColor, $fontPath, 'Дата получения:');
imagettftext($certificateImage, 24, 0, 280, 450, $textColor, $fontPath, date('Y-m-d'));
imagettftext($certificateImage, 18, 0, 330, 550, $textColor, $fontPath, 'EduTech Comp.');

// Убедитесь, что у вас есть права на запись в эту директорию
$certificateImagePath = './database/images/' . $currentUserId . '_' . time() . '.png';
imagepng($certificateImage, $certificateImagePath);
imagedestroy($certificateImage);

// Добавляем запись о сертификате в базу данных
$insertCertificateSql = "INSERT INTO certificates (student_email, course_name, certificate_url) VALUES ('" . $studentInfo['email'] . "', '" . $course['title'] . "', '$certificateImagePath')";
$conn->query($insertCertificateSql);
?>

<main class="container mt-5">
    <h2 class="text-center">Сертификат успешно создан</h2>
    <img src="<?php echo $certificateImagePath; ?>" class="img-fluid mx-auto d-block" alt="Сертификат">
</main>

<?php
include_once "./base/footer.php";
?>
