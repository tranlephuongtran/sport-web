<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký SportWeb</title>
    <script src="https://kit.fontawesome.com/25a68e7dac.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="layout/css/register.css">
</head>
<?php
if (!isset($_GET['register'])) {
    $login = 1;
} else {
    $login = $_GET['register'];
}
?>
<style>
    .status-paid {
        color: green;
        font-weight: bold;
    }

    .status-unpaid {
        color: red;
        font-weight: bold;
    }

    .table th,
    .table td {
        text-align: center;
        vertical-align: middle;
    }
</style>

<body>
    <div id="wrapper">
        <form id="form-login" onsubmit="return validateForm()">
            <h1 class="form-heading">Đăng Ký</h1>
            <div class="form-group">
                <i class="fa-regular fa-user"></i>
                <input type="text" class="form-input" placeholder="Tên đăng nhập" id="username" required>
            </div>
            <div class="form-group">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" class="form-input" placeholder="Email" id="email" required>
            </div>
            <div class="form-group">
                <i class="fa-solid fa-key"></i>
                <input type="password" class="form-input" placeholder="Mật khẩu" id="password" required>
                <div class="eye">
                    <i class="far fa-eye"></i>
                </div>
            </div>
            <div class="form-group">
                <i class="fa-solid fa-key"></i>
                <input type="password" class="form-input" placeholder="Nhập lại mật khẩu" id="confirmPassword" required>
                <div class="eye">
                    <i class="far fa-eye"></i>
                </div>
            </div>
            <div class="form-group">
                <i class="fa-regular fa-user"></i>
                <input type="text" class="form-input" placeholder="Tên của bạn" id="name" required>
            </div>
            <div class="form-group">
                <i class="fa-solid fa-phone"></i>
                <input type="tel" class="form-input" placeholder="Số điện thoại" id="phone" required>
            </div>
            <input type="checkbox" id="agreePolicy" style="width: auto; margin-right: 10px;" required>
            <label for="agreePolicy" style="color: #f5f5f5; font-size: 14px;">Tôi đồng ý với <a href="#" target="_blank"
                    style="color: rgb(215, 248, 124);">chính sách và điều
                    khoản</a>.</label><br>
            <a href="index.php?login" id="link-register">Đã có tài khoản!</a>
            <input type="submit" value="Đăng ký" class="form-submit">
        </form>
    </div>

    <script src="../../layout/js/jquery-3.7.1.min.js"></script>
    <script src="../../layout/js/eye.js"></script>

</body>

</html>