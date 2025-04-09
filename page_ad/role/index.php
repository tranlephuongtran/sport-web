<?php
session_start();
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

// Danh sách quyền cố định
$availablePermissions = [
    'Xem chính sách',
    'Thêm chính sách',
    'Sửa chính sách',
    'Xóa chính sách',
    'Xem khuyến mãi',
    'Thêm khuyến mãi',
    'Sửa khuyến mãi',
    'Xem nhân viên',
    'Thêm nhân viên',
    'Sửa nhân viên',
    'Xóa nhân viên',
    'Xem vai trò',
    'Thêm vai trò',
    'Sửa vai trò',
    'Xóa vai trò'
];

// Truy vấn danh sách vai trò
$query = "SELECT maRole, roleName, quyen FROM role WHERE roleName != 'Khách hàng'";
$result = mysqli_query($conn, $query);

// Truy vấn danh sách người dùng không phải Khách hàng
$usersQuery = "
    SELECT DISTINCT nd.maNguoiDung, nd.ten 
    FROM nguoidung nd
    JOIN taikhoan tk ON nd.maTK = tk.maTK
    JOIN role r ON tk.maRole = r.maRole
    WHERE r.roleName != 'Khách hàng'";
$usersResult = mysqli_query($conn, $usersQuery);

// Xử lý thêm vai trò
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $roleName = mysqli_real_escape_string($conn, $_POST['roleName']);
    $permissions = isset($_POST['permissions']) ? implode(',', $_POST['permissions']) : '';
    if (empty($roleName)) {
        echo "<script>alert('Tên vai trò không được để trống!');</script>";
    } else {
        $query = "INSERT INTO role (roleName, quyen) VALUES ('$roleName', '$permissions')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Thêm vai trò thành công!'); window.location.href='index_ad.php?role';</script>";
        } else {
            echo "<script>alert('Lỗi thêm vai trò: " . mysqli_error($conn) . "');</script>";
        }
    }
}

// Xử lý cập nhật vai trò
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $maRole = intval($_POST['maRole']);
    $roleName = mysqli_real_escape_string($conn, $_POST['roleName']);
    $permissions = isset($_POST['permissions']) ? implode(',', $_POST['permissions']) : '';
    if (empty($roleName)) {
        echo "<script>alert('Tên vai trò không được để trống!');</script>";
    } else {
        $query = "UPDATE role SET roleName = '$roleName', quyen = '$permissions' WHERE maRole = $maRole";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Cập nhật vai trò thành công!'); window.location.href='index_ad.php?role';</script>";
        } else {
            echo "<script>alert('Lỗi cập nhật vai trò: " . mysqli_error($conn) . "');</script>";
        }
    }
}

// Xử lý xóa vai trò
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $maRole = intval($_POST['maRole']);
    $query = "DELETE FROM role WHERE maRole = $maRole";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Xóa vai trò thành công!'); window.location.href='index_ad.php?role';</script>";
    } else {
        echo "<script>alert('Lỗi xóa vai trò: " . mysqli_error($conn) . "');</script>";
    }
}

// Xử lý gán vai trò cho người dùng
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign'])) {
    $maRole = intval($_POST['maRole']);
    $maNguoiDung = intval($_POST['maNguoiDung']);
    $checkQuery = "SELECT * FROM user_role WHERE maNguoiDung = $maNguoiDung AND maRole = $maRole";
    $checkResult = mysqli_query($conn, $checkQuery);
    if (mysqli_num_rows($checkResult) == 0) {
        $query = "INSERT INTO user_role (maNguoiDung, maRole) VALUES ($maNguoiDung, $maRole)";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Gán vai trò thành công!'); window.location.href='index_ad.php?role';</script>";
        } else {
            echo "<script>alert('Lỗi gán vai trò: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Người dùng đã được gán vai trò này!');</script>";
    }
}

// Xử lý thu hồi vai trò của người dùng
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['revoke'])) {
    $maRole = intval($_POST['maRole']);
    $maNguoiDung = intval($_POST['maNguoiDung']);
    $checkQuery = "SELECT * FROM user_role WHERE maNguoiDung = $maNguoiDung AND maRole = $maRole";
    $checkResult = mysqli_query($conn, $checkQuery);
    if (mysqli_num_rows($checkResult) > 0) {
        $query = "DELETE FROM user_role WHERE maNguoiDung = $maNguoiDung AND maRole = $maRole";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Thu hồi vai trò thành công!'); window.location.href='index_ad.php?role';</script>";
        } else {
            echo "<script>alert('Lỗi thu hồi vai trò: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Người dùng không có vai trò này để thu hồi!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Vai Trò</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .modal-lg {
            max-width: 90%;
        }

        .btn-icon {
            font-size: 1rem;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a>
                    </li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Quản Lý Vai Trò</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Quản Lý Vai Trò</h6>
            </nav>
        </div>
    </nav>

    <!-- Nội dung chính -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">QUẢN LÝ VAI TRÒ</h6>
                        </div>
                    </div>
                    <div class="card-body px-0">
                        <div class="table-responsive p-3" align="right">
                            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="fa fa-plus"></i> Thêm mới
                            </button>
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th>ID</th>
                                        <th>Tên Vai Trò</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr class='text-center'>
                                                    <td>" . htmlspecialchars($row['maRole']) . "</td>
                                                    <td>" . htmlspecialchars($row['roleName']) . "</td>
                                                    <td>
                                                        <button class='btn btn-warning btn-icon edit-btn me-2' 
                                                                data-id='" . $row['maRole'] . "' 
                                                                data-rolename='" . htmlspecialchars($row['roleName']) . "' 
                                                                data-permissions='" . htmlspecialchars($row['quyen']) . "'>
                                                            <i class='fa fa-edit'></i>
                                                        </button>
                                                        <button class='btn btn-info btn-icon assign-btn me-2' 
                                                                data-id='" . $row['maRole'] . "'>
                                                            <i class='fa fa-user-plus'></i>
                                                        </button>
                                                        <button class='btn btn-secondary btn-icon revoke-btn me-2' 
                                                                data-id='" . $row['maRole'] . "'>
                                                            <i class='fa fa-user-minus'></i>
                                                        </button>
                                                        <form method='POST' style='display:inline;'>
                                                            <input type='hidden' name='maRole' value='" . $row['maRole'] . "'>
                                                            <button type='submit' name='delete' class='btn btn-danger btn-icon' 
                                                                    onclick='return confirm(\"Bạn có chắc chắn muốn xóa vai trò này?\");'>
                                                                <i class='fa fa-trash'></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                  </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='text-muted'>Không có vai trò nào.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thêm vai trò -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Thêm Vai Trò</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="text" name="roleName" class="form-control mb-2" placeholder="Tên Vai Trò" required>
                        <select name="permissions[]" class="form-control mb-2" multiple required>
                            <?php foreach ($availablePermissions as $permission): ?>
                                <option value="<?= $permission ?>"><?= htmlspecialchars($permission) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div><!-- Modal thêm vai trò -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel"><i class="fas fa-plus me-2"></i> Thêm Vai Trò</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Tên Vai Trò</label>
                                        <input type="text" name="roleName" class="form-control"
                                            placeholder="Nhập tên vai trò" required>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label fw-bold">Phân Quyền</label>
                                    <div class="permissions-list">
                                        <?php foreach ($availablePermissions as $permission): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                                    value="<?= $permission ?>">
                                                <label class="form-check-label"><?= $permission ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" name="add" class="btn btn-primary">Thêm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal sửa vai trò -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel"><i class="fas fa-edit me-2"></i> Sửa Vai Trò</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" id="maRole" name="maRole">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Tên Vai Trò</label>
                                        <input type="text" class="form-control" id="roleName" name="roleName" required>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label fw-bold">Phân Quyền</label>
                                    <div class="permissions-list">
                                        <?php foreach ($availablePermissions as $permission): ?>
                                            <div class="form-check">
                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                    name="permissions[]" value="<?= $permission ?>">
                                                <label class="form-check-label"><?= $permission ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" name="update" class="btn btn-primary">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal gán vai trò -->
        <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignModalLabel">Gán Vai Trò</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" id="assignMaRole" name="maRole">
                            <select name="maNguoiDung" class="form-control mb-2" required>
                                <option value="">-- Chọn người dùng --</option>
                                <?php
                                mysqli_data_seek($usersResult, 0);
                                while ($user = mysqli_fetch_assoc($usersResult)): ?>
                                    <option value="<?= $user['maNguoiDung'] ?>"><?= htmlspecialchars($user['ten']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="assign" class="btn btn-primary">Gán</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal thu hồi vai trò -->
        <div class="modal fade" id="revokeModal" tabindex="-1" aria-labelledby="revokeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="revokeModalLabel">Thu Hồi Vai Trò</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" id="revokeMaRole" name="maRole">
                            <select name="maNguoiDung" class="form-control mb-2" required>
                                <option value="">-- Chọn người dùng --</option>
                                <?php
                                mysqli_data_seek($usersResult, 0);
                                while ($user = mysqli_fetch_assoc($usersResult)): ?>
                                    <option value="<?= $user['maNguoiDung'] ?>"><?= htmlspecialchars($user['ten']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="revoke" class="btn btn-primary">Thu Hồi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            $(document).ready(function () {
                // Sửa vai trò
                $(".edit-btn").click(function () {
                    var data = $(this).data();
                    $("#maRole").val(data.id);
                    $("#roleName").val(data.rolename);
                    $("#permissions").val(data.permissions.split(","));
                    $("#editModal").modal("show");
                });

                // Gán vai trò
                $(".assign-btn").click(function () {
                    var maRole = $(this).data('id');
                    $("#assignMaRole").val(maRole);
                    $("#assignModal").modal("show");
                });

                // Thu hồi vai trò
                $(".revoke-btn").click(function () {
                    var maRole = $(this).data('id');
                    $("#revokeMaRole").val(maRole);
                    $("#revokeModal").modal("show");
                });
            });
        </script>
</body>

</html>
<?php mysqli_close($conn); ?>