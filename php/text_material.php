<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<?php
include_once "./base/header.php";
include_once "../php/database/db.php";

if (isset($_GET['material_id'])) {
    $material_id = $_GET['material_id'];

    $materialSql = "SELECT * FROM materials WHERE id = '$material_id'";
    $materialResult = $conn->query($materialSql);

    if ($materialResult->num_rows > 0) {
        $materialRow = $materialResult->fetch_assoc();
        $lesson_id = $materialRow['lesson_id'];

        $userRole = $role; 
        $creatorRole = 2;

        // Получаем предыдущий материал
        $prevMaterialSql = "SELECT * FROM materials WHERE lesson_id = '$lesson_id' AND id < $material_id ORDER BY id DESC LIMIT 1";
        $prevMaterialResult = $conn->query($prevMaterialSql);
        $prevMaterial = ($prevMaterialResult->num_rows > 0) ? $prevMaterialResult->fetch_assoc() : null;

        // Получаем следующий материал
        $nextMaterialSql = "SELECT * FROM materials WHERE lesson_id = '$lesson_id' AND id > $material_id ORDER BY id ASC LIMIT 1";
        $nextMaterialResult = $conn->query($nextMaterialSql);
        $nextMaterial = ($nextMaterialResult->num_rows > 0) ? $nextMaterialResult->fetch_assoc() : null;

        // Проверяем, является ли текущий пользователь преподавателем
        if ($userRole == $creatorRole) {
            // Если да, то отображаем кнопки "Редактировать" и "Удалить"
            ?>
            <div class="container mt-5">
                <div class="card mb-3">
                    <div class="card-body">
                        <h1 class="card-title">Текстовая информация</h1>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <p id="materialContent"><?php echo $materialRow['content']; ?></p>
                    </div>
                </div>

                <!-- Кнопки "Редактировать" и "Удалить" -->
                <div class="mb-3">
                    <button class="btn btn-primary" onclick="editMaterial(<?php echo $materialRow['id']; ?>)">Редактировать</button>
                    <!-- Кнопка для удаления материала -->
                    <button class="btn btn-danger" onclick="removeMaterial(<?php echo $materialRow['id']; ?>)">Удалить материал</button>
                </div>
            </div>

            <!-- Модальное окно для редактирования текстового материала -->
            <div class="modal fade" id="editMaterialModal" tabindex="-1" aria-labelledby="editMaterialModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editMaterialModalLabel">Редактировать текстовый материал</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Форма для редактирования текстового материала -->
                            <form id="editMaterialForm" action="../../diploma-project/php/database/update_material.php" method="post">
                                <input type="hidden" name="material_id" value="<?php echo $materialRow['id']; ?>">
                                <label for="materialContent" class="form-label">Текстовое содержание:</label>
                                <textarea class="form-control" name="materialContent"><?php echo $materialRow['content']; ?></textarea>
                                <button type="submit" class="btn btn-primary mt-3">Сохранить изменения</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mt-3">
                <div class="row">
                    <div class="col-6">
                        <?php if ($prevMaterial) { ?>
                            <a class="btn btn-secondary" href="text_material.php?material_id=<?php echo $prevMaterial['id']; ?>">Назад</a>
                        <?php } ?>
                    </div>
                    <div class="col-6 text-end">
                        <?php if ($nextMaterial) { ?>
                            <a class="btn btn-secondary" href="text_material.php?material_id=<?php echo $nextMaterial['id']; ?>">Далее</a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <script>
                function editMaterial(materialId) {
                    // Задаем значения в модальном окне перед открытием
                    document.getElementById('editMaterialForm').elements.material_id.value = materialId;
                    document.getElementById('editMaterialForm').elements.materialContent.value = document.getElementById('materialContent').innerHTML;

                    // Открываем модальное окно
                    $('#editMaterialModal').modal('show');
                }

                $(document).ready(function() {
                    // Обработка формы редактирования
                    $('#editMaterialForm').submit(function(e) {
                        e.preventDefault();

                        var form = $(this);
                        var url = form.attr('action');
                        var formData = form.serialize();

                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: formData,
                            success: function(response) {
                                // Закрываем модальное окно
                                $('#editMaterialModal').modal('hide');

                                // Обновляем текст материала на странице
                                $('#materialContent').text(form.find('textarea[name="materialContent"]').val());
                            },
                            error: function(error) {
                                console.log('Error:', error);
                            }
                        });
                    });
                });

                function removeMaterial(materialId) {
                    if (confirm('Вы уверены, что хотите удалить этот материал?')) {
                        $.ajax({
                            type: 'POST',
                            url: './database/delete_material.php',
                            data: {
                                material_id: materialId
                            },
                            success: function(response) {
                                var result = JSON.parse(response);
                                if (result.status === 'success') {
                                    // Перенаправляем пользователя на страницу с уроками (или иную страницу)
                                    window.location.href = './lesson_details.php?lesson_id=<?php echo $materialRow['lesson_id']; ?>'; // Замените 'lessons.php' на нужный URL
                                } else {
                                    console.error('Ошибка удаления материала: ', result.message);
                                }
                            },
                            error: function(error) {
                                console.error('Ошибка удаления материала: ', error);
                            }
                        });
                    }
                }
            </script>
            <?php
        } else {
            // Если пользователь не является преподавателем, отображаем только содержимое материала
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

                <div class="container mt-3">
                    <div class="row">
                        <div class="col-6">
                            <?php if ($prevMaterial) { ?>
                                <a class="btn btn-secondary" href="text_material.php?material_id=<?php echo $prevMaterial['id']; ?>">Назад</a>
                            <?php } ?>
                        </div>
                        <div class="col-6 text-end">
                            <?php if ($nextMaterial) { ?>
                                <a class="btn btn-secondary" href="text_material.php?material_id=<?php echo $nextMaterial['id']; ?>">Далее</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "Материал не найден.";
    }
} else {
    echo "Не указан ID материала.";
}

include_once "./base/footer.php";
?>
