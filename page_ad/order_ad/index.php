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

  $orderQuery = "SELECT hinhAnh, tinhTrang FROM dondatsan WHERE maDon = '$maDon'";
  $orderResult = mysqli_query($conn, $orderQuery);
  $orderData = mysqli_fetch_assoc($orderResult);

  if ($orderData) {
    $imageExists = !empty($orderData['hinhAnh']);
    if ($currentStatus == 'Chờ xác nhận' && $imageExists) {
      mysqli_query($conn, "UPDATE dondatsan SET tinhTrang = 'Đã thanh toán' WHERE maDon = '$maDon'");
    } elseif ($currentStatus == 'Đã thanh toán') {
      mysqli_query($conn, "UPDATE dondatsan SET tinhTrang = 'Hoàn thành' WHERE maDon = '$maDon'");
    }
  }
}

// Lọc tìm kiếm
$tinhTrangFilter = isset($_GET['tinhTrang']) ? $_GET['tinhTrang'] : 'Tất cả';
$searchKeyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Thiết lập phân trang
$itemsPerPage = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

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

// Lấy tổng số bản ghi
$totalQuery = "SELECT COUNT(*) AS total FROM ($query) AS temp";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalPages = ceil($totalRow['total'] / $itemsPerPage);

// Áp dụng giới hạn phân trang
$query .= " LIMIT $itemsPerPage OFFSET $offset";
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
  <!-- Lightbox2 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
  <!-- Lightbox2 JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

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
                    <th>Xem Chi Tiết</th>

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
                            <td>
                              <a href='layout/img/bills/{$row['hinhAnh']}' data-lightbox='bill-image' data-title='Hóa đơn {$row['maDon']}'>
                                <img src='layout/img/bills/{$row['hinhAnh']}'
                                    alt='Hóa đơn' 
                                    width='100'
                                    height='100' 
                                    class='img-thumbnail' 
                                    style='cursor:pointer'>
                              </a>
                            </td>
                            <td>
                              <a href='index_ad.php?order_detail&maDon={$row['maDon']}' 
                                class='text-primary'>
                                Xem Chi Tiết
                              </a>
                            </td>


                          </tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
              <nav>
                <ul class="pagination">
                  <?php
                  if ($page > 1) {
                    echo '<li class="page-item">
                  <a class="page-link" href="?page=' . ($page - 1) . '&tinhTrang=' . $tinhTrangFilter . '">Trước</a>
                </li>';
                  }

                  for ($i = 1; $i <= $totalPages; $i++) {
                    $activeClass = ($i == $page) ? 'active' : '';
                    echo '<li class="page-item ' . $activeClass . '">
                  <a class="page-link" href="?page=' . $i . '&tinhTrang=' . $tinhTrangFilter . '">' . $i . '</a>
                </li>';
                  }

                  if ($page < $totalPages) {
                    echo '<li class="page-item">
                  <a class="page-link" href="?page=' . ($page + 1) . '&tinhTrang=' . $tinhTrangFilter . '">Sau</a>
                </li>';
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
<style>
  .page-item.active .page-link {
    z-index: 3;
    color: #fff;
    background-color: rgb(63, 87, 247);
    border-color: rgb(63, 87, 247);
</style>

</html>