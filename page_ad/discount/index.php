<?php
session_start();
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Lấy danh sách quyền từ session
$currentPermissions = [];
if (isset($_SESSION['quyen'])) {
    if (is_array($_SESSION['quyen'])) {
        $currentPermissions = $_SESSION['quyen'];
    } elseif (is_string($_SESSION['quyen'])) {
        $currentPermissions = array_map('trim', explode(',', $_SESSION['quyen']));
    }
}
// Kiểm tra quyền truy cập trang
if (!in_array('Xem khuyến mãi', $currentPermissions) && (!isset($_SESSION['maRole']) || $_SESSION['maRole'] != 1)) {
    echo "<script>alert('Bạn không có quyền truy cập trang này!'); window.location.href='index_ad.php?dashboard';</script>";
    exit();
}

// Xử lý cập nhật trạng thái AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['status'])) {
    $id = intval($_POST['id']);
    $status = intval($_POST['status']);

    $query = "UPDATE khuyenmai SET trangThai = ? WHERE maKM = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $status, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Lỗi SQL"]);
    }
    exit;
}

// Xử lý thêm khuyến mãi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    if (!in_array('Thêm khuyến mãi', $currentPermissions) && (!isset($_SESSION['maRole']) || $_SESSION['maRole'] != 1)) {
        echo "<script>alert('Bạn không có quyền thêm khuyến mãi!'); window.location.href='index_ad.php?discount';</script>";
        exit();
    }

    $tenKM = mysqli_real_escape_string($conn, $_POST['tenKM']);
    $noiDung = mysqli_real_escape_string($conn, $_POST['noiDungChuongTrinh']);
    $giaGiam = intval($_POST['giaGiam']);
    $loaiGiamGia = mysqli_real_escape_string($conn, $_POST['loaiGiamGia']);

    if ($giaGiam < 0) {
        echo "<script>alert('Giá giảm phải lớn hơn hoặc bằng 0!');</script>";
    } else {
        $query = "INSERT INTO khuyenmai (tenKM, noiDungChuongTrinh, giaGiam, loaiGiamGia, trangThai) 
                  VALUES ('$tenKM', '$noiDung', $giaGiam, '$loaiGiamGia', 1)";

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Thêm khuyến mãi thành công!'); window.location.href='index_ad.php?discount';</script>";
        } else {
            echo "<script>alert('Lỗi thêm khuyến mãi!');</script>";
        }
    }
}

// Xử lý cập nhật khuyến mãi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    if (!in_array('Sửa khuyến mãi', $currentPermissions) && (!isset($_SESSION['maRole']) || $_SESSION['maRole'] != 1)) {
        echo "<script>alert('Bạn không có quyền sửa khuyến mãi!'); window.location.href='index_ad.php?discount';</script>";
        exit();
    }

    $maKM = intval($_POST['maKM']);
    $tenKM = mysqli_real_escape_string($conn, $_POST['tenKM']);
    $noiDung = mysqli_real_escape_string($conn, $_POST['noiDungChuongTrinh']);
    $giaGiam = intval($_POST['giaGiam']);
    $loaiGiamGia = mysqli_real_escape_string($conn, $_POST['loaiGiamGia']);

    if ($giaGiam < 0) {
        echo "<script>alert('Giá giảm phải lớn hơn hoặc bằng 0!');</script>";
    } else {
        $query = "UPDATE khuyenmai 
                  SET tenKM = '$tenKM', 
                      noiDungChuongTrinh = '$noiDung', 
                      giaGiam = $giaGiam, 
                      loaiGiamGia = '$loaiGiamGia' 
                  WHERE maKM = $maKM";

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Cập nhật khuyến mãi thành công!'); window.location.href='index_ad.php?discount';</script>";
        } else {
            echo "<script>alert('Lỗi cập nhật!');</script>";
        }
    }
}

// Truy vấn danh sách khuyến mãi
$query = "SELECT * FROM khuyenmai";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Khuyến Mãi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 34px;
            height: 20px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 20px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #28a745;
        }

        input:checked+.slider:before {
            transform: translateX(14px);
        }

        .btn-icon i {
            font-size: 1rem;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
        navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a>
                    </li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Khuyến mãi</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Khuyến Mãi</h6>
            </nav>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">QUẢN LÝ KHUYẾN MÃI</h6>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-3" align="right">
                            <?php if (in_array('Thêm khuyến mãi', $currentPermissions) || (isset($_SESSION['maRole']) && $_SESSION['maRole'] == 1)): ?>
                                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="fa fa-add"></i> Thêm mới
                                </button>
                            <?php endif; ?>
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th
                                            class="text-uppercase text-secondary font-weight-bolder opacity-75 text-start">
                                            TÊN</th>
                                        <th
                                            class="text-uppercase text-secondary font-weight-bolder opacity-75 text-start">
                                            NỘI DUNG</th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-75">LOẠI
                                            GIẢM GIÁ</th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-75">GIÁ GIẢM
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-75">TRẠNG
                                            THÁI</th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-75"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $checked = $row['trangThai'] == 1 ? "checked" : "";
                                            echo "<tr class='text-center'>
                                                    <td class='text-start'>" . htmlspecialchars($row['tenKM']) . "</td>
                                                    <td class='text-start text-wrap' style='max-width: 250px;'>" . htmlspecialchars($row['noiDungChuongTrinh']) . "</td>
                                                    <td style='max-width: 250px;'>" . htmlspecialchars($row['loaiGiamGia']) . "</td>
                                                    <td class='text-primary fw-bold'>"
                                                . number_format($row['giaGiam'], 0, ',', '.') .
                                                ($row['loaiGiamGia'] == 'Tiền' ? ' VND' : '%') .
                                                "</td>
                                                    <td>
                                                        <label class='switch'>
                                                            <input type='checkbox' class='toggle-status' data-id='{$row['maKM']}' $checked>
                                                            <span class='slider'></span>
                                                        </label>
                                                    </td>
                                                    <td>";
                                            if (in_array('Sửa khuyến mãi', $currentPermissions) || (isset($_SESSION['maRole']) && $_SESSION['maRole'] == 1)) {
                                                echo "<a data-toggle='modal' data-target='#editModal' class='btn btn-warning edit-btn btn-icon' 
                                                        data-id='{$row['maKM']}' 
                                                        data-tenkm='" . htmlspecialchars($row['tenKM']) . "' 
                                                        data-noidung='" . htmlspecialchars($row['noiDungChuongTrinh']) . "' 
                                                        data-loaigiamgia='" . htmlspecialchars($row['loaiGiamGia']) . "'
                                                        data-giagiam='{$row['giaGiam']}'>
                                                        <i class='fa fa-edit'></i>
                                                      </a>";
                                            }
                                            echo "</td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-muted'>Không có khuyến mãi nào.</td></tr>";
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

    <!-- Modal sửa khuyến mãi -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Sửa Khuyến Mãi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="maKM" name="maKM">
                        <div class="mb-3">
                            <label class="form-label">Tên Khuyến Mãi</label>
                            <input type="text" class="form-control" id="tenKM" name="tenKM" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nội Dung</label>
                            <textarea class="form-control" id="noiDungChuongTrinh" name="noiDungChuongTrinh"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Loại Giảm Giá</label>
                            <input type="text" class="form-control" id="loaiGiamGia" name="loaiGiamGia" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá Giảm</label>
                            <input type="number" class="form-control" id="giaGiam" name="giaGiam" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="update">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thêm khuyến mãi -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Thêm Khuyến Mãi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="text" name="tenKM" class="form-control mb-2" placeholder="Tên" required>
                        <textarea name="noiDungChuongTrinh" class="form-control mb-2" placeholder="Nội dung"
                            required></textarea>
                        <select name="loaiGiamGia" class="form-control mb-2" required>
                            <option value="" disabled selected>Chọn loại giảm giá</option>
                            <option value="Tiền">Tiền (VND)</option>
                            <option value="Phần trăm">Phần trăm (%)</option>
                        </select>
                        <input type="number" name="giaGiam" class="form-control mb-2" placeholder="Giá giảm" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Thư viện -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".toggle-status").on("change", function () {
                var maKM = $(this).data("id");
                var trangThai = $(this).is(":checked") ? 1 : 0;
                var btn = $(this);

                $.ajax({
                    url: "",
                    type: "POST",
                    data: { id: maKM, status: trangThai },
                    success: function (response) {
                        var res = JSON.parse(response);
                        if (!res.success) {
                            alert("Lỗi cập nhật trạng thái!");
                            btn.prop("checked", !trangThai);
                        }
                    },
                    error: function () {
                        alert("Lỗi kết nối server!");
                        btn.prop("checked", !trangThai);
                    }
                });
            });

            $(".edit-btn").click(function () {
                var data = $(this).data();
                console.log("Dữ liệu modal:", data);

                $("#maKM").val(data.id);
                $("#tenKM").val(data.tenkm);
                $("#noiDungChuongTrinh").val(data.noidung);
                $("#loaiGiamGia").val(data.loaigiamgia);
                $("#giaGiam").val(data.giagiam).prop("readonly", false);

                $("#editModal").modal("show");
            });
        });
    </script>
</body>

</html>
<?php mysqli_close($conn); ?>