<?php
session_start();
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");
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
if (!in_array('Xem chính sách', $currentPermissions) && (!isset($_SESSION['maRole']) || $_SESSION['maRole'] != 1)) {
    echo "<script>alert('Bạn không có quyền truy cập trang này!'); window.location.href='index_ad.php?dashboard';</script>";
    exit();
}

// Truy vấn danh sách chính sách
$query = "SELECT * FROM chinhsach";
$result = mysqli_query($conn, $query);

// Xử lý thêm chính sách
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    if (!in_array('Thêm chính sách', $currentPermissions) && (!isset($_SESSION['maRole']) || $_SESSION['maRole'] != 1)) {
        echo "<script>alert('Bạn không có quyền thêm chính sách!'); window.location.href='index_ad.php?rule';</script>";
        exit();
    }
    $ten = mysqli_real_escape_string($conn, $_POST['ten']);
    $noiDung = mysqli_real_escape_string($conn, $_POST['noiDung']);
    if (empty($ten) || empty($noiDung)) {
        echo "<script>alert('Tên và nội dung không được để trống!');</script>";
    } else {
        $query = "INSERT INTO chinhsach (ten, noiDung) VALUES ('$ten', '$noiDung')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Thêm chính sách thành công!'); window.location.href='index_ad.php?rule';</script>";
        } else {
            echo "<script>alert('Lỗi thêm chính sách!');</script>";
        }
    }
}

// Xử lý cập nhật chính sách
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    if (!in_array('Sửa chính sách', $currentPermissions) && (!isset($_SESSION['maRole']) || $_SESSION['maRole'] != 1)) {
        echo "<script>alert('Bạn không có quyền sửa chính sách!'); window.location.href='index_ad.php?rule';</script>";
        exit();
    }

    $maChinhSach = intval($_POST['maChinhSach']);
    $ten = mysqli_real_escape_string($conn, $_POST['ten']);
    $noiDung = mysqli_real_escape_string($conn, $_POST['noiDung']);

    if (empty($ten) || empty($noiDung)) {
        echo "<script>alert('Tên và nội dung không được để trống!');</script>";
    } else {
        $query = "UPDATE chinhsach SET ten = '$ten', noiDung = '$noiDung' WHERE maChinhSach = $maChinhSach";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Cập nhật chính sách thành công!'); window.location.href='index_ad.php?rule';</script>";
        } else {
            echo "<script>alert('Lỗi cập nhật chính sách!');</script>";
        }
    }
}

// Xử lý xóa chính sách
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    if (!in_array('Xóa chính sách', $currentPermissions) && (!isset($_SESSION['maRole']) || $_SESSION['maRole'] != 1)) {
        echo "<script>alert('Bạn không có quyền xóa chính sách!'); window.location.href='index_ad.php?rule';</script>";
        exit();
    }

    $maChinhSach = intval($_POST['maChinhSach']);
    $query = "DELETE FROM chinhsach WHERE maChinhSach = $maChinhSach";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Xóa chính sách thành công!'); window.location.href='index_ad.php?rule';</script>";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .policy-content {
            max-width: 800px;
            max-height: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            line-height: 1.5;
            text-align: justify;
            color: #495057;
        }

        .policy-content p {
            margin: 0;
            padding: 0.5em 0;
        }

        .modal-lg {
            max-width: 90%;
        }

        .textarea-large {
            height: 300px;
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
                        <div class="table-responsive p-3" align="right">
                            <?php if (in_array('Thêm chính sách', $currentPermissions) || (isset($_SESSION['maRole']) && $_SESSION['maRole'] == 1)): ?>
                                <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="fa fa-add"></i> Thêm mới
                                </button>
                            <?php endif; ?>
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 5%;"
                                            class="text-uppercase text-secondary font-weight-bolder opacity-75 text-start">
                                            TÊN CHÍNH SÁCH</th>
                                        <th style="width: 90%;"
                                            class="text-uppercase text-secondary font-weight-bolder opacity-75 text-start">
                                            NỘI DUNG CHÍNH SÁCH</th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-75"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $shortContent = $row['noiDung'];
                                            if (substr_count($shortContent, "\n") > 4) {
                                                $shortContent = implode('<p>', array_slice(explode("\n", $shortContent), 0, 5)) . '...</p>';
                                            }
                                            echo "<tr class='text-center'>
                                                    <td class='text-start'><h6>{$row['ten']}</h6></td>
                                                    <td class='text-start text-wrap policy-content'>{$shortContent}</td>
                                                    <td>";
                                            if (in_array('Sửa chính sách', $currentPermissions) || (isset($_SESSION['maRole']) && $_SESSION['maRole'] == 1)) {
                                                echo "<a data-toggle='modal' data-target='#editModal' 
                                                        class='btn btn-warning edit-btn btn-icon' 
                                                        data-id='" . $row['maChinhSach'] . "' 
                                                        data-ten='" . htmlspecialchars($row['ten']) . "' 
                                                        data-noidung='" . htmlspecialchars($row['noiDung']) . "'>
                                                        <i class='fa fa-edit'></i>
                                                      </a>";
                                            }
                                            if (in_array('Xóa chính sách', $currentPermissions) || (isset($_SESSION['maRole']) && $_SESSION['maRole'] == 1)) {
                                                echo "<form method='POST' style='display:inline;'>
                                                        <input type='hidden' name='maChinhSach' value='" . $row['maChinhSach'] . "'>
                                                        <button type='submit' name='delete' class='btn btn-danger btn-icon' 
                                                                onclick='return confirm(\"Bạn có chắc chắn muốn xóa Chính sách này?\");'>
                                                            <i class='fa fa-trash'></i>
                                                        </button>
                                                      </form>";
                                            }
                                            echo "</td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='text-muted'>Không có chính sách nào.</td></tr>";
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

    <!-- Modal sửa chính sách -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
                            <textarea class="form-control textarea-large" id="noiDung" name="noiDung"
                                required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="update" class="btn btn-primary">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thêm chính sách -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Thêm Chính Sách</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="text" name="ten" class="form-control mb-2" placeholder="Tên" required>
                        <textarea name="noiDung" class="form-control textarea-large mb-2" placeholder="Nội dung"
                            required></textarea>
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
                var data = $(this).data();
                $("#maChinhSach").val(data.id);
                $("#ten").val(data.ten);
                $("#noiDung").val(data.noidung);
                $("#editModal").modal("show");
            });
        });
    </script>
</body>

</html>
<?php mysqli_close($conn); ?>