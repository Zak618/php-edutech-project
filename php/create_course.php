<?php include_once "./base/header.php"; 

if (!isset($_SESSION['role'])) {
    // Сохраняем URL, на который пытается зайти неаутентифицированный пользователь
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    // Перенаправляем на страницу входа
    header("Location: ../../../diploma-project/php/url_auth.php");
    exit();
}
?>

<main>
    <h2 align="center" style="margin-top: 50px;">Создать новый курс</h2>

    <div style="margin-top: 50px; display:flex; justify-content: center;">
        <form action="../php/database/create_db_course.php" method="POST">
            <div class="form-group">
                <label for="title">Название курса:</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Описание курса:</label>
                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
            </div>
            <div style="display: none;">
                <input type="text" class="form-control" id="title" name="teacher_id" value="<?php echo $teacher_id ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Создать курс</button>
        </form>
    </div>
</main>

<?php include_once "./base/footer.php"; ?>