<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập SportWeb</title>
    <script src="https://kit.fontawesome.com/25a68e7dac.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="layout/css/login.css">
</head>
<?php
if (!isset($_GET['login'])) {
    $login = 1;
} else {
    $login = $_GET['login'];
}
?>

<body>
    <div class="video-wrapper">
        <video src="layout/img/loginvid.mp4" autoplay loop muted id="#loginvideo"></video>
    </div>
    <div id="wrapper">
        <form action="" id="form-login" onsubmit="return validateLoginForm()">
            <h1 class="form-heading">Đăng Nhập</h1>
            <div class="form-group">
                <i class="fa-regular fa-user"></i>
                <input type="text" class="form-input" placeholder="Tên đăng nhập" id="login-username" required>
            </div>
            <div class="form-group">
                <i class="fa-solid fa-key"></i>
                <input type="password" class="form-input" placeholder="Mật khẩu" id="login-password" required>
                <div class="eye">
                    <i class="far fa-eye"></i>
                </div>
            </div>
            <a href="index.php?register" id="link-login">Đăng ký tài khoản!</a>
            <input type="submit" value="Đăng nhập" class="form-submit">
        </form>
    </div>

</body>
<script src="../../layout/js/jquery-3.7.1.min.js"></script>
<script src="../../layout/js/eye.js"></script>

</html>