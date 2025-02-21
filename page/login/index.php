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
session_start();
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if (isset($_POST['btnLogin'])) {
    $email = mysqli_real_escape_string($conn, $_POST['CustomerEmail']);
    $password = mysqli_real_escape_string($conn, $_POST['CustomerPassword']);

    $query = "SELECT taikhoan.maTK, nguoidung.maNguoiDung, khachhang.maKH, taikhoan.password, taikhoan.status 
          FROM taikhoan 
          JOIN nguoidung ON taikhoan.maTK = nguoidung.maTK 
          JOIN khachhang ON nguoidung.maNguoiDung = khachhang.maNguoiDung 
          WHERE taikhoan.email = '$email'";



    $result = mysqli_query($conn, $query);
    if ($user = mysqli_fetch_assoc($result)) {
        // Kiểm tra trạng thái tài khoản
        if ($user['status'] == 0) {
            echo '<script>alert("Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email để kích hoạt tài khoản.");</script>';
        } else {
            // Kiểm tra mật khẩu
            if ($password === $user['password']) {
                $_SESSION['login'] = $user['maNguoiDung'];
                echo '<script>
                    alert("Đăng nhập thành công");
                    window.location.href = "index.php?home";
                </script>';
            } else {
                echo '<script>alert("Sai mật khẩu. Vui lòng nhập lại!");</script>';
            }
        }
    } else {
        echo '<script>alert("Email không tồn tại");</script>';
    }

    mysqli_close($conn);
}
?>

<body>

    <div class="video-wrapper">
        <video src="layout/img/loginvid.mp4" autoplay loop muted></video>
    </div>
    <div id="wrapper">
        <form action="" method="POST" id="form-login" onsubmit="return validateLoginForm()">
            <h1 class="form-heading">Đăng Nhập</h1>
            <div class="form-group">
                <i class="fa-regular fa-user"></i>
                <input type="email" name="CustomerEmail" class="form-input" placeholder="Email" id="login-username">
            </div>
            <div class="form-group">
                <i class="fa-solid fa-key"></i>
                <input type="password" name="CustomerPassword" class="form-input" placeholder="Mật khẩu"
                    id="login-password">
                <div class="eye">
                    <i class="far fa-eye-slash"></i>
                </div>
            </div>
            <a href="index.php?register" id="link-login">Đăng ký tài khoản!</a>
            <input type="submit" name="btnLogin" value="Đăng nhập" class="form-submit">
        </form>
    </div>

</body>
<script src="../../layout/js/jquery-3.7.1.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const eyeIcon = document.querySelector(".eye");
        const passwordInput = document.getElementById("login-password");

        eyeIcon.addEventListener("click", function () {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.children[0].classList.remove("fa-eye-slash");
                eyeIcon.children[0].classList.add("fa-eye");
            } else {
                passwordInput.type = "password";
                eyeIcon.children[0].classList.remove("fa-eye");
                eyeIcon.children[0].classList.add("fa-eye-slash");
            }
        });
    });
    // Hàm kiểm tra form đăng nhập
    document.getElementById("form-login").addEventListener("submit", function (event) {
        const emailInput = document.querySelector("input[name='CustomerEmail']");
        const passwordInput = document.querySelector("input[name='CustomerPassword']");

        if (!emailInput.value.trim() || !passwordInput.value.trim()) {
            alert("Vui lòng nhập đầy đủ email và mật khẩu!");
            event.preventDefault();
        }
    });

</script>


</html>