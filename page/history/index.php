<?php include("../layout/header.php");
if (!isset($_GET['history'])) {
    $history = 1;
} else {
    $history = $_GET['history'];
}
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");
$user = $_SESSION['login'];
$str = "SELECT 
    dds.maDon, dds.ngayDat, dds.ngayChoi, 
    GROUP_CONCAT(DISTINCT s.tenSan ORDER BY s.tenSan SEPARATOR '<br>') AS danhSachSan,
    REPLACE(GROUP_CONCAT(DISTINCT cthd.gioChoi ORDER BY cthd.gioChoi SEPARATOR ', '), ', ', '<br>') AS danhSachGioChoi,
    dds.tongTien, km.giaGiam, dds.tongThanhToan, dds.tinhTrang
FROM dondatsan dds
LEFT JOIN chitiethoadon cthd ON dds.maDon = cthd.maDon 
LEFT JOIN khachhang kh ON dds.maKH = kh.maKH 
LEFT JOIN san s ON cthd.maSan = s.maSan
LEFT JOIN khuyenmai km ON dds.maKM = km.maKM
WHERE kh.maNguoiDung = $user
GROUP BY dds.maDon;

";

$result = $conn->query($str);
?>

<style>
    /* Màu chủ đạo */
    .text-primary {
        color: #004aad !important;
        font-weight: bold;
    }

    /* Bảng có viền bo tròn và màu sắc tinh tế */
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

    /* Hiệu ứng hover trên hàng */
    .table-hover tbody tr:hover {
        background-color: #f0f5ff;
    }

    /* Cột trạng thái */
    /* Trạng thái màu sắc */
    .status-pending {
        color: #ffc107;
        /* Vàng */
        font-weight: bold;
    }

    .status-confirmed {
        color: #007bff;
        /* Xanh dương */
        font-weight: bold;
    }

    .status-completed {
        color: #28a745;
        /* Xanh lá */
        font-weight: bold;
    }


    /* Nút Chi Tiết */
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

    /* Styling for the invoice */
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

    .total-section p {
        font-size: 14px;
        margin: 5px 0;
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

    /* Hiệu ứng làm mờ nền */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        backdrop-filter: blur(5px);
        /* Làm mờ nền */
        background-color: rgba(0, 0, 0, 0.2);
        /* Màu nền tối mờ */
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
                    <th>Sân chơi</th>
                    <th>Giờ chơi</th>
                    <th>Tổng tiền</th>
                    <th>Khuyến mãi</th>
                    <th>Tổng thanh toán</th>
                    <th>Tình trạng</th>
                    <th>Xem chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Xác định lớp CSS theo trạng thái
                        $statusClass = "";
                        if ($row['tinhTrang'] == "Chờ xác nhận") {
                            $statusClass = "status-pending";
                        } elseif ($row['tinhTrang'] == "Đã xác nhận") {
                            $statusClass = "status-confirmed";
                        } elseif ($row['tinhTrang'] == "Hoàn thành") {
                            $statusClass = "status-completed";
                        }

                        echo "<tr>
                                <td><b>{$row['maDon']}</b></td>
                                <td>{$row['ngayDat']}</td>
                                <td>{$row['ngayChoi']}</td>
                                <td>{$row['danhSachSan']}</td>
                                <td>{$row['danhSachGioChoi']}</td>
                                <td>" . number_format($row['tongTien'], 0, '.', '.') . "</td>
                                <td>" . number_format($row['giaGiam'], 0, '.', '.') . "</td>
                                <td>" . number_format($row['tongThanhToan'], 0, '.', '.') . "</td>
                                <td class='{$statusClass}'>{$row['tinhTrang']}</td>
                                <td> 
                                <button type='button' class='open-modal-btn btn-detail' 
                                    data-maDon='{$row['maDon']}'
                                    data-ngayDat='{$row['ngayDat']}'
                                    data-ngayChoi='{$row['ngayChoi']}'
                                    data-danhSachSan='{$row['danhSachSan']}'
                                    data-danhSachGioChoi='{$row['danhSachGioChoi']}'
                                    data-tongTien='{$row['tongTien']}'
                                    data-giaGiam='{$row['giaGiam']}'
                                    data-tongThanhToan='{$row['tongThanhToan']}'
                                    data-tinhTrang='{$row['tinhTrang']}'
                                    onclick='openModal(this)'>Chi tiết
                                </button> 
                            </td>

                                </tr>";
                    }

                }

                ?>
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
                                    <th>Thời gian</th>
                                    <th>T.Tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span id="modal-danhSachSan"></span></td>
                                    <td><span id="modal-danhSachGioChoi"></span> (20.000 đ)</td>
                                    <td>40.000đ</td>
                                </tr>
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
                document.getElementById("invoice-modal").style.display = "block";

                // Lấy dữ liệu từ button
                let maDon = button.getAttribute("data-maDon");
                let ngayDat = button.getAttribute("data-ngayDat");
                let ngayChoi = button.getAttribute("data-ngayChoi");
                let danhSachSan = button.getAttribute("data-danhSachSan").replace(/<br>/g, "\n");
                let danhSachGioChoi = button.getAttribute("data-danhSachGioChoi").replace(/<br>/g, "\n");
                let tongTien = button.getAttribute("data-tongTien");
                let giaGiam = button.getAttribute("data-giaGiam");
                let tongThanhToan = button.getAttribute("data-tongThanhToan");
                let tinhTrang = button.getAttribute("data-tinhTrang");

                // Đổ dữ liệu vào modal
                document.getElementById("modal-maDon").innerText = maDon;
                document.getElementById("modal-ngayDat").innerText = ngayDat;
                document.getElementById("modal-ngayChoi").innerText = ngayChoi;
                document.getElementById("modal-danhSachSan").innerText = danhSachSan;
                document.getElementById("modal-danhSachGioChoi").innerText = danhSachGioChoi;
                document.getElementById("modal-tongTien").innerText = Number(tongTien).toLocaleString("vi-VN") + " đ";
                document.getElementById("modal-giaGiam").innerText = Number(giaGiam).toLocaleString("vi-VN") + " đ";
                document.getElementById("modal-tongThanhToan").innerText = Number(tongThanhToan).toLocaleString("vi-VN") + " đ";

                document.getElementById("modal-tinhTrang").innerText = tinhTrang;
            }

            function closeModal() {
                document.getElementById("invoice-modal").style.display = "none";
            }

        </script>
    </div>
</div>


<?php include("../layout/footer.php"); ?>