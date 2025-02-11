<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ĐĂNG NHẬP ADMIN</title>
    <script src="https://kit.fontawesome.com/25a68e7dac.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="layout/css/login_ad.css">
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

if (isset($_POST['btnLogin_ad'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Truy vấn lấy thông tin người dùng
    $query = "SELECT taikhoan.maTK, nguoidung.maNguoiDung, nhanvien.maNV, taikhoan.password 
              FROM taikhoan 
              JOIN nguoidung ON taikhoan.maTK = nguoidung.maTK 
              JOIN nhanvien ON nguoidung.maNguoiDung = nhanvien.maNguoiDung 
              WHERE taikhoan.email = '$email'";

    $result = mysqli_query($conn, $query);
    if ($user = mysqli_fetch_assoc($result)) {
        if ($password === $user['password']) {
            $_SESSION['login_ad'] = $user['maNguoiDung'];
            echo '<script>
                alert("Đăng nhập thành công");
                window.location.href = "index_ad.php?dashboard";
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

<body>
    <div id="wrapper">
        <form id="form-login" action="" method="POST" onsubmit="return validateForm()">
            <h1 class="form-heading">Đăng Nhập ADMIN</h1>
            <div class="form-group">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" class="form-input" placeholder="Email" name="email">
            </div>
            <div class="form-group">
                <i class="fa-solid fa-key"></i>
                <input type="password" class="form-input" placeholder="Mật khẩu" name="password">
            </div>

            <input type="submit" name="btnLogin_ad" value="Đăng nhập" class="form-submit">
        </form>
    </div>
    <script src="../../layout/js/jquery-3.7.1.min.js"></script>
    <script>
        // Hàm kiểm tra form đăng nhập
        document.getElementById("form-login").addEventListener("submit", function (event) {
            const emailInput = document.querySelector("input[name='email']");
            const passwordInput = document.querySelector("input[name='password']");

            if (!emailInput.value.trim() || !passwordInput.value.trim()) {
                alert("Vui lòng nhập đầy đủ email và mật khẩu!");
                event.preventDefault();
            }
        });
    </script>
</body>

</html>