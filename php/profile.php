<?php
include_once "./base/header.php";
require_once("./database/db.php");

// Получение фото профиля из базы данных
if ($role == 2) {
    $sql = "SELECT image FROM `teacher` WHERE email = '$email'";
} else {
    $sql = "SELECT image FROM `student` WHERE email = '$email'";
}
$result = $conn->query($sql);

$profile_image = "";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $profile_image = './database/' . $row['image'];
}

$default_image = '../image/profile/1.svg';
?>

<div class="p-2 text-white bg-opacity-75" style="height: 400px; background-image: url('<?php echo "../image/profile/3.jpg"; ?>');">
    <div class="text-center">
        <img src="<?php echo $profile_image != './database/' ? $profile_image : $default_image; ?>" class="rounded-circle" width="18%" style="margin-top: 260px">
    </div>
</div>

<h2 align="center" style="margin-top: 120px;"><?php echo $name ?></h2>
<p align="center" style="margin-top: 20px; font-size: 23px;">Моя почта: <?php echo $email ?></p>

<div class="container mt-4">
    <div class="card mt-4">
        <div class="card-body">
            <?php
            $certificates_sql = "SELECT course_name, certificate_url FROM certificates WHERE student_email = '$email'";
            $certificates_result = $conn->query($certificates_sql);

            if ($certificates_result->num_rows > 0) {
                while ($cert_row = $certificates_result->fetch_assoc()) {
                    echo "<p class='mb-2'><a href='" . $cert_row['certificate_url'] . "' target='_blank' class='btn btn-success'>" . $cert_row['course_name'] . " Certificate</a></p>";
                }
            } else {
                echo "<p class='text-center'>Нет сертификатов</p>";
            }
            ?>
        </div>
    </div>
</div>

<?php
include_once "./base/footer.php";
?>