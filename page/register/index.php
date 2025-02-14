<?php
session_start();
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // Email validation
    if (!preg_match("/^[^\s@]+@[^\s@]+\.[^\s@]+$/", $email)) {
        echo "<script>alert('Email không hợp lệ.');setTimeout(function () {
            window.history.back(); }, 1000);</script>";
        exit;
    }

    // Password validation
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
        echo "<script>alert('Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.');setTimeout(function () {
            window.history.back(); }, 1000);</script>";
        exit;
    }

    // Confirm password check
    if ($password !== $confirmPassword) {
        echo "<script>alert('Mật khẩu không khớp.');setTimeout(function () {
            window.history.back(); }, 1000);</script>";
        exit;
    }

    // Phone number validation
    if (!preg_match("/^(03|07|09|08)\d{8}$/", $phone)) {
        echo "<script>alert('Số điện thoại không hợp lệ.');setTimeout(function () {
            window.history.back(); }, 1000);</script>";
        exit;
    }

    // Check for existing email
    $emailCheck = "SELECT * FROM taikhoan WHERE email = '$email'";
    $emailResult = mysqli_query($conn, $emailCheck);
    if (mysqli_num_rows($emailResult) > 0) {
        echo "<script>alert('Email đã được sử dụng.');setTimeout(function () {
            window.history.back(); }, 1000);</script>";
        exit;
    }

    // Check for existing phone number
    $phoneCheck = "SELECT * FROM nguoidung WHERE sdt = '$phone'";
    $phoneResult = mysqli_query($conn, $phoneCheck);
    if (mysqli_num_rows($phoneResult) > 0) {
        echo "<script>alert('Số điện thoại đã được sử dụng.');setTimeout(function () {
            window.history.back(); }, 1000);</script>";
        exit;
    }

    $maRole = 3;

    $query = "INSERT INTO taikhoan (email, password, maRole) VALUES ('$email', '$password', '$maRole')";
    if (mysqli_query($conn, $query)) {
        $maTK = mysqli_insert_id($conn);

        $queryNguoiDung = "INSERT INTO nguoidung (maTK, ten, sdt) VALUES ('$maTK', '$name', '$phone')";
        if (mysqli_query($conn, $queryNguoiDung)) {
            $maNguoiDung = mysqli_insert_id($conn);
            $queryKhachHang = "INSERT INTO khachhang (maNguoiDung) VALUES ('$maNguoiDung')";
            mysqli_query($conn, $queryKhachHang);
            echo "<script>alert('Đăng ký thành công!');
            window.location.href = 'index.php?login'</script>";
            exit;
        } else {
            echo "<script>alert('Có lỗi xảy ra khi thêm người dùng: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Có lỗi xảy ra khi đăng ký: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký SportWeb</title>
    <script src="https://kit.fontawesome.com/25a68e7dac.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="layout/css/register.css">
</head>

<body>
    <div id="wrapper">
        <form id="form-login" action="" method="post" onsubmit="return validateForm()">
            <h1 class="form-heading">Đăng Ký</h1>
            <div class="form-group">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" class="form-input" placeholder="Email" name="email" required>
            </div>
            <div class="form-group">
                <i class="fa-solid fa-key"></i>
                <input type="password" class="form-input" placeholder="Mật khẩu" name="password" required>
            </div>
            <div class="form-group">
                <i class="fa-solid fa-key"></i>
                <input type="password" class="form-input" placeholder="Nhập lại mật khẩu" name="confirmPassword"
                    required>
            </div>
            <div class="form-group">
                <i class="fa-regular fa-user"></i>
                <input type="text" class="form-input" placeholder="Tên của bạn" name="name" required>
            </div>
            <div class="form-group">
                <i class="fa-solid fa-phone"></i>
                <input type="tel" class="form-input" placeholder="Số điện thoại" name="phone" required>
            </div>
            <input type="checkbox" id="agreePolicy" style="width: auto; margin-right: 10px;" required>
            <label for="agreePolicy">Tôi đồng ý với <a href="index.php?rule" target="_blank">chính sách và điều
                    khoản</a>.</label><br>
            <a href="index.php?login" id="link-register">Đã có tài khoản!</a>
            <input type="submit" value="Đăng ký" class="form-submit">
        </form>
    </div>
    <script src="../../layout/js/jquery-3.7.1.min.js"></script>
    <script src="../../layout/js/eye.js"></script>
</body>

</html>