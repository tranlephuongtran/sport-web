<?php
session_start();
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");
$str = "SELECT tensan, khunggio FROM san";
$result = $conn->query($str);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['date'])) {
        $_SESSION['booking']['date'] = $_POST['date'];
    }

    if (isset($_POST['court'])) {
        $court = $_POST['court'];
        if (!isset($_SESSION['booking']['courts'][$court])) {
            $_SESSION['booking']['courts'][$court] = [];
        }
        $_SESSION['booking']['selected_court'] = $court;
    }

    if (isset($_POST['time_slot']) && isset($_SESSION['booking']['selected_court'])) {
        $selected_court = $_SESSION['booking']['selected_court'];
        $selected_time = $_POST['time_slot'];

        if (!isset($_SESSION['booking']['courts'][$selected_court])) {
            $_SESSION['booking']['courts'][$selected_court] = [];
        }

        if (in_array($selected_time, $_SESSION['booking']['courts'][$selected_court])) {
            $_SESSION['booking']['courts'][$selected_court] = array_diff(
                $_SESSION['booking']['courts'][$selected_court],
                [$selected_time]
            );
        } else {
            $_SESSION['booking']['courts'][$selected_court][] = $selected_time;
        }
    }

    if (isset($_POST['remove_time_slot']) && isset($_POST['court'])) {
        $court = $_POST['court'];
        $time = $_POST['remove_time_slot'];
        if (isset($_SESSION['booking']['courts'][$court])) {
            $_SESSION['booking']['courts'][$court] = array_diff($_SESSION['booking']['courts'][$court], [$time]);
        }
    }

    // Loại bỏ các sân không có khung giờ nào đã chọn
    foreach ($_SESSION['booking']['courts'] as $court => $times) {
        if (empty($times)) {
            unset($_SESSION['booking']['courts'][$court]);
        }
    }

    // Tính tổng tiền
    $total_price = 0;
    foreach ($_SESSION['booking']['courts'] as $times) {
        $total_price += count($times) * 50000;
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
        margin-top: 20px;
        margin-bottom: 20px;
        padding: 20px;
        border-radius: 8px;
        background-color: #ffffff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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

    .time-slots button {
        margin: 5px;
        padding: 8px 12px;
        border-radius: 5px;
        border: 1px solid #004aad;
        background-color: #e0e9f5;
        transition: all 0.3s;
    }

    .time-slots button:hover,
    .time-slots button.selected {
        background-color: #004aad;
        color: white;
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
</style>
<div class="container">
    <div class="row">
        <div class="col-md-3 section" style="margin-top: 100px; height: fit-content; position: relative; right: 40px">
            <h5 style="color: #004aad;">Chọn sân:</h5>
            <form method="POST">

                <?php foreach (["Sân 01", "Sân 02", "Sân 03", "Sân 04"] as $court_option): ?>
                    <button type="submit" name="court" value="<?= $court_option ?>"
                        class="btn-custom <?= $selected_court == $court_option ? 'selected' : '' ?>">
                        <?= $court_option ?>
                    </button>
                <?php endforeach; ?>
            </form>
        </div>

        <div class="col-md-6 section" style="position: relative; right: 20px">
            <h4 style="color: #004aad;">Đặt sân</h4>
            <form method="POST">
                <label for="date">Chọn ngày</label>
                <input type="date" id="date" name="date" class="form-control" value="<?= htmlspecialchars($date) ?>"
                    onchange="this.form.submit()">
            </form>

            <h5 style="color: #004aad; margin-top: 20px">Chọn khung giờ:</h5>
            <div class="time-slots">
                <?php
                $time_slots = [
                    "05:30 - 06:00",
                    "06:00 - 06:30",
                    "06:30 - 07:00",
                    "07:00 - 07:30",
                    "07:30 - 08:00",
                    "08:00 - 08:30",
                    "08:30 - 09:00",
                    "09:00 - 09:30",
                    "09:30 - 10:00",
                    "10:00 - 10:30",
                    "10:30 - 11:00",
                    "11:00 - 11:30",
                    "11:30 - 12:00",
                    "12:00 - 12:30",
                    "12:30 - 13:00"
                ];
                if ($selected_court) {
                    $selected_times = $courts[$selected_court] ?? [];
                    foreach ($time_slots as $slot) {
                        $selected = in_array($slot, $selected_times) ? "selected" : "";
                        echo "<form method='POST' style='display:inline;'>
                                <input type='hidden' name='time_slot' value='$slot'>
                                <button type='submit' class='btn btn-outline-primary $selected'>$slot</button>
                              </form>";
                    }
                } else {
                    echo "<p>Chọn sân trước khi chọn khung giờ</p>";
                }
                ?>
            </div>
        </div>

        <div class="col-md-3 section">
            <h4 style="color: #004aad; position: relative; left: 40px">Thông tin đặt sân</h4>
            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Ngày chơi:</span> <strong><?= htmlspecialchars($date) ?></strong>
                </li>
                <li class="list-group-item">
                    <span>Các sân đã chọn:</span>
                    <ul>
                        <?php foreach ($courts as $court_name => $times): ?>
                            <li><strong><?= htmlspecialchars($court_name) ?></strong></li>
                            <ul>
                                <?php foreach ($times as $t): ?>
                                    <li>
                                        <?= htmlspecialchars($t) ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="court" value="<?= $court_name ?>">
                                            <input type="hidden" name="remove_time_slot" value="<?= $t ?>">
                                            <button type="submit" class="btn-remove"><i class="fas fa-times"></i></button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Tổng tiền:</span> <strong><?= number_format($total_price, 0, ',', '.') ?> VND</strong>
                </li>
            </ul>
            <button class="btn-primary-order">Đặt sân</button>
        </div>
    </div>
</div>

<?php include("../layout/footer.php"); ?>