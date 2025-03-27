<?php
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

if (!isset($_GET['maDon'])) {
    die("Không tìm thấy đơn hàng.");
}

$maDon = mysqli_real_escape_string($conn, $_GET['maDon']);

// Lấy thông tin đơn hàng
$query = "
    SELECT DISTINCT d.maDon, d.ngayDat, d.ngayChoi, d.tongTien, d.tinhTrang, d.phuongThucThanhToan, d.hinhAnh
    FROM dondatsan d
    WHERE d.maDon = '$maDon'
";

$result = mysqli_query($conn, $query);
$order = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Hóa Đơn</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .order-img {
            width: 100px;
            height: 100px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <!-- Navbar từ trang đầu tiên (không có nút Quay lại) -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a>
                    </li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Chi Tiết Hóa Đơn</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Chi Tiết Hóa Đơn</h6>
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
                            <h6 class="text-white text-capitalize ps-3">CHI TIẾT HÓA ĐƠN</h6>
                        </div>
                    </div>

                    <div class="card-body px-0">
                        <div class="table-responsive p-3">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th>Mã Đơn</th>
                                        <th>Ngày Đặt</th>
                                        <th>Ngày Chơi</th>
                                        <th>Tổng Tiền</th>
                                        <th>Tình Trạng</th>
                                        <th>Phương Thức Thanh Toán</th>
                                        <th>Hình Ảnh</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-center">
                                        <td class="text-primary"><?= $order['maDon'] ?></td>
                                        <td><?= $order['ngayDat'] ?></td>
                                        <td><?= $order['ngayChoi'] ?></td>
                                        <td class="text-primary fw-bold">
                                            <?= number_format($order['tongTien'], 0, ',', '.') ?> VND
                                        </td>
                                        <td
                                            class="<?= ($order['tinhTrang'] == 'Hoàn thành') ? 'text-success' : (($order['tinhTrang'] == 'Đã thanh toán') ? 'text-warning' : 'text-danger') ?> fw-bold">
                                            <?= $order['tinhTrang'] ?>
                                        </td>
                                        <td><?= $order['phuongThucThanhToan'] ?></td>
                                        <td>
                                            <?php if (!empty($order['hinhAnh'])): ?>
                                                <a href="layout/img/bills/<?= $order['hinhAnh'] ?>"
                                                    data-lightbox="bill-image" data-title="Hóa đơn <?= $order['maDon'] ?>">
                                                    <img src="layout/img/bills/<?= $order['hinhAnh'] ?>" alt="Hóa đơn"
                                                        class="img-thumbnail order-img" style="cursor:pointer">
                                                </a>
                                            <?php else: ?>
                                                Không có ảnh
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Nút Quay lại đặt dưới bảng -->
                        <div class="mt-3  ps-3">
                            <a href="index_ad.php?order_ad" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>