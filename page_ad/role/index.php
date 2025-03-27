<?php
session_start();
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

if (!isset($_SESSION['maRole']) || $_SESSION['maRole'] != 1) {
    echo "<script>alert('Chỉ Admin mới có quyền truy cập!'); window.location.href='index_ad.php?staff';</script>";
    exit();
}

if (!isset($_GET['maNV'])) {
    die("Không tìm thấy nhân viên.");
}
$maNV = intval($_GET['maNV']);

$query = "SELECT nv.maNV, nd.ten, tk.maTK, r.maRole, r.quyen
          FROM nhanvien nv
          JOIN nguoidung nd ON nv.maNguoiDung = nd.maNguoiDung
          JOIN taikhoan tk ON nd.maTK = tk.maTK
          JOIN role r ON tk.maRole = r.maRole
          WHERE nv.maNV = $maNV";
$result = mysqli_query($conn, $query);
$nhanvien = mysqli_fetch_assoc($result);

if (!$nhanvien) {
    die("Không tìm thấy nhân viên này.");
}

// Danh sách quyền khớp với menu trong index_ad.php
$permissions = [
    'baocao' => ['Xem báo cáo'],
    'hoadon' => ['Xem hóa đơn'],
    'chinhsach' => ['Xem chính sách', 'Thêm chính sách', 'Sửa chính sách', 'Xóa chính sách'],
    'khuyenmai' => ['Xem khuyến mãi', 'Thêm khuyến mãi', 'Sửa khuyến mãi', 'Xóa khuyến mãi'],
    'nhanvien' => ['Xem nhân viên', 'Thêm nhân viên', 'Sửa nhân viên', 'Xóa nhân viên']
];

// Chuẩn hóa quyền hiện tại từ CSDL
$currentPermissions = $nhanvien['quyen'] ? array_map('trim', explode(',', $nhanvien['quyen'])) : [];

// Nếu là Admin (maRole = 1), mặc định tích tất cả quyền trong giao diện
$isAdmin = $nhanvien['maRole'] == 1;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_permissions'])) {
    $selectedPermissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
    $newPermissions = implode(', ', $selectedPermissions);
    $query = "UPDATE role r
              JOIN taikhoan tk ON tk.maRole = r.maRole
              JOIN nguoidung nd ON nd.maTK = tk.maTK
              JOIN nhanvien nv ON nv.maNguoiDung = nd.maNguoiDung
              SET r.quyen = '$newPermissions'
              WHERE nv.maNV = $maNV";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Cập nhật quyền thành công!'); window.location.href='index_ad.php?staff';</script>";
    } else {
        echo "<script>alert('Lỗi khi cập nhật quyền: " . mysqli_error($conn) . "');</script>";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phân Quyền - <?php echo htmlspecialchars($nhanvien['ten']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .form-check {
            margin-right: 20px;
        }

        .permission-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">PHÂN QUYỀN</h6>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <h5>Phân quyền cho: <?php echo htmlspecialchars($nhanvien['ten']); ?></h5>
                        <form method="POST">
                            <?php foreach ($permissions as $module => $actions): ?>
                                <div class="mb-3">
                                    <h6><?php echo ucfirst(htmlspecialchars($module)); ?></h6>
                                    <div class="permission-row">
                                        <?php foreach ($actions as $action): ?>
                                            <?php
                                            // Tích ô nếu quyền có trong $currentPermissions hoặc nhân viên là Admin
                                            $isChecked = $isAdmin || in_array($action, $currentPermissions) ? 'checked' : '';
                                            ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                                    value="<?php echo htmlspecialchars($action); ?>" <?php echo $isChecked; ?>>
                                                <label class="form-check-label"><?php echo htmlspecialchars($action); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <button type="submit" name="update_permissions" class="btn btn-primary">Lưu</button>
                            <a href="index_ad.php?staff" class="btn btn-outline-secondary">Quay lại</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>