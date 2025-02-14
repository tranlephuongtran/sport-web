<?php

$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
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

// Truy vấn danh sách khuyến mãi
$query = "SELECT * FROM khuyenmai";
$result = mysqli_query($conn, $query);
// Xử lý cập nhật khuyến mãi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $maKM = intval($_POST['maKM']);
    $tenKM = mysqli_real_escape_string($conn, $_POST['tenKM']);
    $noiDung = mysqli_real_escape_string($conn, $_POST['noiDungChuongTrinh']);
    $giaGiam = intval($_POST['giaGiam']);
    $loaiGiamGia = mysqli_real_escape_string($conn, $_POST['loaiGiamGia']);

    // Kiểm tra điều kiện giá giảm
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
            echo "<script>alert('Cập nhật thành công!');</script>";
            header("Location: index_ad.php?discount");
            exit(); // Load lại trang
        } else {
            echo "<script>alert('Lỗi cập nhật!');</script>";
        }
    }
}
/// Xử lý thêm khuyến mãi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $tenKM = mysqli_real_escape_string($conn, $_POST['tenKM']);
    $noiDung = mysqli_real_escape_string($conn, $_POST['noiDungChuongTrinh']);
    $giaGiam = intval($_POST['giaGiam']);
    $loaiGiamGia = mysqli_real_escape_string($conn, $_POST['loaiGiamGia']);

    // Kiểm tra điều kiện giá giảm
    if ($giaGiam < 0) {
        echo "<script>alert('Giá giảm phải lớn hơn hoặc bằng 0!');</script>";
    } else {
        $query = "INSERT INTO khuyenmai (tenKM, noiDungChuongTrinh, giaGiam, loaiGiamGia, trangThai) 
                  VALUES ('$tenKM', '$noiDung', $giaGiam, 'Tiền', 1)";

        if (mysqli_query($conn, $query)) {
            header("Location: index_ad.php?discount");
            exit();
        } else {
            echo "<script>alert('Lỗi thêm khuyến mãi!');</script>";
        }
    }
}
// Xử lý xóa khuyến mãi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $maKM = intval($_POST['maKM']);
    $query = "DELETE FROM khuyenmai WHERE maKM = $maKM";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Xóa khuyến mãi thành công!');</script>";
        header("Location: index_ad.php?discount");
        exit();
    } else {
        echo "<script>alert('Lỗi khi xóa khuyến mãi!');</script>";
    }
}
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
        /* CSS cho công tắc bật/tắt */
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

    <!-- End Navbar -->
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

                        <div class="table-responsive p-3">
                            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal"><i
                                    class='fa fa-add'></i>Thêm
                            </button>
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th
                                            class="text-uppercase text-secondary font-weight-bolder opacity-75 text-start">
                                            TÊN</th>
                                        <th
                                            class="text-uppercase text-secondary font-weight-bolder opacity-75 text-start">
                                            NỘI DUNG</th>

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
                                                    <td class='text-start'>{$row['tenKM']}</td>
                                                    <td class='text-start text-wrap' style='max-width: 250px;'>{$row['noiDungChuongTrinh']}</td>
                                                    
                                                   <td class='text-primary fw-bold'>" . number_format($row['giaGiam'], 0, ',', '.') . " VND</td>
                                                    <td>
                                                        <label class='switch'>
                                                            <input type='checkbox' class='toggle-status' data-id='{$row['maKM']}' $checked>
                                                            <span class='slider'></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <a data-toggle='modal'
                                                    data-target='#editModal'
                                                    class='btn btn-sm btn-warning edit-btn' 
                                                        data-id='{$row['maKM']}' 
                                                        data-tenkm='{$row['tenKM']}' 
                                                        data-noidung='{$row['noiDungChuongTrinh']}' 
                                                        data-loaigiamgia='{$row['loaiGiamGia']}'
                                                        data-giagiam='{$row['giaGiam']}'>
                                                        <i class='fa fa-edit'></i>
                                                        </a>
                                                        <form method='POST' style='display:inline;'>
                                                            <input type='hidden' name='maKM' value='{$row['maKM']}''>
                                                            <button type='submit' name='delete' class='btn btn-danger btn-sm' onclick='return confirm(\"Bạn có chắc chắn muốn xóa khuyến mãi này?\");'>
                                                                <i class='fa fa-trash'></i>
                                                            </button>
                                                        </form>
                                                    </td>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        });
        $(document).ready(function () {
            $(".edit-btn").click(function () {
                $("#maKM").val($(this).data("id"));
                $("#tenKM").val($(this).data("tenkm"));
                $("#noiDung").val($(this).data("noidung"));
                $("#loaiGiamGia").val($(this).data("loaigiamgia"));
                $("#giaGiam").val($(this).data("giagiam"));
                $("#editModal").modal("show");
            });
        });
    </script>
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
                            <textarea class="form-control" id="noiDung" name="noiDung" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá Giảm</label>
                            <input type="number" class="form-control" id="giaGiam" name="giaGiam" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Loại Giảm Giá</label>
                            <input type="text" class="form-control" id="loaiGiamGia" name="loaiGiamGia" readonly>
                        </div>

                        <button type="submit" class="btn btn-primary" name="update">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal thêm khuyến mãi -->
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Khuyến Mãi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="text" name="tenKM" class="form-control mb-2" placeholder="Tên" required>
                        <textarea name="noiDung" class="form-control mb-2" placeholder="Nội dung" required></textarea>
                        <input type="number" name="giaGiam" class="form-control mb-2" placeholder="Giá giảm" required>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Thư viện FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>