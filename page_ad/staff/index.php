<?php
session_start();
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Truy vấn danh sách nhân viên
$query = "
    SELECT 
    nv.maNV, 
    nd.ten, 
    nd.sdt, 
    tk.email, 
    tk.password, 
    nv.ngayVaoLam,
    GROUP_CONCAT(r.roleName SEPARATOR ', ') AS roleNames
FROM 
    nhanvien nv
JOIN 
    nguoidung nd ON nv.maNguoiDung = nd.maNguoiDung
JOIN 
    taikhoan tk ON nd.maTK = tk.maTK
LEFT JOIN 
    user_role ur ON nd.maNguoiDung = ur.maNguoiDung
LEFT JOIN 
    role r ON ur.maRole = r.maRole
GROUP BY 
    nv.maNV, nd.ten, nd.sdt, tk.email, tk.password, nv.ngayVaoLam
";
$result = mysqli_query($conn, $query);

// Truy vấn danh sách vai trò (loại trừ vai trò 'Khách hàng')
$roleQuery = "SELECT maRole, roleName FROM role WHERE roleName != 'Khách hàng'";
$roleResult = mysqli_query($conn, $roleQuery); // Sửa từ $query thành $roleQuery

// Lưu dữ liệu vai trò vào mảng để sử dụng nhiều lần
$roles = [];
while ($role = mysqli_fetch_assoc($roleResult)) {
    $roles[] = $role;
}

// Xử lý thêm nhân viên mới
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $ten = mysqli_real_escape_string($conn, $_POST['ten']);
    $sdt = mysqli_real_escape_string($conn, $_POST['sdt']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $maRole = intval($_POST['maRole']);
    $ngayVaoLam = mysqli_real_escape_string($conn, $_POST['ngayVaoLam']);

    if (empty($ten) || empty($sdt) || empty($email) || empty($password) || empty($maRole) || empty($ngayVaoLam)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!');</script>";
    } else {
        $query_tk = "INSERT INTO taikhoan (email, password, maRole) VALUES ('$email', '$password', $maRole)";
        if (mysqli_query($conn, $query_tk)) {
            $maTK = mysqli_insert_id($conn);
            $query_nd = "INSERT INTO nguoidung (ten, sdt, maTK) VALUES ('$ten', '$sdt', $maTK)";
            if (mysqli_query($conn, $query_nd)) {
                $maNguoiDung = mysqli_insert_id($conn);
                $query_nv = "INSERT INTO nhanvien (ngayVaoLam, maNguoiDung) VALUES ('$ngayVaoLam', $maNguoiDung)";
                if (mysqli_query($conn, $query_nv)) {
                    // Thêm vai trò vào bảng user_role
                    $query_ur = "INSERT INTO user_role (maNguoiDung, maRole) VALUES ($maNguoiDung, $maRole)";
                    mysqli_query($conn, $query_ur);
                    echo "<script>alert('Thêm nhân viên thành công!'); window.location.href='index_ad.php?staff';</script>";
                } else {
                    echo "<script>alert('Lỗi khi thêm vào bảng nhanvien!');</script>";
                }
            } else {
                echo "<script>alert('Lỗi khi thêm vào bảng nguoidung!');</script>";
            }
        } else {
            echo "<script>alert('Lỗi khi thêm vào bảng taikhoan!');</script>";
        }
    }
}

// Xử lý cập nhật nhân viên
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $maNV = intval($_POST['maNV']);
    $ten = mysqli_real_escape_string($conn, $_POST['ten']);
    $sdt = mysqli_real_escape_string($conn, $_POST['sdt']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $maRole = intval($_POST['maRole']);
    $ngayVaoLam = mysqli_real_escape_string($conn, $_POST['ngayVaoLam']);

    if (empty($ten) || empty($sdt) || empty($email) || empty($password) || empty($maRole) || empty($ngayVaoLam)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!');</script>";
    } else {
        // Cập nhật thông tin nhân viên
        $query = "UPDATE nhanvien nv
                  JOIN nguoidung nd ON nv.maNguoiDung = nd.maNguoiDung
                  JOIN taikhoan tk ON nd.maTK = tk.maTK
                  SET nd.ten = '$ten', 
                      nd.sdt = '$sdt', 
                      tk.email = '$email', 
                      tk.password = '$password', 
                      tk.maRole = $maRole,
                      nv.ngayVaoLam = '$ngayVaoLam'
                  WHERE nv.maNV = $maNV";

        if (mysqli_query($conn, $query)) {
            // Cập nhật vai trò trong user_role
            $maNguoiDungQuery = "SELECT maNguoiDung FROM nhanvien WHERE maNV = $maNV";
            $maNguoiDungResult = mysqli_query($conn, $maNguoiDungQuery);
            $maNguoiDung = mysqli_fetch_assoc($maNguoiDungResult)['maNguoiDung'];

            // Xóa vai trò cũ và thêm vai trò mới (nếu muốn giữ nhiều vai trò, bỏ đoạn xóa)
            $deleteQuery = "DELETE FROM user_role WHERE maNguoiDung = $maNguoiDung AND maRole != $maRole";
            mysqli_query($conn, $deleteQuery);
            $checkQuery = "SELECT * FROM user_role WHERE maNguoiDung = $maNguoiDung AND maRole = $maRole";
            if (mysqli_num_rows(mysqli_query($conn, $checkQuery)) == 0) {
                $insertQuery = "INSERT INTO user_role (maNguoiDung, maRole) VALUES ($maNguoiDung, $maRole)";
                mysqli_query($conn, $insertQuery);
            }
            echo "<script>alert('Cập nhật nhân viên thành công!'); window.location.href='index_ad.php?staff';</script>";
        } else {
            echo "<script>alert('Lỗi cập nhật nhân viên: " . mysqli_error($conn) . "');</script>";
        }
    }
}

// Xử lý xóa nhân viên
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $maNV = intval($_POST['maNV']);
    $maNguoiDungQuery = "SELECT maNguoiDung FROM nhanvien WHERE maNV = $maNV";
    $maNguoiDungResult = mysqli_query($conn, $maNguoiDungQuery);
    $maNguoiDung = mysqli_fetch_assoc($maNguoiDungResult)['maNguoiDung'];

    // Xóa vai trò từ user_role
    $deleteRoleQuery = "DELETE FROM user_role WHERE maNguoiDung = $maNguoiDung";
    mysqli_query($conn, $deleteRoleQuery);

    // Xóa nhân viên
    $query = "DELETE FROM nhanvien WHERE maNV = $maNV";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Xóa nhân viên thành công!'); window.location.href='index_ad.php?staff';</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa nhân viên: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Nhân Viên</title>
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
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Quản Lý Nhân Viên</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Quản Lý Nhân Viên</h6>
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
                            <h6 class="text-white text-capitalize ps-3">QUẢN LÝ NHÂN VIÊN</h6>
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
                                        <th>Họ Tên</th>
                                        <th>SĐT</th>
                                        <th>Email</th>
                                        <th>Vai Trò</th>
                                        <th>Ngày Vào Làm</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr class="text-center">
                                            <td><?= htmlspecialchars($row['maNV']) ?></td>
                                            <td><?= htmlspecialchars($row['ten']) ?></td>
                                            <td><?= htmlspecialchars($row['sdt']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td><?= htmlspecialchars($row['roleNames'] ?: 'Chưa có vai trò') ?></td>
                                            <td><?= htmlspecialchars($row['ngayVaoLam']) ?></td>
                                            <td>

                                                <button class="btn btn-warning edit-btn btn-icon"
                                                    data-id="<?= $row['maNV'] ?>"
                                                    data-ten="<?= htmlspecialchars($row['ten']) ?>"
                                                    data-sdt="<?= htmlspecialchars($row['sdt']) ?>"
                                                    data-email="<?= htmlspecialchars($row['email']) ?>"
                                                    data-password="<?= htmlspecialchars($row['password']) ?>"
                                                    data-marole="<?= $row['maRole'] ?>"
                                                    data-ngayvaolam="<?= htmlspecialchars($row['ngayVaoLam']) ?>">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="maNV" value="<?= $row['maNV'] ?>">
                                                    <button type="submit" name="delete" class="btn btn-danger btn-icon"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên này?');">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal sửa nhân viên -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Sửa Thông Tin Nhân Viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" id="maNV" name="maNV">
                        <div class="mb-3">
                            <label class="form-label">Họ Tên</label>
                            <input type="text" class="form-control" id="ten" name="ten" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SĐT</label>
                            <input type="text" class="form-control" id="sdt" name="sdt" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật Khẩu</label>
                            <input type="text" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vai Trò</label>
                            <select class="form-control" id="maRole" name="maRole" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['maRole'] ?>"><?= htmlspecialchars($role['roleName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày Vào Làm</label>
                            <input type="date" class="form-control" id="ngayVaoLam" name="ngayVaoLam" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="update" class="btn btn-primary">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thêm nhân viên -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Thêm Nhân Viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="text" name="ten" class="form-control mb-2" placeholder="Họ Tên" required>
                        <input type="text" name="sdt" class="form-control mb-2" placeholder="SĐT" required>
                        <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                        <input type="text" name="password" class="form-control mb-2" placeholder="Mật Khẩu" required>
                        <select name="maRole" class="form-control mb-2" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['maRole'] ?>"><?= htmlspecialchars($role['roleName']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="date" name="ngayVaoLam" class="form-control mb-2" placeholder="Ngày Vào Làm"
                            required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".edit-btn").click(function () {
                $("#maNV").val($(this).data("id"));
                $("#ten").val($(this).data("ten"));
                $("#sdt").val($(this).data("sdt"));
                $("#email").val($(this).data("email"));
                $("#password").val($(this).data("password"));
                $("#maRole").val($(this).data("marole"));
                $("#ngayVaoLam").val($(this).data("ngayvaolam"));
                $("#editModal").modal("show");
            });
        });
    </script>
</body>

</html>
<?php mysqli_close($conn); ?>