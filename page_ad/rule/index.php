<?php

$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Truy vấn danh sách khuyến mãi
$query = "SELECT * FROM chinhsach";
$result = mysqli_query($conn, $query);

// Xử lý cập nhật khuyến mãi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $maChinhSach = intval($_POST['maChinhSach']);
    $ten = mysqli_real_escape_string($conn, $_POST['ten']);
    $noiDung = mysqli_real_escape_string($conn, nl2br($_POST['noiDung'])); // Sử dụng nl2br

    $query = "UPDATE chinhsach 
              SET ten = '$ten', 
                  noiDung = '$noiDung'
              WHERE maChinhSach = $maChinhSach";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Cập nhật thành công!');</script>";
        header("Location: index_ad.php?rule");
        exit(); // Load lại trang
    } else {
        echo "<script>alert('Lỗi cập nhật!');</script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $ten = mysqli_real_escape_string($conn, $_POST['ten']);
    $noiDung = mysqli_real_escape_string($conn, nl2br($_POST['noiDung'])); // Sử dụng nl2br

    $query = "INSERT INTO chinhsach (ten, noiDung) 
              VALUES ('$ten', '$noiDung')";

    if (mysqli_query($conn, $query)) {
        header("Location: index_ad.php?rule");
        exit();
    } else {
        echo "<script>alert('Lỗi thêm chính sách!');</script>";
    }
}

// Xử lý xóa khuyến mãi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $maChinhSach = intval($_POST['maChinhSach']);
    $query = "DELETE FROM chinhsach WHERE maChinhSach = $maChinhSach";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Xóa chính sách thành công!');</script>";
        header("Location: index_ad.php?rule");
        exit();
    } else {
        echo "<script>alert('Lỗi khi xóa chính sách!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Chính Sách</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
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

        .policy-content {
            max-width: 800px;
            max-height: 200px;
            overflow-y: auto;
            overflow-wrap: break-word;
            word-wrap: break-word;
            white-space: normal;
            line-height: 1.5;
            text-align: justify;
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
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Chính Sách</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Chính Sách</h6>
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
                            <h6 class="text-white text-capitalize ps-3">QUẢN LÝ CHÍNH SÁCH</h6>
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
                                        <th style="width: 5%;"
                                            class="text-uppercase text-secondary font-weight-bolder opacity-75 text-start">
                                            TÊN CHÍNH SÁCH</th>
                                        <th style="width: 95%;"
                                            class="text-uppercase text-secondary font-weight-bolder opacity-75 text-start">
                                            NỘI DUNG CHÍNH SÁCH</th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-75"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr class='text-center'>
                                                    <td class='text-start'><h6>{$row['ten']}</h6></td>
                                                    <td class='text-start text-wrap policy-content'>{$row['noiDung']}</td>
                                                    <td>
                                                        <a data-toggle='modal'
                                                           data-target='#editModal'
                                                           class='btn btn-sm btn-warning edit-btn' 
                                                           data-id='{$row['maChinhSach']}' 
                                                           data-ten='{$row['ten']}' 
                                                           data-noidung='{$row['noiDung']}'>
                                                           <i class='fa fa-edit'></i>
                                                        </a>
                                                        <form method='POST' style='display:inline;'>
                                                            <input type='hidden' name='maChinhSach' value='{$row['maChinhSach']}'>
                                                            <button type='submit' name='delete' class='btn btn-danger btn-sm' onclick='return confirm(\"Bạn có chắc chắn muốn xóa Chính sách này?\");'>
                                                                <i class='fa fa-trash'></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-muted'>Không có chính sách nào.</td></tr>";
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
            $(".edit-btn").click(function () {
                $("#maChinhSach").val($(this).data("id"));
                $("#ten").val($(this).data("ten"));
                $("#noiDung").val($(this).data("noidung"));
                $("#editModal").modal("show");
            });
        });
    </script>

    <!-- Modal sửa chính sách -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Sửa Chính Sách</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="maChinhSach" name="maChinhSach">
                        <div class="mb-3">
                            <label class="form-label">Tên Chính Sách</label>
                            <input type="text" class="form-control" id="ten" name="ten" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nội Dung</label>
                            <textarea class="form-control" id="noiDung" name="noiDung" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" name="update">Lưu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thêm chính sách -->
    <div class="modal fade" id="addModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Chính Sách</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="text" name="ten" class="form-control mb-2" placeholder="Tên" required>
                        <textarea name="noiDung" class="form-control mb-2" placeholder="Nội dung" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add" class="btn btn-primary">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>