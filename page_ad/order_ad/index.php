<?php

$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

// Kiểm tra kết nối
if (!$conn) {
  die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Cập nhật tình trạng đơn hàng
if (isset($_GET['update_maDon'])) {
  $maDon = mysqli_real_escape_string($conn, $_GET['update_maDon']);
  $updateQuery = "UPDATE dondatsan SET tinhTrang = 'Hoàn thành' WHERE maDon = '$maDon' AND tinhTrang = 'Đang xử lý'";
  mysqli_query($conn, $updateQuery);
}

// Lấy giá trị lọc từ request (nếu có)
$tinhTrangFilter = isset($_GET['tinhTrang']) ? $_GET['tinhTrang'] : 'Tất cả';
$searchKeyword = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Truy vấn để lấy dữ liệu hóa đơn có lọc
$query = "
    SELECT 
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
    function updateTinhTrang(maDon) {
      if (confirm("Bạn có chắc chắn muốn cập nhật tình trạng đơn hàng này?")) {
        window.location.href = "?update_maDon=" + maDon;
      }
    }
    function showImage(src) {
      document.getElementById("modalImage").src = src;
      var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
      myModal.show();
    }
  </script>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- jQuery & Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

</head>
<!-- Modal hiển thị ảnh -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Xem Ảnh</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalImage" src="" class="img-fluid" alt="Ảnh hóa đơn">
      </div>
    </div>
  </div>
</div>

<body>
  <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a>
          </li>
          <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Hóa Đơn</li>
        </ol>
        <h6 class="font-weight-bolder mb-0">Hóa Đơn</h6>
      </nav>
      <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
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
            <div class=" p-3 col-2">
              <form method="GET">
                <select name="tinhTrang" id="tinhTrang" class="form-select" onchange="this.form.submit()">
                  <option value="Tất cả" <?= $tinhTrangFilter == 'Tất cả' ? 'selected' : '' ?>>Tất cả</option>
                  <option value="Đang xử lý" <?= $tinhTrangFilter == 'Đang xử lý' ? 'selected' : '' ?>>Đang xử lý</option>
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
                  if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                      $statusColor = ($row['tinhTrang'] == 'Hoàn thành') ? 'text-success' : (($row['tinhTrang'] == 'Đang xử lý') ? 'text-danger' : '');
                      $updateLink = ($row['tinhTrang'] == 'Đang xử lý') ? " onclick='updateTinhTrang({$row['maDon']})' style='cursor:pointer; color:blue; text-decoration:underline;'" : "";
                      echo "<tr class='text-center'>
                                <td {$updateLink}>{$row['maDon']}</td>
                                <td>{$row['ngayDat']}</td>
                                <td>{$row['ngayChoi']}</td>
                                <td class='text-primary fw-bold'>" . number_format($row['tongTien'], 0, ',', '.') . " VND</td>
                                <td class='{$statusColor} fw-bold'>{$row['tinhTrang']}</td>
                                <td>{$row['phuongThucThanhToan']}</td>
                                <td>
                    <a href='layout/img/bills/{$row['hinhAnh']}' data-lightbox='image-{$row['maDon']}' data-title='Mã Đơn: {$row['maDon']}'>
                        <img src='layout/img/bills/{$row['hinhAnh']}' alt='Hình ảnh' style='width: 100px; height: 100px;'>
                    </a>
                </td>


                              </tr>";
                    }
                  } else {
                    echo "<tr><td colspan='7' class='text-center'>Không có hóa đơn nào.</td></tr>";
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