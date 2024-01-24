<?php
include_once "./base/header.php";
?>

<form class="row g-3" style="width: 50%; margin-left: auto; margin-right: auto; margin-top: 20px; margin-top: 100px;" action="./database/update_profile.php" method="POST" enctype="multipart/form-data">
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
  <style>
    .password-container {
        position: relative;
    }

    .password-input {
        padding-right: 30px;
    }

    .eye-icon {
        position: absolute;
        top: 50%;
        right: -460px;
        transform: translateY(-50%);
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.9s ease-in-out;
    }

    .eye-icon:hover {
        opacity: 1;
    }

    .smiley {
        display: inline-block;
        animation: bounce 0.9s infinite alternate;
    }

    @keyframes bounce {
        to {
            transform: translateY(-5px);
        }
    }
</style>

<div class="row mb-3">
    <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
    <div class="col-sm-10 password-container">
        <input type="password" class="form-control password-input" id="inputPassword3" value="<?php echo $password ?>" name="password">
        <span class="eye-icon" onclick="togglePassword()"><span class="smiley"><img src="../image/profile/free-icon-close-eyes-11516530.png" width="5%"></span></span>
    </div>
</div>

<script>
    function togglePassword() {
        var passwordInput = document.getElementById("inputPassword3");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
        } else {
            passwordInput.type = "password";
        }
    }
</script>

  <div class="row mb-3">
    <label for="inputImage" class="col-sm-2 col-form-label">Фото профиля</label>
    <div class="col-sm-10">
      <input type="file" class="form-control" id="inputImage" name="profile_image">
    </div>
  </div>
  

  <button type="submit" class="btn btn-primary">Сохранить</button>
</form>

<?php
include_once "./base/footer.php";
?>