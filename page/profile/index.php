<?php include("../layout/header.php");
if (!isset($_GET['profile'])) {
    $profile = 1;
} else {
    $profile = $_GET['profile'];
} ?>

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
                        <p><strong>Tên đăng nhập:</strong> <span>username123</span></p>
                        <p><strong>Email:</strong> <span>email@example.com</span></p>
                        <p><strong>Tên của bạn:</strong> <span>Nguyễn Văn A</span></p>
                        <p><strong>Số điện thoại:</strong> <span>0987654321</span></p>
                        <p><strong>Ngày tham gia:</strong> <span>01/01/2024</span></p>
                        <p><strong>Trạng thái tài khoản:</strong> <span style="color: green;">Hoạt động</span></p>
                    </div>
                </div>

                <!-- Cập nhật thông tin -->
                <div id="update-info" class="tab-content" style="display: none;">
                    <h3 class="mb-3">✏️ Cập nhật thông tin</h3>
                    <form>
                        <div class="mb-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="email@example.com">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Tên của bạn</label>
                            <input type="text" class="form-control" value="Nguyễn Văn A">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" value="0987654321">
                        </div>
                        <button type="submit" class="btn btn-primary btn-custom">💾 Lưu thay đổi</button>
                    </form>
                </div>

                <!-- Đổi mật khẩu -->
                <div id="change-password" class="tab-content" style="display: none;">
                    <h3 class="mb-3">🔒 Đổi mật khẩu</h3>
                    <form>
                        <div class="mb-4">
                            <label class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary btn-custom">🔄 Cập nhật mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="margin-bottom: 150px;"></div>
<?php include("../layout/footer.php"); ?>