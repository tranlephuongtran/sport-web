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
<?php
session_start();
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if (isset($_POST['btnLogin'])) {
    $email = mysqli_real_escape_string($conn, $_POST['CustomerEmail']);
    $password = mysqli_real_escape_string($conn, $_POST['CustomerPassword']);

    // Truy vấn lấy thông tin người dùng
    $query = "SELECT taikhoan.maTK, nguoidung.maNguoiDung, khachhang.maKH, taikhoan.password 
              FROM taikhoan 
              JOIN nguoidung ON taikhoan.maTK = nguoidung.maTK 
              JOIN khachhang ON nguoidung.maNguoiDung = khachhang.maNguoiDung 
              WHERE taikhoan.email = '$email'";

    $result = mysqli_query($conn, $query);
    if ($user = mysqli_fetch_assoc($result)) {
        if ($password === $user['password']) {
            $_SESSION['login'] = $user['maNguoiDung'];
            echo '<script>
                alert("Đăng nhập thành công");
                window.location.href = "index.php?home";
            </script>';
        } else {
            echo '<script>alert("Sai mật khẩu. Vui lòng nhập lại !");</script>';
        }
    } else {
        echo '<script>alert("Email không tồn tại");</script>';
    }

    mysqli_close($conn);
}
?>


<script>
    function validateLoginForm() {
        const email = document.getElementById('login-username').value;
        const password = document.getElementById('login-password').value;
        const emailPattern = /^[a-zA-Z0-9._%+-]+@(gmail\.com|edu\.vn|yahoo\.com|hotmail\.com|outlook\.com|example\.com)$/;
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (!emailPattern.test(email)) {
            alert('Vui lòng nhập email hợp lệ (ví dụ: user@gmail.com).');
            return false;
        }



        return true; // Valid inputs
    }
</script>

<body>
    <div class="video-wrapper">
        <video src="layout/img/loginvid.mp4" autoplay loop muted></video>
    </div>
    <div id="wrapper">
        <form action="" method="POST" id="form-login" onsubmit="return validateLoginForm()">
            <h1 class="form-heading">Đăng Nhập</h1>
            <div class="form-group">
                <i class="fa-regular fa-user"></i>
                <input type="email" name="CustomerEmail" class="form-input" placeholder="Email" id="login-username"
                    required>
            </div>
            <div class="form-group">
                <i class="fa-solid fa-key"></i>
                <input type="password" name="CustomerPassword" class="form-input" placeholder="Mật khẩu"
                    id="login-password" required>
                <div class="eye">
                    <i class="far fa-eye"></i>
                </div>
            </div>
            <a href="index.php?register" id="link-login">Đăng ký tài khoản!</a>
            <input type="submit" name="btnLogin" value="Đăng nhập" class="form-submit">
        </form>
    </div>
</body>
<script src="../../layout/js/jquery-3.7.1.min.js"></script>
<script src="../../layout/js/eye.js"></script>

</html>