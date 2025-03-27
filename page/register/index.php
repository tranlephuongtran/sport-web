<?php
session_start();
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // Kiểm tra dữ liệu
    if (!preg_match("/^[^\s@]+@[^\s@]+\.[^\s@]+$/", $email)) {
        echo "<script>alert('Email không hợp lệ.');setTimeout(function () { window.history.back(); }, 1000);</script>";
        exit;
    }

    if ($password !== $confirmPassword) {
        echo "<script>alert('Mật khẩu không khớp.');setTimeout(function () { window.history.back(); }, 1000);</script>";
        exit;
    }

    // Kiểm tra email và số điện thoại đã tồn tại
    $emailCheck = "SELECT * FROM taikhoan WHERE email = '$email'";
    if (mysqli_num_rows(mysqli_query($conn, $emailCheck)) > 0) {
        echo "<script>alert('Email đã được sử dụng.');setTimeout(function () { window.history.back(); }, 1000);</script>";
        exit;
    }

    $phoneCheck = "SELECT * FROM nguoidung WHERE sdt = '$phone'";
    if (mysqli_num_rows(mysqli_query($conn, $phoneCheck)) > 0) {
        echo "<script>alert('Số điện thoại đã được sử dụng.');setTimeout(function () { window.history.back(); }, 1000);</script>";
        exit;
    }

    // Tạo mã xác thực ngẫu nhiên 6 số
    $verificationCode = rand(100000, 999999);

    // Lưu thông tin tài khoản và mã xác thực vào cơ sở dữ liệu
    $maRole = 3;
    // Lưu thông tin tài khoản và mã xác thực vào cơ sở dữ liệu
    $query = "INSERT INTO taikhoan (email, password, maRole, verification_code) VALUES ('$email', '$password', '$maRole', '$verificationCode')";
    if (mysqli_query($conn, $query)) {
        $maTK = mysqli_insert_id($conn); // Lấy ID của tài khoản vừa tạo

        // Lưu thông tin người dùng vào bảng nguoidung
        $queryNguoiDung = "INSERT INTO nguoidung (maTK, ten, sdt) VALUES ('$maTK', '$name', '$phone')";
        mysqli_query($conn, $queryNguoiDung);

        // Lưu thông tin khách hàng vào bảng khachhang (thêm câu lệnh này)
        $queryKhachHang = "INSERT INTO khachhang (maNguoiDung) VALUES ('$maTK')";
        mysqli_query($conn, $queryKhachHang);

        // Gửi email xác nhận
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'phamquan100503@gmail.com'; // Thay đổi thành email của bạn
            $mail->Password = 'iuea etde iodm jtfi'; // Thay đổi thành password của bạn
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('phamquan100503@gmail.com', 'SportWeb');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Confirm registration - Ace Plus';
            $mail->Body = "
            <h1>Cảm ơn bạn đã đăng ký!</h1>
            <p>Email: <strong>$email</strong></p>
            <p>Mật khẩu: <strong>$password</strong></p>
            <p>Mã xác thực của bạn là: <strong>$verificationCode</strong></p>
            <p>Bạn cần nhập mã xác thực này để kích hoạt tài khoản của mình.</p>
        ";

            $mail->send();

            // Lưu mã xác thực vào session
            $_SESSION['verification_email'] = $email;
            $_SESSION['verification_code'] = $verificationCode;

            // Redirect đến form xác thực
            echo "<script>
                alert('Đăng ký thành công! Vui lòng nhập mã xác thực để kích hoạt tài khoản.');
                setTimeout(function() {
                    window.location.href = 'index.php?verify';
                }); 
            </script>";
            exit;

        } catch (Exception $e) {
            echo "<script>alert('Không thể gửi email: {$mail->ErrorInfo}');</script>";
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