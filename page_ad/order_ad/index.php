<?php

$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

// Kiểm tra kết nối
if (!$conn) {
  die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Cập nhật trạng thái đơn hàng
if (isset($_GET['update_maDon']) && isset($_GET['current_status'])) {
  $maDon = mysqli_real_escape_string($conn, $_GET['update_maDon']);
  $currentStatus = mysqli_real_escape_string($conn, $_GET['current_status']);

  // Lấy thông tin đơn hàng
  $orderQuery = "SELECT hinhAnh, tinhTrang FROM dondatsan WHERE maDon = '$maDon'";
  $orderResult = mysqli_query($conn, $orderQuery);
  $orderData = mysqli_fetch_assoc($orderResult);

  if ($orderData) {
    $imageExists = !empty($orderData['hinhAnh']);

    // Chỉ cập nhật nếu điều kiện đúng
    if ($currentStatus == 'Chờ xác nhận' && $imageExists) {
      $updateQuery = "UPDATE dondatsan SET tinhTrang = 'Đã thanh toán' WHERE maDon = '$maDon'";
      mysqli_query($conn, $updateQuery);
    } elseif ($currentStatus == 'Đã thanh toán') {
      $updateQuery = "UPDATE dondatsan SET tinhTrang = 'Hoàn thành' WHERE maDon = '$maDon'";
      mysqli_query($conn, $updateQuery);
    }
  }
}

// Lọc tìm kiếm
$tinhTrangFilter = isset($_GET['tinhTrang']) ? $_GET['tinhTrang'] : 'Tất cả';
$searchKeyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Truy vấn danh sách đơn hàng
$query = "
    SELECT DISTINCT 
        d.maDon, 
        d.ngayDat, 
        d.ngayChoi, 
        d.tongTien, 
        d.tinhTrang, 
        d.phuongThucThanhToan, 
        d.hinhAnh
    FROM 
        dondatsan d
    JOIN 
        chitiethoadon c ON d.maDon = c.maDon
";

$conditions = [];
if ($tinhTrangFilter !== 'Tất cả') {
  $conditions[] = "d.tinhTrang = '" . mysqli_real_escape_string($conn, $tinhTrangFilter) . "'";
}
if (!empty($searchKeyword)) {
  $conditions[] = "d.maDon LIKE '%$searchKeyword%'";
}
if (!empty($conditions)) {
  $query .= " WHERE " . implode(" AND ", $conditions);
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hóa đơn</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    function updateTinhTrang(maDon, currentStatus) {
      if (confirm("Bạn có chắc chắn muốn cập nhật tình trạng đơn hàng này?")) {
        window.location.href = "?update_maDon=" + maDon + "&current_status=" + currentStatus;
      }
    }
  </script>
</head>

<body>
  <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
    <div class="container-fluid py-1 px-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Hóa Đơn</li>
        </ol>
        <h6 class="font-weight-bolder mb-0">Hóa Đơn</h6>
      </nav>
      <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4">
        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
          <form method="GET" class="input-group input-group-outline">
            <input type="text" name="search" class="form-control" placeholder="Tìm Kiếm Theo Mã Đơn..."
              value="<?= htmlspecialchars($searchKeyword) ?>">
          </form>
        </div>
      </div>
    </div>
  </nav>

  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card my-4">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
              <h6 class="text-white text-capitalize ps-3">HÓA ĐƠN</h6>
            </div>
          </div>

          <div class="card-body px-0">
            <div class="p-3 col-2">
              <form method="GET">
                <select name="tinhTrang" class="form-select" onchange="this.form.submit()">
                  <option value="Tất cả" <?= $tinhTrangFilter == 'Tất cả' ? 'selected' : '' ?>>Tất cả</option>
                  <option value="Chờ xác nhận" <?= $tinhTrangFilter == 'Chờ xác nhận' ? 'selected' : '' ?>>Chờ xác nhận
                  </option>
                  <option value="Đã thanh toán" <?= $tinhTrangFilter == 'Đã thanh toán' ? 'selected' : '' ?>>Đã thanh toán
                  </option>
                  <option value="Hoàn thành" <?= $tinhTrangFilter == 'Hoàn thành' ? 'selected' : '' ?>>Hoàn thành</option>
                </select>
              </form>
            </div>

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
                  <?php
                  while ($row = mysqli_fetch_assoc($result)) {
                    $statusColor = $row['tinhTrang'] == 'Hoàn thành' ? 'text-success' :
                      ($row['tinhTrang'] == 'Đã thanh toán' ? 'text-warning' : 'text-danger');

                    $updateLink = ($row['tinhTrang'] == 'Chờ xác nhận' && !empty($row['hinhAnh'])) ||
                      ($row['tinhTrang'] == 'Đã thanh toán') ?
                      " onclick='updateTinhTrang({$row['maDon']}, \"{$row['tinhTrang']}\")' style='cursor:pointer; color:blue; text-decoration:underline;'" : "";

                    echo "<tr class='text-center'>
                            <td {$updateLink}>{$row['maDon']}</td>
                            <td>{$row['ngayDat']}</td>
                            <td>{$row['ngayChoi']}</td>
                            <td class='text-primary fw-bold'>" . number_format($row['tongTien'], 0, ',', '.') . " VND</td>
                            <td class='{$statusColor} fw-bold'>{$row['tinhTrang']}</td>
                            <td>{$row['phuongThucThanhToan']}</td>
                            <td><img src='layout/img/bills/{$row['hinhAnh']}' alt='Hóa đơn' width='100'></td>
                          </tr>";
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

</body>

</html>