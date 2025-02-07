<?php include("../layout/header.php");
if (!isset($_GET['history'])) {
    $history = 1;
} else {
    $history = $_GET['history'];
} ?>

<?php include("../layout/header.php"); ?>
<div class="container mt-5">
    <h2 class="text-center mb-4">Lịch sử giao dịch đặt sân</h2>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Mã đơn</th>
                <th>Ngày đặt</th>
                <th>Sân chơi</th>
                <th>Giờ chơi</th>
                <th>Tổng tiền (VND)</th>
                <th>Khuyến mãi (VND)</th>
                <th>Tổng thanh toán (VND)</th>
                <th>Tình trạng</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>HD001</td>
                <td>2025-02-05</td>
                <td>Sân 1</td>
                <td>
                    7h30-8h00 <br>
                    8h00-8h30
                </td>
                <td>200,000</td>
                <td>50,000</td>
                <td>150,000</td>
                <td class="status-paid">Đã thanh toán</td>
            </tr>
            <tr>
                <td>HD002</td>
                <td>2025-02-05</td>
                <td>Sân 2</td>
                <td>
                    19h00-19h30 <br>
                    19h30-20h00
                </td>
                <td>250,000</td>
                <td>0</td>
                <td>250,000</td>
                <td class="status-unpaid">Chưa thanh toán</td>
            </tr>
        </tbody>
    </table>
</div>
<?php include("../layout/footer.php"); ?>