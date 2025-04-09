<?php include("../layout/header.php");
if (!isset($_GET['profile'])) {
    $profile = 1;
} else {
    $profile = $_GET['profile'];
}
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");
$user = $_SESSION['login'];
$str = "SELECT * FROM taikhoan tk INNER JOIN nguoidung nd ON tk.maTK = nd.maTK WHERE maNguoiDung = $user";
$result = $conn->query($str);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $ten = $row['ten'];
        $sdt = $row['sdt'];
        $email = $row['email'];
        $password = $row['password'];
    }
}
?>

<?php include("../layout/header.php"); ?>
<style>
    .account-sidebar {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
    }

    .nav-pills .nav-item {
        margin-bottom: 20px;
        /* Tăng khoảng cách giữa các item */
    }

    .nav-pills .nav-link {
        width: 270px;
        border-radius: 8px;
        font-size: 18px;
        padding: 16px 24px;
        color: #6c757d;
        background: #e9ecef;
        transition: all 0.3s ease;
        font-weight: 300;
        text-align: left;
    }

    .nav-pills .nav-link:hover {
        background-color: #007bff;
        color: white;
    }

    .nav-pills .nav-link.active {
        background-color: #007bff;
        color: white;
        font-weight: bold;
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
    }

    .nav-pills {
        padding-bottom: 20px;
        margin-top: 100px;
        margin-left: 70px;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        background: #ffffff;
        padding: 30px;
        margin-top: 20px;
    }

    .form-control {
        border-radius: 10px;
        height: 45px;
        font-size: 16px;
        color: #000;
        padding-left: 15px;
    }

    .btn-custom {
        width: 100%;
        border-radius: 10px;
        padding: 14px;
        font-size: 18px;
        transition: all 0.3s ease;
    }

    .btn-custom:hover {
        background-color: #0056b3;
    }

    .tab-content {
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .account-info {
        font-size: 18px;
        color: #444;
        line-height: 1.8;
        padding: 20px 0;
    }

    .account-info span {
        color: #007bff;
    }

    h3 {
        font-weight: 300;
        color: #333;
    }

    .account-sidebar {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
    }

    .account-sidebar h4 {
        color: #343a40;
        font-weight: 300;
    }
</style>

<script>
    function showTab(tab) {
        document.querySelectorAll('.tab-content').forEach(item => item.style.display = 'none');
        document.getElementById(tab).style.display = 'block';

        document.querySelectorAll('.nav-link').forEach(item => item.classList.remove('active'));
        document.querySelector(`button[data-tab="${tab}"]`).classList.add('active');
    }
</script>
<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center py-5" style="max-width: 900px;">
        <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Quản lý tài khoản</h4>
    </div>
</div>
<!-- Header End -->
<div class="container account-container">
    <div class="row">
        <!-- Sidebar bên trái -->
        <div class="col-md-4 account-sidebar">
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <button class="nav-link active" data-tab="current-info" onclick="showTab('current-info')">📋 Thông
                        tin tài khoản</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-tab="update-info" onclick="showTab('update-info')">✏️ Cập nhật thông
                        tin</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-tab="change-password" onclick="showTab('change-password')">🔒 Đổi mật
                        khẩu</button>
                </li>
            </ul>
        </div>

        <!-- Nội dung chính -->
        <div class="col-md-8">
            <div class="card">
                <!-- Hiển thị thông tin hiện tại -->
                <div id="current-info" class="tab-content" style="margin-left: 270px;">
                    <h3 class="mb-3">📋 Thông tin tài khoản</h3>
                    <div class="account-info">
                        <p><strong>Email: </strong> <span><?= $email ?></span></p>
                        <p><strong>Tên của bạn:</strong> <span><?= $ten ?></span></p>
                        <p><strong>Số điện thoại:</strong> <span><?= $sdt ?></span></p>
                        <p><strong>Trạng thái tài khoản:</strong> <span style="color: green;">Hoạt động</span></p>
                    </div>
                </div>

                <!-- Cập nhật thông tin -->
                <div id="update-info" class="tab-content" style="display: none;">
                    <h3 class="mb-3">✏️ Cập nhật thông tin</h3>
                    <form method="POST" action="">
                        <div class="mb-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= $email ?>" name="email" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Tên của bạn</label>
                            <input type="text" class="form-control" value="<?= $ten ?>" name="name" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" value="<?= $sdt ?>" name="phone" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-custom" name="btn-save-profile">💾 Lưu thay
                            đổi</button>
                    </form>

                </div>

                <!-- Đổi mật khẩu -->
                <div id="change-password" class="tab-content" style="display: none;">
                    <h3 class="mb-3">🔒 Đổi mật khẩu</h3>
                    <form method="POST" action="">
                        <div class="mb-4">
                            <label class="form-label">Mật khẩu cũ</label>
                            <input type="password" class="form-control" name="old_password" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" name="new_password" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-custom" name="btn-change-password">🔒 Đổi mật
                            khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="margin-bottom: 150px;"></div>
<?php include("../layout/footer.php");
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn-save-profile'])) {
    $new_email = $_POST['email'];
    $new_name = $_POST['name'];
    $new_phone = $_POST['phone'];

    // Cập nhật bảng taikhoan (chỉ cập nhật email)
    $sql1 = "UPDATE taikhoan SET email = '$new_email' WHERE maTK = (SELECT maTK FROM nguoidung WHERE maNguoiDung = $user)";
    mysqli_query($conn, $sql1);

    // Cập nhật bảng nguoidung (cập nhật tên và số điện thoại)
    $sql2 = "UPDATE nguoidung SET ten = '$new_name', sdt = '$new_phone' WHERE maNguoiDung = $user";
    mysqli_query($conn, $sql2);

    echo "<script>alert('Cập nhật thông tin thành công!'); window.location.href = 'index.php?profile'</script>";
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn-change-password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];


    if ($old_password !== $password) {
        echo "<script>alert('Mật khẩu cũ không chính xác!');</script>";
    } else if (!preg_match($pattern = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&+=!]).{8,}$/", $new_password)) {
        echo "<script>alert('Mật khẩu mới phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt!');</script>";
    } else if ($new_password !== $confirm_password) {
        echo "<script>alert('Mật khẩu xác nhận không khớp!');</script>";
    } else {
        // Cập nhật mật khẩu mới vào CSDL
        $update_sql = "UPDATE taikhoan SET password = '$new_password' WHERE maTK = (SELECT maTK FROM nguoidung WHERE maNguoiDung = $user)";
        if (mysqli_query($conn, $update_sql)) {
            echo "<script>alert('Đổi mật khẩu thành công!'); window.location.href='index.php?profile';</script>";
        } else {
            echo "<script>alert('Lỗi khi đổi mật khẩu, vui lòng thử lại!');</script>";
        }
    }

    // Đóng kết nối
    mysqli_close($conn);
}
?>