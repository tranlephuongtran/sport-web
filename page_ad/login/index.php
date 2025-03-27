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
session_start();
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Chuyển hướng nếu đã đăng nhập
if (isset($_SESSION['login_ad'])) {
    header("Location: index_ad.php?dashboard");
    exit();
}

if (isset($_POST['btnLogin_ad'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Truy vấn lấy thông tin người dùng bao gồm maRole và quyen
    $query = "SELECT tk.maTK, nd.maNguoiDung, nv.maNV, tk.password, r.maRole, r.quyen 
              FROM taikhoan tk
              JOIN nguoidung nd ON tk.maTK = nd.maTK 
              JOIN nhanvien nv ON nd.maNguoiDung = nv.maNguoiDung 
              JOIN role r ON tk.maRole = r.maRole
              WHERE tk.email = '$email'";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo '<script>alert("Lỗi truy vấn cơ sở dữ liệu!");</script>';
    } elseif ($user = mysqli_fetch_assoc($result)) {
        // Kiểm tra mật khẩu
        if ($password === $user['password']) { // Nên thay bằng password_verify nếu mã hóa
            // Kiểm tra vai trò (chỉ cho phép Admin hoặc Nhân viên)
            if ($user['maRole'] == 1 || $user['maRole'] == 2) { // 1 = Admin, 2 = Nhân viên
                $_SESSION['login_ad'] = $user['maNguoiDung'];
                $_SESSION['maRole'] = $user['maRole'];
                $_SESSION['maNV'] = $user['maNV'];
                $_SESSION['quyen'] = $user['quyen']; // Lưu quyền vào session
                echo '<script>
                    alert("Đăng nhập thành công");
                    window.location.href = "index_ad.php?dashboard";
                </script>';
            } else {
                echo '<script>alert("Bạn không có quyền truy cập khu vực quản trị!");</script>';
            }
        } else {
            echo '<script>alert("Sai mật khẩu. Vui lòng nhập lại!");</script>';
        }
    } else {
        echo '<script>alert("Email không tồn tại hoặc không phải nhân viên!");</script>';
    }

    mysqli_close($conn);
}
?>

<body>
    <div id="wrapper">
        <form id="form-login" action="" method="POST">
            <h1 class="form-heading">Đăng Nhập ADMIN</h1>
            <div class="form-group">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" class="form-input" placeholder="Email" name="email" required>
            </div>
            <div class="form-group">
                <i class="fa-solid fa-key"></i>
                <input type="password" class="form-input" placeholder="Mật khẩu" name="password" required>
            </div>
            <input type="submit" name="btnLogin_ad" value="Đăng nhập" class="form-submit">
        </form>
    </div>
    <script src="../../layout/js/jquery-3.7.1.min.js"></script>
    <script>
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