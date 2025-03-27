<?php
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

if (!$conn) {
  die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

$fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : date('Y-m-01');
$toDate = isset($_GET['toDate']) ? $_GET['toDate'] : date('Y-m-t');

$fromDate = date('Y-m-d', strtotime($fromDate));
$toDate = date('Y-m-d', strtotime($toDate));

$query = "
    SELECT DATE(ngayDat) AS ngay, 
           COALESCE(SUM(tongTien), 0) AS doanhThu,
           COALESCE(SUM(tongThanhToan), 0) AS tongThanhToan
    FROM dondatsan
    WHERE (tinhTrang = 'Đã thanh toán' OR tinhTrang = 'Hoàn thành')
    AND ngayDat BETWEEN '$fromDate' AND '$toDate'
    GROUP BY DATE(ngayDat)
    ORDER BY ngayDat ASC
";

$result = mysqli_query($conn, $query);
$totalRevenue = 0;
$totalPaid = 0;
$dataChart = [];

while ($row = mysqli_fetch_assoc($result)) {
  $dataChart[] = [
    'date' => $row['ngay'],
    'revenue' => $row['doanhThu'],
    'paid' => $row['tongThanhToan']
  ];
  $totalRevenue += $row['doanhThu'];
  $totalPaid += $row['tongThanhToan'];
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Doanh Thu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
</head>

<body>
  <div class="container py-4">
    <h2 class="text-center">THỐNG KÊ DOANH THU</h2>
    <form method="GET" action="index_ad.php" class="row g-3 my-3">
      <input type="hidden" name="dashboard" value="1">
      <div class="col-md-2">
        <input type="date" name="fromDate" class="form-control" value="<?= htmlspecialchars($fromDate) ?>">
      </div>
      <div class="col-md-2">
        <input type="date" name="toDate" class="form-control" value="<?= htmlspecialchars($toDate) ?>">
      </div>
      <div class="col-md-2 text-center align-self-end">
        <button type="submit" class="btn btn-primary">Lọc Doanh Thu</button>
      </div>
    </form>

    <div class="row">
      <div class="row">
        <div class="col-md-8">
          <div class="card p-3 mb-4">
            <h5 class="text-center">DOANH THU THEO NGÀY</h5>
            <div id="mixed-chart" style="height: 300px;"></div>
          </div>
        </div>

        <!-- Biểu đồ Donut tổng doanh thu -->
        <div class="col-md-4">
          <div class="card p-3 text-center">
            <h5 class="fw-bold">TỔNG DOANH THU</h5>
            <div id="donut-chart" style="height: 300px;"></div>
          </div>
        </div>
      </div>

    </div>




    <div class="table-responsive card">
      <table class="table text-center">
        <thead class="table-dark">
          <tr>
            <th>Ngày</th>
            <th>Doanh Thu (VND)</th>
            <th>Doanh Thu (VND) (Đã trừ Vouchers)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($dataChart as $data): ?>
            <tr>
              <td><?= $data['date'] ?></td>
              <td class="text-primary fw-bold"><?= number_format($data['revenue'], 0, ',', '.') ?></td>
              <td class="text-success fw-bold"><?= number_format($data['paid'], 0, ',', '.') ?></td>
            </tr>
          <?php endforeach; ?>

          <tr class="table-success fw-bold">
            <td>Tổng Cộng</td>
            <td class="text-primary"><?= number_format($totalRevenue, 0, ',', '.') ?> VND</td>
            <td class="text-success"><?= number_format($totalPaid, 0, ',', '.') ?> VND</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Morris.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

  <script>
    new Morris.Area({
      element: 'mixed-chart',
      data: <?= json_encode($dataChart) ?>,
      xkey: 'date',
      ykeys: ['revenue', 'paid'],
      labels: ['Doanh thu', 'Doanh thu đã trừ Voucher'],
      lineColors: ['#007bff', '#28a745'],
      fillOpacity: 0.5,
      behaveLikeLine: true,
      hideHover: 'auto',
      resize: true,
      parseTime: false
    });

    new Morris.Donut({
      element: 'donut-chart',
      data: [
        { label: "Tổng Doanh Thu", value: <?= $totalRevenue ?> },
        { label: "Sau khi trừ Voucher", value: <?= $totalPaid ?> }
      ],
      colors: ['#007bff', '#28a745'],
      resize: true
    });
  </script>

  <style>
    .card {
      padding: 10px;
      border-radius: 15px;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }

    #bar-chart {
      max-height: 300px;
    }
  </style>
</body>

</html>