<?php include("../layout/header.php");
if (!isset($_GET['history'])) {
    $history = 1;
} else {
    $history = $_GET['history'];
}
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");
$user = $_SESSION['login'];

// Thực hiện truy vấn TRƯỚC khi xử lý dữ liệu
$str = "SELECT 
    dds.maDon, 
    dds.ngayDat, 
    dds.ngayChoi,
    s.tenSan,
    GROUP_CONCAT(
        CONCAT(cthd.gioChoi, ' (', FORMAT(cthd.giaSan, 0), ' đ)')
        ORDER BY cthd.gioChoi 
        SEPARATOR '<br>'
    ) AS gioChoiVaGia,
    dds.tongTien, 
    km.giaGiam, 
    dds.tongThanhToan, 
    dds.tinhTrang
FROM dondatsan dds
LEFT JOIN chitiethoadon cthd ON dds.maDon = cthd.maDon 
LEFT JOIN khachhang kh ON dds.maKH = kh.maKH 
LEFT JOIN san s ON cthd.maSan = s.maSan
LEFT JOIN khuyenmai km ON dds.maKM = km.maKM
WHERE kh.maNguoiDung = $user
GROUP BY dds.maDon, s.maSan, s.tenSan
ORDER BY dds.maDon DESC, s.tenSan;";

$result = $conn->query($str);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Khởi tạo biến để lưu trữ dữ liệu
$orders = array();

// Xử lý dữ liệu từ kết quả truy vấn
while ($row = mysqli_fetch_assoc($result)) {
    $maDon = $row['maDon'];

    if (!isset($orders[$maDon])) {
        $orders[$maDon] = array(
            'maDon' => $maDon,
            'ngayDat' => $row['ngayDat'],
            'ngayChoi' => $row['ngayChoi'],
            'tongTien' => $row['tongTien'],
            'giaGiam' => $row['giaGiam'],
            'tongThanhToan' => $row['tongThanhToan'],
            'tinhTrang' => $row['tinhTrang'],
            'courts' => array()
        );
    }

    $orders[$maDon]['courts'][] = array(
        'tenSan' => $row['tenSan'],
        'gioChoiVaGia' => $row['gioChoiVaGia']
    );
}
?>

<style>
    .text-primary {
        color: #004aad !important;
        font-weight: bold;
    }

    .table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .table thead {
        background-color: #004aad;
        color: white;
        font-weight: bold;
    }

    .table-hover tbody tr:hover {
        background-color: #f0f5ff;
    }

    .status-pending {
        color: #ffc107;
        font-weight: bold;
    }

    .status-confirmed {
        color: #007bff;
        font-weight: bold;
    }

    .status-completed {
        color: #28a745;
        font-weight: bold;
    }

    .btn-detail {
        display: inline-block;
        padding: 6px 14px;
        font-size: 14px;
        color: white;
        background-color: #004aad;
        border-radius: 20px;
        text-decoration: none;
        transition: 0.3s;
    }

    .btn-detail:hover {
        background-color: #003b82;
        transform: scale(1.05);
    }

    th {
        font-weight: lighter;
    }

    .invoice {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        font-family: Arial, sans-serif;
        line-height: 1.6;
        color: #333;
        background-color: #f9f9f9;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .invoice-header h1 {
        font-size: 24px;
        margin-bottom: 10px;
        color: #004aad;
        text-align: center;
    }

    .invoice-header p {
        margin: 5px 0;
        font-size: 14px;
        text-align: center;
    }

    .invoice-body h2 {
        font-size: 18px;
        color: #555;
        margin-bottom: 10px;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .invoice-table thead th {
        background-color: #004aad;
        color: #fff;
        padding: 10px;
        text-align: left;
        font-size: 14px;
    }

    .invoice-table tbody td {
        padding: 10px;
        border: 1px solid #ddd;
        font-size: 14px;
    }

    .invoice-footer p {
        font-size: 16px;
        margin: 10px 0;
        text-align: right;
    }

    .invoice-footer strong {
        font-size: 18px;
        color: #004aad;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        backdrop-filter: blur(5px);
        background-color: rgba(0, 0, 0, 0.2);
    }

    .modal-content {
        position: relative;
        margin: 50px auto;
        padding: 20px;
        width: 60%;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        animation: fadeIn 0.3s ease-in-out;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        color: #333;
        font-size: 20px;
        cursor: pointer;
    }

    .court-info {
        margin-bottom: 10px;
        padding: 5px;
        border-bottom: 1px solid #eee;
    }

    .court-info:last-child {
        border-bottom: none;
    }

    .court-info strong {
        color: #004aad;
    }

    .invoice-table td {
        vertical-align: top;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>

<!-- Header End -->
<div class="container mt-5">
    <h2 class="text-center mb-4 text-primary">Lịch sử giao dịch đặt sân</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Ngày chơi</th>
                    <th colspan="2">Thông tin đặt sân</th>
                    <th>Tổng tiền</th>
                    <th>Khuyến mãi</th>
                    <th>Tổng thanh toán</th>
                    <th>Tình trạng</th>
                    <th>Xem chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order):
                    $statusClass = "";
                    if ($order['tinhTrang'] == "Chờ xác nhận") {
                        $statusClass = "status-pending";
                    } elseif ($order['tinhTrang'] == "Đã xác nhận") {
                        $statusClass = "status-confirmed";
                    } elseif ($order['tinhTrang'] == "Hoàn thành") {
                        $statusClass = "status-completed";
                    }

                    // Tạo HTML cho thông tin sân
                    $courtInfo = "";
                    foreach ($order['courts'] as $court) {
                        $courtInfo .= "<div class='court-info'>";
                        $courtInfo .= "<strong>{$court['tenSan']}</strong><br>";
                        $courtInfo .= "{$court['gioChoiVaGia']}<br>";
                        $courtInfo .= "</div>";
                    }
                    ?>
                    <tr>
                        <td><b><?= $order['maDon'] ?></b></td>
                        <td><?= $order['ngayDat'] ?></td>
                        <td><?= $order['ngayChoi'] ?></td>
                        <td colspan="2"><?= $courtInfo ?></td>
                        <td><?= number_format($order['tongTien'], 0, '.', '.') ?></td>
                        <td><?= number_format($order['giaGiam'], 0, '.', '.') ?></td>
                        <td><?= number_format($order['tongThanhToan'], 0, '.', '.') ?></td>
                        <td class="<?= $statusClass ?>"><?= $order['tinhTrang'] ?></td>
                        <td>
                            <button type='button' class='open-modal-btn btn-detail'
                                data-order='<?= htmlspecialchars(json_encode($order), ENT_QUOTES, 'UTF-8') ?>'
                                onclick='openModal(this)'>Chi tiết
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal -->
        <div id="invoice-modal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <div class="invoice">
                    <div class="invoice-header">
                        <h1>HÓA ĐƠN ĐẶT SÂN</h1>
                        <p><strong>Mã đơn:</strong> <span id="modal-maDon"></span></p>
                        <p><strong>Ngày tạo:</strong> <span id="modal-ngayDat"></span></p>
                        <p><strong>Ngày chơi:</strong> <span id="modal-ngayChoi"></span></p>
                    </div>

                    <div class="invoice-body">
                        <h2>Thông tin sân</h2>
                        <table class="invoice-table">
                            <thead>
                                <tr>
                                    <th>Tên sân</th>
                                    <th>Thời gian và giá</th>
                                </tr>
                            </thead>
                            <tbody id="modal-court-details">
                                <!-- Sẽ được điền bởi JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <div class="invoice-footer">
                        <p><strong>Tổng hóa đơn:</strong> <span id="modal-tongTien"></span></p>
                        <p><strong>Chiết khấu:</strong> <span id="modal-giaGiam"></span></p>
                        <p><strong>TỔNG THANH TOÁN:</strong> <span id="modal-tongThanhToan"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openModal(button) {
                const modal = document.getElementById("invoice-modal");
                const data = JSON.parse(button.getAttribute("data-order"));

                // Điền thông tin cơ bản
                document.getElementById("modal-maDon").innerText = data.maDon;
                document.getElementById("modal-ngayDat").innerText = data.ngayDat;
                document.getElementById("modal-ngayChoi").innerText = data.ngayChoi;

                // Điền thông tin sân
                const courtDetailsContainer = document.getElementById("modal-court-details");
                courtDetailsContainer.innerHTML = '';

                data.courts.forEach(court => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><strong>${court.tenSan}</strong></td>
                        <td>${court.gioChoiVaGia}</td>
                    `;
                    courtDetailsContainer.appendChild(row);
                });

                // Điền thông tin thanh toán
                document.getElementById("modal-tongTien").innerText = Number(data.tongTien).toLocaleString("vi-VN") + " đ";
                document.getElementById("modal-giaGiam").innerText = Number(data.giaGiam).toLocaleString("vi-VN") + " đ";
                document.getElementById("modal-tongThanhToan").innerText = Number(data.tongThanhToan).toLocaleString("vi-VN") + " đ";

                modal.style.display = "block";
            }

            function closeModal() {
                document.getElementById("invoice-modal").style.display = "none";
            }
        </script>
    </div>
</div>

<?php include("../layout/footer.php"); ?>