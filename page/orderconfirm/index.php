<?php
include("../layout/header.php");
session_start();

// Kết nối database
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

// Lấy thông tin từ session booking
$booking = $_SESSION['booking'] ?? [];
$date = $booking['date'] ?? 'Chưa chọn';
$courts = $booking['courts'] ?? [];
$total_price = $booking['total_price'] ?? 0;
$discount = $booking['discount'] ?? ['amount' => 0, 'code' => '', 'type' => '', 'name' => ''];

// Xử lý áp dụng khuyến mãi
if (isset($_POST['apply_promotion']) && isset($_POST['promotion_id'])) {
    $discount_id = $_POST['promotion_id'];

    $query = "SELECT * FROM khuyenmai WHERE maKM = ? AND trangThai = 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $discount_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $discount_amount = $row['giaGiam'];
        $discount_type = $row['loaiGiamGia'];

        $discount_value = ($discount_type == 'Phần trăm') ?
            $total_price * ($discount_amount / 100) : $discount_amount;

        $_SESSION['booking']['discount'] = [
            'amount' => $discount_value,
            'code' => $row['maKM'],
            'type' => $discount_type,
            'name' => $row['tenKM']
        ];

        $_SESSION['booking']['final_price'] = $total_price - $discount_value;
        $discount = $_SESSION['booking']['discount'];
    }
}

// Xử lý xóa khuyến mãi
if (isset($_POST['remove_promotion'])) {
    unset($_SESSION['booking']['discount']);
    $_SESSION['booking']['final_price'] = $total_price;
    $discount = ['amount' => 0, 'code' => '', 'type' => '', 'name' => ''];
}

// Lấy danh sách khuyến mãi
$query = "SELECT * FROM khuyenmai WHERE trangThai = 1";
$promotions = mysqli_query($conn, $query);
?>

<style>
    /* Card styles */
    .card {
        background: linear-gradient(145deg, #ffffff, #f5f7fa);
        border: none;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 20px rgba(0, 123, 255, 0.1);
        margin-bottom: 30px;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    /* Table styles */
    .table {
        margin-top: 20px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        color: #495057;
        font-weight: 600;
        padding: 15px;
    }

    .table tbody td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #dee2e6;
    }

    /* Modal styles */
    .promotion-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1000;
        backdrop-filter: blur(5px);
    }

    .promotion-content {
        position: relative;
        background: white;
        width: 90%;
        max-width: 600px;
        margin: 30px auto;
        padding: 30px;
        border-radius: 20px;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Promotion item styles */
    .promotion-item {
        border: 2px solid #e9ecef;
        padding: 20px;
        margin: 15px 0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .promotion-item:hover {
        border-color: #007bff;
        background: linear-gradient(145deg, #f8f9fa, #ffffff);
        transform: translateY(-2px);
    }

    .promotion-item.selected {
        border-color: #007bff;
        background: linear-gradient(145deg, #e7f1ff, #f8f9fa);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.1);
    }

    .promotion-item h6 {
        color: #2c3e50;
        font-size: 1.1rem;
        margin-bottom: 10px;
    }

    .promotion-item p {
        color: #6c757d;
        margin-bottom: 10px;
        line-height: 1.5;
    }

    /* Total section styles */
    .total-section {
        background: linear-gradient(145deg, #ffffff, #f8f9fa);
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    }

    .total-section h5 {
        color: #2c3e50;
        margin-bottom: 20px;
        font-weight: 600;
    }

    /* Button styles */
    .btn-outline-primary {
        border-width: 2px;
        font-weight: 500;
        padding: 8px 20px;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.2);
    }

    .btn-apply {
        background: linear-gradient(145deg, #007bff, #0056b3);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 10px;
        width: 100%;
        margin-top: 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-apply:hover {
        background: linear-gradient(145deg, #0056b3, #004085);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
    }

    /* Badge styles */
    .promotion-badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 15px;
        background: linear-gradient(145deg, #e7f1ff, #f8f9fa);
        color: #007bff;
        border-radius: 8px;
        margin-top: 10px;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0, 123, 255, 0.1);
    }

    .btn-remove-promotion {
        color: #dc3545;
        cursor: pointer;
        margin-left: 10px;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }

    .btn-remove-promotion:hover {
        color: #c82333;
        transform: scale(1.1);
    }

    /* Close modal button */
    .close-modal {
        position: absolute;
        right: 20px;
        top: 20px;
        font-size: 28px;
        cursor: pointer;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .close-modal:hover {
        color: #343a40;
        transform: rotate(90deg);
    }
</style>

<!-- Main Content -->
<div class="container mt-5">
    <div class="row">
        <!-- Order Information -->
        <div class="col-md-8">
            <div class="card">
                <h4>Thông tin đơn hàng</h4>
                <div class="order-info">
                    <p>Mã đơn: <strong>#<?php echo "GOVAP-" . date('ymd') . "-" . rand(10, 99); ?></strong></p>
                    <p>Ngày đặt: <strong><?php echo date('d/m/Y'); ?></strong></p>
                    <p>Ngày chơi: <strong><?php echo date('d/m/Y', strtotime($date)); ?></strong></p>
                </div>

                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>Sân</th>
                            <th>Thời gian</th>
                            <th class="text-right">Giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courts as $court_name => $bookings): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($court_name); ?></td>
                                    <td><?php echo htmlspecialchars($booking['time']); ?></td>
                                    <td class="text-right"><?php echo number_format($booking['price'], 0, ',', '.'); ?>đ
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Total Section -->
        <div class="col-md-4">
            <div class="total-section">
                <h5>Tổng cộng</h5>
                <div class="d-flex justify-content-between mb-3">
                    <span>Tổng tiền sân:</span>
                    <span><?php echo number_format($total_price, 0, ',', '.'); ?>đ</span>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span>Khuyến mãi:</span>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="openPromotionModal()">
                        Chọn khuyến mãi
                    </button>
                </div>

                <div id="applied-promotion">
                    <?php if ($discount['code']): ?>
                        <div class="promotion-badge">
                            <?php echo $discount['name']; ?>
                            <form method="POST" style="display: inline;"
                                onsubmit="return confirm('Bạn có chắc muốn xóa khuyến mãi này?');">
                                <input type="hidden" name="remove_promotion" value="1">
                                <button type="submit" class="btn-remove-promotion"
                                    style="border: none; background: none;">×</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span>Giảm giá:</span>
                    <span id="discount-amount">-<?php echo number_format($discount['amount'], 0, ',', '.'); ?>đ</span>
                </div>

                <div class="d-flex justify-content-between mb-3 font-weight-bold">
                    <span>TỔNG THANH TOÁN:</span>
                    <span id="final-price">
                        <?php echo number_format($_SESSION['booking']['final_price'] ?? $total_price, 0, ',', '.'); ?>đ
                    </span>
                </div>

                <button class="btn btn-primary btn-block" onclick="window.location.href='index.php?payment'">
                    Tiến hành thanh toán
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Promotion Modal -->
<div id="promotionModal" class="promotion-modal">
    <div class="promotion-content">
        <span class="close-modal" onclick="closePromotionModal()">&times;</span>
        <h5>Chọn khuyến mãi</h5>
        <form method="POST">
            <div id="promotion-list">
                <?php
                mysqli_data_seek($promotions, 0);
                while ($promo = mysqli_fetch_assoc($promotions)):
                    ?>
                    <div class="promotion-item" onclick="selectPromotion('<?php echo $promo['maKM']; ?>', this)">
                        <h6><?php echo $promo['tenKM']; ?></h6>
                        <p><?php echo $promo['noiDungChuongTrinh']; ?></p>
                        <strong class="text-danger">
                            <?php
                            if ($promo['loaiGiamGia'] == 'Phần trăm') {
                                echo "Giảm " . $promo['giaGiam'] . "%";
                            } else {
                                echo "Giảm " . number_format($promo['giaGiam'], 0, ',', '.') . "đ";
                            }
                            ?>
                        </strong>
                    </div>
                <?php endwhile; ?>
            </div>
            <input type="hidden" name="promotion_id" id="selected_discount">
            <button type="submit" name="apply_promotion" class="btn-apply">
                Áp dụng khuyến mãi
            </button>
        </form>
    </div>
</div>

<script>
    let selectedPromotionId = null;

    function openPromotionModal() {
        document.getElementById('promotionModal').style.display = 'block';
    }

    function closePromotionModal() {
        document.getElementById('promotionModal').style.display = 'none';
    }

    function selectPromotion(promotionId, element) {
        document.querySelectorAll('.promotion-item').forEach(item => {
            item.classList.remove('selected');
        });
        element.classList.add('selected');
        document.getElementById('selected_discount').value = promotionId;
    }

    // Close modal when clicking outside
    window.onclick = function (event) {
        let modal = document.getElementById('promotionModal');
        if (event.target == modal) {
            closePromotionModal();
        }
    }
</script>

<?php include("../layout/footer.php"); ?>