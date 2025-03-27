<?php
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

// Kiểm tra kết nối
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
$labels = [];
$revenues = [];
$paidAmounts = [];
$dateRange = [];

$start = new DateTime($fromDate);
$end = new DateTime($toDate);
$interval = new DateInterval('P1D');
$period = new DatePeriod($start, $interval, $end->modify('+1 day'));

foreach ($period as $date) {
  $dateRange[$date->format("Y-m-d")] = ["doanhThu" => 0, "tongThanhToan" => 0];
}

while ($row = mysqli_fetch_assoc($result)) {
  $dateRange[$row['ngay']] = [
    "doanhThu" => $row['doanhThu'],
    "tongThanhToan" => $row['tongThanhToan']
  ];
  $totalRevenue += $row['doanhThu'];
  $totalPaid += $row['tongThanhToan'];
}

foreach ($dateRange as $date => $values) {
  $labels[] = $date;
  $revenues[] = $values["doanhThu"];
  $paidAmounts[] = $values["tongThanhToan"];
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Doanh Thu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="container py-4 ">
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
      <div class="col-md-8">
        <div class="card p-3 mb-4">
          <canvas id="revenueChart"></canvas>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card p-3">
          <div class="row">
            <div class="col-md-12 p-2">
              <div class="card text-center shadow-sm p-4 bg-primary text-white">
                <h5 class="fw-bold">Tổng Doanh Thu</h5>
                <h2 class="fw-bold"><?= number_format($totalRevenue, 0, ',', '.') ?> VND</h2>
              </div>
            </div>

            <!-- Card Tổng Doanh Thu (Đã trừ Voucher) -->
            <div class="col-md-12 p-2">
              <div class="card text-center shadow-sm p-4 bg-success text-white">
                <h5 class="fw-bold">Tổng Doanh Thu (Đã trừ Voucher)</h5>
                <h2 class="fw-bold"><?= number_format($totalPaid, 0, ',', '.') ?> VND</h2>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="table-responsive card ">
      <table class="table  text-center">
        <thead class="table-dark">
          <tr>
            <th>Ngày</th>
            <th>Doanh Thu (VND)</th>
            <th>Doanh Thu (VND) (Đã trừ Vouchers)</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $hasData = false; // Biến kiểm tra có dữ liệu hay không
          foreach ($dateRange as $date => $values):
            if ($values["doanhThu"] > 0 || $values["tongThanhToan"] > 0): // Chỉ hiển thị ngày có doanh thu
              $hasData = true;
              ?>
              <tr>
                <td><?= $date ?></td>
                <td class="text-primary fw-bold"><?= number_format($values["doanhThu"], 0, ',', '.') ?></td>
                <td class="text-success fw-bold"><?= number_format($values["tongThanhToan"], 0, ',', '.') ?></td>
              </tr>
              <?php
            endif;
          endforeach;
          ?>

          <?php if ($hasData): // Chỉ hiển thị dòng tổng nếu có dữ liệu ?>
            <tr class="table-success fw-bold">
              <td>Tổng Cộng</td>
              <td class="text-primary"><?= number_format($totalRevenue, 0, ',', '.') ?> VND</td>
              <td class="text-success"><?= number_format($totalPaid, 0, ',', '.') ?> VND</td>
            </tr>
          <?php else: ?>
            <tr>
              <td colspan="3" class="text-muted">Không có dữ liệu doanh thu trong khoảng thời gian này.</td>
            </tr>
          <?php endif; ?>
        </tbody>


      </table>
    </div>


  </div>

  <script>
    // biểu đồ cột
    var ctx = document.getElementById('revenueChart').getContext('2d');
    var revenueChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [
          {
            label: 'Doanh thu (VND)',
            data: <?= json_encode($revenues) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.8)', // Xanh dương đậm
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            borderRadius: 10, // Bo góc cột
            hoverBackgroundColor: 'rgba(54, 162, 235, 1)', // Hiệu ứng hover
          },
          {
            label: 'Doanh Thu (VND) (Đã trừ Vouchers)',
            data: <?= json_encode($paidAmounts) ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.8)', // Xanh lá đậm
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            borderRadius: 10,
            hoverBackgroundColor: 'rgba(75, 192, 192, 1)',
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              font: {
                size: 14,
                weight: 'bold'
              }
            }
          },
          tooltip: {
            callbacks: {
              label: function (tooltipItem) {
                return new Intl.NumberFormat('vi-VN').format(tooltipItem.raw) + ' VND';
              }
            }
          }
        },
        scales: {
          x: {
            stacked: false,
            ticks: {
              font: {
                size: 12
              }
            }
          },
          y: {
            beginAtZero: true,
            ticks: {
              font: {
                size: 12
              },
              callback: function (value) {
                return new Intl.NumberFormat('vi-VN').format(value) + ' VND';
              }
            }
          }
        }
      }
    });

  </script>


</body>
<style>
  .card {
    padding: 10px;
    border-radius: 15px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
  }

  #totalRevenueChart {
    max-height: 300px;
  }
</style>


</html>