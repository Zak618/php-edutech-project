<?php
include_once "./base/header.php";
?>

<form class="row g-3" style="width: 50%; margin-left: auto; margin-right: auto; margin-top: 20px; margin-top: 100px;" action="./database/update_profile.php" method="POST">
<div class="row mb-3">
    <label for="inputEmail3" class="col-sm-2 col-form-label">Имя</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="inputEmail3" value="<?php echo $name ?>" name="name">
    </div>
  </div>
  <div class="row mb-3">
    <label for="inputEmail3" class="col-sm-2 col-form-label">Фамилия</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="inputEmail3" value="<?php echo $female ?>" name="female">
    </div>
  </div>  
<div class="row mb-3" style="display: none;">
    <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
    <div class="col-sm-10">
      <input type="email" class="form-control" id="inputEmail3" value="<?php echo $email ?>" name="email">
    </div>
  </div>
  <div class="row mb-3">
    <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="inputPassword3" value="<?php echo $password ?>" name="password">
    </div>
  </div>
  

  <button type="submit" class="btn btn-primary">Сохранить</button>
</form>

<?php
include_once "./base/footer.php";
?>