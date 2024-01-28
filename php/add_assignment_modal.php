<!-- Модальное окно для добавления задания -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1" aria-labelledby="addAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAssignmentModalLabel">Добавить задание</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Форма для добавления задания -->
                <form action='../php/database/add_assignment.php' method='post' enctype='multipart/form-data'>
                    <input type='hidden' name='lesson_id' value='<?php echo $lesson_id; ?>'>

                    <div class='mb-3'>
                        <label for='assignmentTitle' class='form-label'>Заголовок задания:</label>
                        <input type='text' class='form-control' name='assignmentTitle' required>
                    </div>

                    <div class='mb-3'>
                        <label for='assignmentDescription' class='form-label'>Описание задания:</label>
                        <textarea class='form-control' name='assignmentDescription' required></textarea>
                    </div>

                    <div class='mb-3'>
                        <label for='assignmentFile' class='form-label'>Прикрепите файл (если необходимо):</label>
                        <input type='file' class='form-control' name='assignmentFile'>
                    </div>

                    <!-- Добавлено поле для максимального количества баллов -->
                    <div class='mb-3'>
                        <label for='assignmentMaxPoints' class='form-label'>Максимальное количество баллов:</label>
                        <input type='number' class='form-control' name='assignmentMaxPoints' value='100' min='1'>
                    </div>

                    <button type='submit' class='btn btn-primary'>Добавить задание</button>
                </form>
            </div>
        </div>
    </div>
</div>
