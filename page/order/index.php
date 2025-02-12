<?php
session_start();
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Khởi tạo session booking nếu chưa có
if (!isset($_SESSION['booking'])) {
    $_SESSION['booking'] = [
        'courts' => [],
        'date' => null,
        'selected_court' => null,
        'total_price' => 0
    ];
}

// Lấy danh sách sân
$str = "SELECT maSan, tenSan FROM san";
$result = $conn->query($str);

// Xử lý POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['date'])) {
        $_SESSION['booking']['date'] = $_POST['date'];
    }

    if (isset($_POST['court'])) {
        $court = $_POST['court'];
        $_SESSION['booking']['selected_court'] = $court;
    }

    if (isset($_POST['time_slot']) && isset($_POST['price']) && isset($_SESSION['booking']['selected_court'])) {
        $selected_court = $_SESSION['booking']['selected_court'];
        $selected_time = $_POST['time_slot'];
        $price = $_POST['price'];

        if (!isset($_SESSION['booking']['courts'][$selected_court])) {
            $_SESSION['booking']['courts'][$selected_court] = [];
        }

        // Kiểm tra xem khung giờ đã được chọn chưa
        $time_exists = false;
        foreach ($_SESSION['booking']['courts'][$selected_court] as $key => $booking_time) {
            if ($booking_time['time'] == $selected_time) {
                unset($_SESSION['booking']['courts'][$selected_court][$key]);
                $time_exists = true;
                break;
            }
        }

        // Nếu chưa có, thêm vào
        if (!$time_exists) {
            $_SESSION['booking']['courts'][$selected_court][] = [
                'time' => $selected_time,
                'price' => $price
            ];
        }

        // Sắp xếp lại mảng
        $_SESSION['booking']['courts'][$selected_court] = array_values($_SESSION['booking']['courts'][$selected_court]);
    }

    if (isset($_POST['remove_time_slot']) && isset($_POST['court'])) {
        $court = $_POST['court'];
        $time = $_POST['remove_time_slot'];

        if (isset($_SESSION['booking']['courts'][$court])) {
            foreach ($_SESSION['booking']['courts'][$court] as $key => $booking_time) {
                if ($booking_time['time'] == $time) {
                    unset($_SESSION['booking']['courts'][$court][$key]);
                    break;
                }
            }
            $_SESSION['booking']['courts'][$court] = array_values($_SESSION['booking']['courts'][$court]);
        }
    }

    // Loại bỏ các sân không có khung giờ
    foreach ($_SESSION['booking']['courts'] as $court => $times) {
        if (empty($times)) {
            unset($_SESSION['booking']['courts'][$court]);
        }
    }

    // Tính tổng tiền
    $total_price = 0;
    foreach ($_SESSION['booking']['courts'] as $court => $bookings) {
        foreach ($bookings as $booking) {
            $total_price += $booking['price'];
        }
    }
    $_SESSION['booking']['total_price'] = $total_price;
}

$booking = $_SESSION['booking'] ?? [];
$date = $booking['date'] ?? 'Chưa chọn';
$courts = $booking['courts'] ?? [];
$selected_court = $booking['selected_court'] ?? null;
$total_price = $booking['total_price'] ?? 0;

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
    }

    .section {
        margin: 20px 0;
        padding: 20px;
        border-radius: 8px;
        background-color: #ffffff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .left-section {
        margin-top: 100px;
    }

    .middle-section {
        min-height: 500px;
    }

    .right-section {
        position: sticky;
        top: 20px;
    }

    .btn-custom {
        display: block;
        width: 100%;
        padding: 10px;
        margin-top: 10px;
        border: none;
        background-color: #e0e9f5;
        color: black;
        text-align: center;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-custom:hover,
    .btn-custom.selected {
        background-color: #004aad;
        color: white;
    }

    .time-slots {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
        margin-top: 20px;
    }

    .time-slots form,
    .time-slots button {
        width: 100%;
    }

    .time-slot-button {
        width: 100%;
        padding: 12px 15px;
        border-radius: 8px;
        border: none;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
    }

    /* Sửa lại style cho button đã đặt */
    .time-slot-button.booked {
        background-color: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
        width: 100%;
        border: none;
    }

    .time-slot-time {
        font-size: 15px;
        font-weight: bold;
    }

    .time-slot-price {
        font-size: 13px;
        opacity: 0.9;
    }

    .btn-primary-order {
        background-color: #004aad;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        color: white;
        text-align: center;
        cursor: pointer;
        width: 100%;
        margin-top: 20px;
        transition: 0.3s;
    }

    .btn-primary-order:hover {
        background-color: black;
        color: white;
    }

    .btn-remove {
        color: red;
        border: none;
        background: none;
        cursor: pointer;
        margin-left: 10px;
    }

    .btn-secondary-select {
        background-color: #004aad !important;
        color: white !important;
    }

    .booking-info {
        padding: 15px;
    }

    .booking-info ul {
        list-style: none;
        padding-left: 0;
    }

    .booking-info ul ul {
        padding-left: 20px;
    }

    .booking-info li {
        margin-bottom: 10px;
    }

    .list-group-item {
        border: none;
        padding: 10px 0;
    }

    /* Thêm phần ghi chú màu sắc */
    .color-note {
        display: flex;
        flex-direction: row;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .color-note-item {
        display: flex;
        align-items: center;
        margin: 8px 0;
        margin-left: 40px;
    }

    .color-box {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        margin-right: 10px;
    }

    .color-box.available {
        background-color: rgb(255, 255, 255);
        border: 1px solid #004aad;
    }

    .color-box.selected {
        background-color: #004aad;
    }

    .color-box.booked {
        background-color: #677080;
    }
</style>

<div class="container">
    <div class="row">
        <!-- Phần chọn sân -->
        <div class="col-md-3">
            <div class="section left-section">
                <h5 style="color: #004aad;">Chọn sân:</h5>
                <form method="POST">
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $court_option = $row['tenSan'];
                            ?>
                            <button type="submit" name="court" value="<?= $court_option ?>"
                                class="btn-custom <?= $selected_court == $court_option ? 'selected' : '' ?>">
                                <?= $court_option ?>
                            </button>
                        <?php }
                    }
                    ?>
                </form>
            </div>
        </div>

        <!-- Phần chọn ngày và khung giờ -->
        <div class="col-md-6">
            <div class="section middle-section">
                <h4 style="color: #004aad;">Đặt sân</h4>
                <form method="POST">
                    <label for="date">Chọn ngày</label>
                    <input type="date" id="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>"
                        onchange="this.form.submit()">
                </form>

                <h5 style="color: #004aad; margin-top: 20px">Chọn khung giờ:</h5>
                <div class="color-note">
                    <div class="color-note-item">
                        <div class="color-box available"></div>
                        <span>Khung giờ trống</span>
                    </div>
                    <div class="color-note-item">
                        <div class="color-box selected"></div>
                        <span>Đang chọn</span>
                    </div>
                    <div class="color-note-item">
                        <div class="color-box booked"></div>
                        <span>Đã có người đặt</span>
                    </div>
                </div>
                <div class="time-slots">
                    <?php
                    if ($selected_court && $date != 'Chưa chọn') {
                        // Lấy mã sân
                        $query_masan = "SELECT maSan FROM san WHERE tenSan = ?";
                        $stmt_masan = mysqli_prepare($conn, $query_masan);
                        mysqli_stmt_bind_param($stmt_masan, "s", $selected_court);
                        mysqli_stmt_execute($stmt_masan);
                        $result_masan = mysqli_stmt_get_result($stmt_masan);

                        if ($row_masan = mysqli_fetch_assoc($result_masan)) {
                            $maSan = $row_masan['maSan'];

                            // Lấy thông tin giá và khung giờ
                            $query = "SELECT khungGioStart, khungGioEnd, giaKhoangCach, khoangCachGio 
                     FROM giasan 
                     WHERE maSan = ?
                     ORDER BY khungGioStart";

                            $stmt = mysqli_prepare($conn, $query);
                            mysqli_stmt_bind_param($stmt, "i", $maSan);
                            mysqli_stmt_execute($stmt);
                            $result_slots = mysqli_stmt_get_result($stmt);

                            if (!$result_slots) {
                                echo "Lỗi truy vấn: " . mysqli_error($conn);
                            } else {
                                $time_slots = [];
                                while ($row = mysqli_fetch_assoc($result_slots)) {
                                    $start = strtotime($row['khungGioStart']);
                                    $end = strtotime($row['khungGioEnd']);
                                    $interval = $row['khoangCachGio'] * 60;
                                    $price = $row['giaKhoangCach'];

                                    for ($time = $start; $time < $end; $time += $interval) {
                                        $slot_start = date('H:i', $time);
                                        $slot_end = date('H:i', $time + $interval);
                                        $time_slots[] = [
                                            'time' => "$slot_start - $slot_end",
                                            'price' => $price
                                        ];
                                    }
                                }

                                // Lấy các khung giờ đã đặt
                                $query_booked = "SELECT khungGio 
                               FROM tinhtrangsan 
                               WHERE maSan = ? 
                               AND ngayDat = ?";

                                $stmt_booked = mysqli_prepare($conn, $query_booked);
                                mysqli_stmt_bind_param($stmt_booked, "is", $maSan, $date);
                                mysqli_stmt_execute($stmt_booked);
                                $result_booked = mysqli_stmt_get_result($stmt_booked);

                                $booked_times = [];
                                while ($row = mysqli_fetch_assoc($result_booked)) {
                                    $booked_slots = explode(", ", $row['khungGio']);
                                    $booked_times = array_merge($booked_times, $booked_slots);
                                }

                                // Hiển thị các khung giờ
                                foreach ($time_slots as $slot) {
                                    $is_booked = in_array($slot['time'], $booked_times);
                                    $is_selected = false;
                                    if (isset($_SESSION['booking']['courts'][$selected_court])) {
                                        foreach ($_SESSION['booking']['courts'][$selected_court] as $booking) {
                                            if ($booking['time'] == $slot['time']) {
                                                $is_selected = true;
                                                break;
                                            }
                                        }
                                    }

                                    if ($is_booked) {
                                        echo "<button class='btn btn-dark' disabled>{$slot['time']}</button> ";
                                    } else {
                                        echo "<form method='POST' style='display:inline;'>
                            <input type='hidden' name='time_slot' value='{$slot['time']}'>
                            <input type='hidden' name='price' value='{$slot['price']}'>
                            <button type='submit' class='btn " . ($is_selected ? "btn-secondary-select" : "btn-outline-primary") . "'>
                                {$slot['time']} (" . number_format($slot['price']) . " VND)
                            </button>
                        </form> ";
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </div>

            </div>
        </div>

        <!-- Phần thông tin đặt sân -->
        <div class="col-md-3">
            <div class="section right-section">
                <h4 style="color: #004aad;">Thông tin đặt sân</h4>
                <div class="booking-info">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Ngày chơi:</span>
                            <strong><?= htmlspecialchars($date) ?></strong>
                        </li>
                        <li class="list-group-item">
                            <span>Các sân đã chọn:</span>
                            <ul>
                                <?php foreach ($courts as $court_name => $bookings): ?>
                                    <li>
                                        <strong><?= htmlspecialchars($court_name) ?></strong>
                                        <ul>
                                            <?php foreach ($bookings as $booking): ?>
                                                <li>
                                                    <?= htmlspecialchars($booking['time']) ?>
                                                    <br>
                                                    <small><?= number_format($booking['price']) ?> VND</small>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="court" value="<?= $court_name ?>">
                                                        <input type="hidden" name="remove_time_slot"
                                                            value="<?= $booking['time'] ?>">
                                                        <button type="submit" class="btn-remove">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Tổng tiền:</span>
                            <strong><?= number_format($total_price, 0, ',', '.') ?> VND</strong>
                        </li>
                    </ul>
                </div>
                <button class="btn-primary-order">Đặt sân</button>
            </div>
        </div>
    </div>
</div>

<?php include("../layout/footer.php"); ?>