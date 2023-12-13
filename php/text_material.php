<?php
include_once "./base/header.php";
include_once "../php/database/db.php";

if (isset($_GET['material_id'])) {
    $material_id = $_GET['material_id'];

    $materialSql = "SELECT * FROM materials WHERE id = '$material_id'";
    $materialResult = $conn->query($materialSql);

    if ($materialResult->num_rows > 0) {
        $materialRow = $materialResult->fetch_assoc();
?>
        <div class="container mt-5">
            <div class="card mb-3">
                <div class="card-body">
                    <h1 class="card-title">Текстовая информация</h1>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <p><?php echo $materialRow['content']; ?></p>
                </div>
            </div>
        </div>
<?php
    } else {
        echo "Материал не найден.";
    }
} else {
    echo "Не указан ID материала.";
}

include_once "./base/footer.php";
?>
