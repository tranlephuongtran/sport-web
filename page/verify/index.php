<?php
session_start();
if (!isset($_SESSION['verification_email'])) {
    header('Location: index.php?register'); // Quay lại trang đăng ký nếu không có email
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredCode = $_POST['verification_code'];

    if ($enteredCode == $_SESSION['verification_code']) {
        // Mã xác thực đúng, kích hoạt tài khoản
        $email = $_SESSION['verification_email'];

        // Cập nhật trạng thái tài khoản trong cơ sở dữ liệu
        $conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");
        $query = "UPDATE taikhoan SET status = 1 WHERE email = '$email'";
        mysqli_query($conn, $query);

        echo "<script>alert('Tài khoản đã được kích hoạt thành công!'); window.location.href = 'index.php?login';</script>";
        exit;
    } else {
        echo "<script>alert('Mã xác thực không đúng.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực tài khoản - SportWeb</title>
    <script src="https://kit.fontawesome.com/25a68e7dac.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="layout/css/register.css">
</head>

<body>
    <div id="wrapper">
        <form id="form-login" action="" method="post">
            <h1 class="form-heading">Nhập mã xác thực</h1>
            <div class="form-group">
                <i class="fa-solid fa-key"></i>
                <input type="text" class="form-input" placeholder="Mã xác thực" name="verification_code" required>
            </div>
            <input type="submit" value="Xác thực" class="form-submit">
            <a href="index.php" id="link-register">Quay lại trang đăng ký</a>
        </form>
    </div>
    <script src="../../layout/js/jquery-3.7.1.min.js"></script>
    <script src="../../layout/js/eye.js"></script>
</body>

</html>