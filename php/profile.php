<?php
include_once "./base/header.php";
?>

<div class="p-2 text-white bg-opacity-75" style="height: 300px; background-color: #173038;">
    <div class="text-center">
        <img src="../image/profile/1.svg" class="rounded" width="18%" style="margin-top: 160px;">
    </div>
</div>

<h2 align="center" style="margin-top: 120px;"><?php echo $name ?></h2>
<p align="center" style="margin-top: 20px; font-size: 23px;">Моя почта: <?php echo $email ?></p>
<?php
include_once "./base/footer.php";
?>