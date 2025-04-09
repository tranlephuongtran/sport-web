<?php
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Thiết lập mã hóa UTF-8
mysqli_set_charset($conn, "utf8");

// Lấy từ khóa tìm kiếm từ tham số 'customer'
$searchKeyword = isset($_GET['customer']) ? mysqli_real_escape_string($conn, $_GET['customer']) : '';

// Truy vấn cơ bản
$query = "
    SELECT 
        kh.maKH, 
        tk.email, 
        nd.ten AS tenKH, 
        nd.sdt AS sdtKH
    FROM 
        khachhang kh
    JOIN 
        nguoidung nd ON kh.maNguoiDung = nd.maNguoiDung
    JOIN 
        taikhoan tk ON nd.maTK = tk.maTK
";

// Thêm điều kiện tìm kiếm
if (!empty($searchKeyword)) {
    $query .= " WHERE nd.ten LIKE '%$searchKeyword%' 
                OR tk.email LIKE '%$searchKeyword%' 
                OR nd.sdt LIKE '%$searchKeyword%' 
                OR kh.maKH LIKE '%$searchKeyword%'";
}

// Phân trang
$itemsPerPage = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Tổng số bản ghi
$totalQuery = "SELECT COUNT(*) AS total FROM ($query) AS temp";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $itemsPerPage);

// Giới hạn kết quả
$query .= " LIMIT $itemsPerPage OFFSET $offset";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khách Hàng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .card-header {
            background: linear-gradient(90deg, #007bff, #00c4cc);
            border-radius: 10px 10px 0 0;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px;
        }

        .search-form input {
            border-radius: 20px;
            padding: 10px 20px;
            border: 1px solid #ced4da;
        }

        .search-form button {
            position: relative;
            top: 5px;
            width: 100px;
            border-radius: 20px;
            padding: 10px;
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s;
        }

        .search-form button:hover {
            background-color: #0056b3;
        }

        .table {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .table th {
            background-color: #f1f3f5;
            color: #495057;
            font-weight: 600;
            padding: 15px;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
        }

        .table tr:hover {
            background-color: #f8f9fa;
        }

        .pagination {
            margin: 20px 0;
        }

        .page-item .page-link {
            border-radius: 50%;
            margin: 0 5px;
            color: #007bff;
            border: 1px solid #dee2e6;
        }

        .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }

        .page-item .page-link:hover {
            background-color: #e9ecef;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a>
                    </li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Khách Hàng</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Khách Hàng</h6>
            </nav>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">KHÁCH HÀNG</h6>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Form tìm kiếm -->
                        <form method="GET" action="index_ad.php" class="search-form">
                            <input type="text" name="customer" id="customer" class="form-control"
                                placeholder="Nhập tên, email, hoặc SĐT..."
                                value="<?= htmlspecialchars($searchKeyword) ?>">
                            <button type="submit" class="btn btn-primary">Tìm Kiếm</button>
                        </form>

                        <!-- Bảng dữ liệu -->
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th>Mã Khách Hàng</th>
                                        <th>Email</th>
                                        <th>Tên Khách Hàng</th>
                                        <th>Số Điện Thoại</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr class='text-center'>
                <td>
                    <a href='index_ad.php?order_ad&customer={$row['maKH']}' style='color:blue; text-decoration:underline;'>
                        {$row['maKH']}
                    </a>
                </td>
                <td>{$row['email']}</td>
                <td>{$row['tenKH']}</td>
                <td>{$row['sdtKH']}</td>
              </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        <div class="d-flex justify-content-center">
                            <nav>
                                <ul class="pagination">
                                    <?php
                                    if ($page > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="index_ad.php?customer=' . urlencode($searchKeyword) . '&page=' . ($page - 1) . '">Trước</a></li>';
                                    }
                                    for ($i = 1; $i <= $totalPages; $i++) {
                                        $activeClass = ($i == $page) ? 'active' : '';
                                        echo '<li class="page-item ' . $activeClass . '"><a class="page-link" href="index_ad.php?customer=' . urlencode($searchKeyword) . '&page=' . $i . '">' . $i . '</a></li>';
                                    }
                                    if ($page < $totalPages) {
                                        echo '<li class="page-item"><a class="page-link" href="index_ad.php?customer=' . urlencode($searchKeyword) . '&page=' . ($page + 1) . '">Sau</a></li>';
                                    }
                                    ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php mysqli_close($conn); ?>