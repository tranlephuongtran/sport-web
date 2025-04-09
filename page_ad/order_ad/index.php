<?php
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

// Kiểm tra kết nối
if (!$conn) {
  die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Thiết lập mã hóa UTF-8 cho kết nối
mysqli_set_charset($conn, "utf8");

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
$ngayDatFilter = isset($_GET['ngayDat']) ? mysqli_real_escape_string($conn, $_GET['ngayDat']) : '';
$ngayChoiFilter = isset($_GET['ngayChoi']) ? mysqli_real_escape_string($conn, $_GET['ngayChoi']) : '';

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
        d.tongThanhToan,
        d.hinhAnh,
        GROUP_CONCAT(DISTINCT s.tenSan SEPARATOR ', ') AS tenSan,
        GROUP_CONCAT(c.gioChoi SEPARATOR ', ') AS gioChoi,
        k.tenKM,
        k.giaGiam,
        nd.ten AS tenKH,
        nd.sdt AS sdtKH
    FROM 
        dondatsan d
    JOIN 
        chitiethoadon c ON d.maDon = c.maDon
    JOIN 
        san s ON c.maSan = s.maSan
    LEFT JOIN 
        khuyenmai k ON d.maKM = k.maKM
    JOIN 
        khachhang kh ON d.maKH = kh.maKH
    JOIN 
        nguoidung nd ON kh.maNguoiDung = nd.maNguoiDung
";

$conditions = [];
if ($tinhTrangFilter !== 'Tất cả') {
  $conditions[] = "d.tinhTrang = '" . mysqli_real_escape_string($conn, $tinhTrangFilter) . "'";
}
if (!empty($searchKeyword)) {
  $conditions[] = "d.maDon LIKE '%$searchKeyword%'";
}
if (!empty($ngayDatFilter)) {
  $conditions[] = "d.ngayDat = '$ngayDatFilter'";
}
if (!empty($ngayChoiFilter)) {
  $conditions[] = "d.ngayChoi = '$ngayChoiFilter'";
}
if (!empty($conditions)) {
  $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " GROUP BY d.maDon";

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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Lightbox2 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
  <!-- Lightbox2 JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
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
            <div class="row p-3 align-items-center">
              <div class="col-md-3">
                <form method="GET">
                  <label for="tinhTrang" class="form-label">Tình Trạng</label>
                  <select name="tinhTrang" id="tinhTrang" class="form-select" onchange="this.form.submit()">
                    <option value="Tất cả" <?= $tinhTrangFilter == 'Tất cả' ? 'selected' : '' ?>>Tất cả</option>
                    <option value="Chờ xác nhận" <?= $tinhTrangFilter == 'Chờ xác nhận' ? 'selected' : '' ?>>Chờ xác nhận
                    </option>
                    <option value="Đã thanh toán" <?= $tinhTrangFilter == 'Đã thanh toán' ? 'selected' : '' ?>>Đã thanh
                      toán</option>
                    <option value="Hoàn thành" <?= $tinhTrangFilter == 'Hoàn thành' ? 'selected' : '' ?>>Hoàn thành
                    </option>
                  </select>
                  <input type="hidden" name="ngayDat" value="<?= htmlspecialchars($ngayDatFilter) ?>">
                  <input type="hidden" name="ngayChoi" value="<?= htmlspecialchars($ngayChoiFilter) ?>">
                  <input type="hidden" name="search" value="<?= htmlspecialchars($searchKeyword) ?>">
                </form>
              </div>
              <div class="col-md-3">
                <form method="GET">
                  <label for="ngayDat" class="form-label">Ngày Đặt</label>
                  <input type="date" name="ngayDat" id="ngayDat" class="form-control"
                    value="<?= htmlspecialchars($ngayDatFilter) ?>" onchange="this.form.submit()">
                  <input type="hidden" name="tinhTrang" value="<?= htmlspecialchars($tinhTrangFilter) ?>">
                  <input type="hidden" name="ngayChoi" value="<?= htmlspecialchars($ngayChoiFilter) ?>">
                  <input type="hidden" name="search" value="<?= htmlspecialchars($searchKeyword) ?>">
                </form>
              </div>
              <div class="col-md-3">
                <form method="GET">
                  <label for="ngayChoi" class="form-label">Ngày Chơi</label>
                  <input type="date" name="ngayChoi" id="ngayChoi" class="form-control"
                    value="<?= htmlspecialchars($ngayChoiFilter) ?>" onchange="this.form.submit()">
                  <input type="hidden" name="tinhTrang" value="<?= htmlspecialchars($tinhTrangFilter) ?>">
                  <input type="hidden" name="ngayDat" value="<?= htmlspecialchars($ngayDatFilter) ?>">
                  <input type="hidden" name="search" value="<?= htmlspecialchars($searchKeyword) ?>">
                </form>
              </div>
              <div class="col-md-3">
                <form method="GET">
                  <label for="search" class="form-label">Tìm Theo Mã Đơn</label>
                  <input type="text" name="search" id="search" class="form-control" placeholder="Nhập mã đơn ..."
                    value="<?= htmlspecialchars($searchKeyword) ?>">
                  <input type="hidden" name="tinhTrang" value="<?= htmlspecialchars($tinhTrangFilter) ?>">
                  <input type="hidden" name="ngayDat" value="<?= htmlspecialchars($ngayDatFilter) ?>">
                  <input type="hidden" name="ngayChoi" value="<?= htmlspecialchars($ngayChoiFilter) ?>">

                </form>
              </div>

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
                    <th class="text-wrap">PT Thanh Toán</th>
                    <th class="text-wrap">Tên Sân</th>
                    <th class="text-wrap">Giờ Chơi</th>
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
                                                <td class='text-wrap'>{$row['phuongThucThanhToan']}</td>
                                                <td class='text-wrap'>{$row['tenSan']}</td>
                                                <td class='text-wrap'>{$row['gioChoi']}</td>
                                                <td>
                                                    <a href='layout/img/bills/{$row['hinhAnh']}' data-lightbox='bill-image' data-title='Hóa đơn {$row['maDon']}'>
                                                        <img src='layout/img/bills/{$row['hinhAnh']}' alt='Hóa đơn' width='100' height='100' class='img-thumbnail' style='cursor:pointer'>
                                                    </a>
                                                </td>
                                                <td>
                                                    <button class='btn btn-primary btn-sm detail-btn'
                                                            data-bs-toggle='modal' 
                                                            data-bs-target='#orderDetailModal'
                                                            data-madon='{$row['maDon']}'
                                                            data-ngaydat='{$row['ngayDat']}'
                                                            data-ngaychoi='{$row['ngayChoi']}'
                                                            data-tongtien=" . number_format($row['tongTien'], 0, ',', '.') . "
                                                            data-tongthanhtoan=" . number_format($row['tongThanhToan'], 0, ',', '.') . "
                                                            data-tinhtrang='{$row['tinhTrang']}'
                                                            data-phuongthuc='{$row['phuongThucThanhToan']}'
                                                            data-tenkh='{$row['tenKH']}'
                                                            data-sdtkh='{$row['sdtKH']}'
                                                            data-tensan='{$row['tenSan']}'
                                                            data-giochoi='{$row['gioChoi']}'
                                                            data-tenkm='" . ($row['tenKM'] ?? '') . "'
                                                            data-giagiam='" . ($row['giaGiam'] ? number_format($row['giaGiam'], 0, ',', '.') . ' VND' : '') . "'
                                                            data-hinhanh='{$row['hinhAnh']}'>
                                                        Chi Tiết
                                                    </button>
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
                    echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '&tinhTrang=' . $tinhTrangFilter . '&ngayDat=' . $ngayDatFilter . '&ngayChoi=' . $ngayChoiFilter . '&search=' . $searchKeyword . '">Trước</a></li>';
                  }
                  for ($i = 1; $i <= $totalPages; $i++) {
                    $activeClass = ($i == $page) ? 'active' : '';
                    echo '<li class="page-item ' . $activeClass . '"><a class="page-link" href="?page=' . $i . '&tinhTrang=' . $tinhTrangFilter . '&ngayDat=' . $ngayDatFilter . '&ngayChoi=' . $ngayChoiFilter . '&search=' . $searchKeyword . '">' . $i . '</a></li>';
                  }
                  if ($page < $totalPages) {
                    echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '&tinhTrang=' . $tinhTrangFilter . '&ngayDat=' . $ngayDatFilter . '&ngayChoi=' . $ngayChoiFilter . '&search=' . $searchKeyword . '">Sau</a></li>';
                  }
                  ?>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal chi tiết hóa đơn -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-gradient-primary text-white text-center">
            <h5 class="modal-title" id="orderDetailModalLabel">CHI TIẾT HÓA ĐƠN</h5>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <p><strong>Mã Đơn:</strong> <span id="detail-maDon"></span></p>
                <p><strong>Ngày Đặt:</strong> <span id="detail-ngayDat"></span></p>
                <p><strong>Ngày Chơi:</strong> <span id="detail-ngayChoi"></span></p>
                <p><strong>Khách Hàng:</strong> <span id="detail-tenKH"></span></p>
                <p><strong>SĐT:</strong> <span id="detail-sdtKH"></span></p>
                <p><strong>Tên Sân:</strong> <span id="detail-tenSan"></span></p>
                <p><strong>Giờ Chơi:</strong> <span id="detail-gioChoi"></span></p>
                <p><strong>Tổng Tiền:</strong> <span id="detail-tongTien"></span></p>
                <p><strong>Tổng Thanh Toán:</strong> <span id="detail-tongThanhToan"></span></p>
                <p><strong>Tình Trạng:</strong> <span id="detail-tinhTrang"></span></p>
                <p><strong>Phương Thức Thanh Toán:</strong> <span id="detail-phuongThuc"></span></p>
              </div>
              <div class="col-md-6">
                <p><strong>Hình Ảnh Thanh Toán:</strong></p>
                <a href="" id="detail-hinhAnhLink" data-lightbox="bill-detail">
                  <img src="" id="detail-hinhAnh" alt="Hóa đơn" width="350" class="img-thumbnail">
                </a>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function updateTinhTrang(maDon, currentStatus) {
      if (confirm("Bạn có chắc chắn muốn cập nhật tình trạng đơn hàng này?")) {
        window.location.href = "?update_maDon=" + maDon + "¤t_status=" + currentStatus;
      }
    }

    $(document).ready(function () {
      $('.detail-btn').click(function () {
        const maDon = $(this).data('madon');
        const ngayDat = $(this).data('ngaydat');
        const ngayChoi = $(this).data('ngaychoi');
        const tongTien = $(this).data('tongtien');
        const tongThanhToan = $(this).data('tongthanhtoan');
        const tinhTrang = $(this).data('tinhtrang');
        const phuongThuc = $(this).data('phuongthuc');
        const tenKH = $(this).data('tenkh');
        const sdtKH = $(this).data('sdtkh');
        const tenSan = $(this).data('tensan');
        const gioChoi = $(this).data('giochoi');
        const tenKM = $(this).data('tenkm') ? $(this).data('tenkm') + ' (Giảm ' + $(this).data('giagiam') + ')' : 'Không có';
        const hinhAnh = $(this).data('hinhanh');

        $('#detail-maDon').text(maDon);
        $('#detail-ngayDat').text(ngayDat);
        $('#detail-ngayChoi').text(ngayChoi);
        $('#detail-tongTien').text(tongTien);
        $('#detail-tongThanhToan').text(tongThanhToan);
        $('#detail-tinhTrang').text(tinhTrang).removeClass().addClass(tinhTrang === 'Hoàn thành' ? 'text-success' : (tinhTrang === 'Đã thanh toán' ? 'text-warning' : 'text-danger'));
        $('#detail-phuongThuc').text(phuongThuc);
        $('#detail-tenKH').text(tenKH);
        $('#detail-sdtKH').text(sdtKH);
        $('#detail-tenSan').text(tenSan);
        $('#detail-gioChoi').text(gioChoi);
        $('#detail-tenKM').text(tenKM);
        $('#detail-hinhAnh').attr('src', 'layout/img/bills/' + hinhAnh);
        $('#detail-hinhAnhLink').attr('href', 'layout/img/bills/' + hinhAnh).attr('data-title', 'Hóa đơn ' + maDon);
      });
    });
  </script>

  <style>
    .page-item.active .page-link {
      z-index: 3;
      color: #fff;
      background-color: rgb(63, 87, 247);
      border-color: rgb(63, 87, 247);
    }

    .text-wrap {
      max-width: 100px;
      overflow-wrap: break-word;
      word-wrap: break-word;
      overflow: hidden;
    }
  </style>
</body>

</html>
<?php mysqli_close($conn); ?>