<?php include("../layout/header.php");
session_start();
$final_price = $_SESSION['booking']["final_price"] ?? $_SESSION['booking']["total_price"];
$bookingID = $_SESSION['booking']["order_id"];
$user = $_SESSION['maKH'];
// Kết nối database
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
<style>
    .bg-breadcrumb {
        background-color: #007bff;
    }

    .container {
        margin-top: 20px;
    }

    .border {
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .form-control {
        border-radius: 5px;
        border: 1px solid #007bff;
    }

    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .text-black {
        color: #333;
    }

    h2,
    h4 {
        color: white;
    }

    #qr-code {
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>

<form method="POST" enctype='multipart/form-data'>
    <div class="container-fluid bg-breadcrumb">
        <div class="container text-center py-5" style="max-width: 900px;">
            <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Thanh Toán </h4>
        </div>
    </div>
    <div class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="border p-4">
                        <h2 class="h5 mb-3 text-black">PHƯƠNG THỨC THANH TOÁN</h2>
                        <div class="p-3">
                            <div class="border mb-3">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input class="form-check-input" type="radio" name="paymentMethod"
                                                    value="Banking" id="banking" onchange="updatePaymentInfo('banking')"
                                                    checked>
                                                <label class="form-check-label" for="banking">
                                                    <img style="width: 90px;height: 40px;"
                                                        src="layout/img/logonganhang.png" alt="">Thanh toán ngân hàng
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input class="form-check-input" type="radio" name="paymentMethod"
                                                    value="Momo" id="momo" onchange="updatePaymentInfo('momo')">
                                                <label class="form-check-label" for="momo">
                                                    <img style="width: 40px;height: 40px;margin-left: 20px;margin-right: 30px;"
                                                        src="layout/img/logomomo.png" alt="">Ví Momo
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input class="form-check-input" type="radio" name="paymentMethod"
                                                    value="Zalopay" id="zalopay"
                                                    onchange="updatePaymentInfo('zalopay')">
                                                <label class="form-check-label" for="zalopay">
                                                    <img style="width: 50px;height: 50px;margin-left: 12px;margin-right: 30px;"
                                                        src="layout/img/logozalopay.png" alt="">Ví ZaloPay
                                                </label>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="border p-4">
                        <h2 class="h5 mb-3 text-black text-center">THÔNG TIN THANH TOÁN</h2>
                        <div class="p-5 bg-white">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Số tiền cần thanh toán</label>
                                            <input type="text" class="form-control" id="amount" readonly
                                                value="<?php echo number_format($final_price, 0, ',', '.'); ?>đ">
                                        </div>
                                        <div class="mb-3">
                                            <label for="account" class="form-label">Nội dung chuyển khoản</label>
                                            <input type="text" class="form-control" id="account" readonly
                                                value="THANHTOAN-ACEPLUS-<?php echo $bookingID ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label for="bill" class="form-label">Tải hóa đơn lên</label>
                                            <input type="file" class="form-control" id="bill" style="height: 38px;"
                                                name='billImage' accept='img/*'>
                                        </div>

                                        <button type="submit" class="btn btn-primary" style="border-radius: 10px;"
                                            name="btn-submit">Xác nhận</button>
                                    </div>
                                </div>

                                <!-- QR Code Image -->
                                <div class="col-md-6 d-flex align-items-center justify-content-center">
                                    <img id="qr-code" src="layout/img/QR-nganhang.png" alt="QR Code for Payment"
                                        width="100%" height="100%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
    function updatePaymentInfo(paymentMethod) {
        const qrCodeImage = document.getElementById('qr-code');
        qrCodeImage.style.display = 'block';

        switch (paymentMethod) {
            case 'banking':
                qrCodeImage.src = 'layout/img/QR-nganhang.png';
                break;
            case 'momo':
                qrCodeImage.src = 'layout/img/QR-momo.png';
                break;
            case 'zalopay':
                qrCodeImage.src = 'layout/img/QR-zalopay.png';
                break;
            default:
                qrCodeImage.src = '';
                qrCodeImage.style.display = 'none';
        }
    }
    window.onload = function () {
        updatePaymentInfo('banking');
    }
</script>

<?php
if (isset($_POST['btn-submit'])) {
    $payMethod = $_POST['paymentMethod'];
    $targetDir = "layout/img/bills/";
    $fileName = isset($_FILES["billImage"]["name"]) ? basename($_FILES["billImage"]["name"]) : "";
    $uploadFile = $targetDir . $fileName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    if (!empty($fileName)) {
        $check = getimagesize($_FILES["billImage"]["tmp_name"]);
        if ($check === false) {
            echo "<script>alert('File không phải là ảnh hợp lệ.');</script>";
            $uploadOk = 0;
        }
        if ($_FILES["billImage"]["size"] > 5000000) {
            echo "<script>alert('Kích thước ảnh quá lớn (tối đa 5MB).');</script>";
            $uploadOk = 0;
        }
        if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
            echo "<script>alert('Chỉ cho phép định dạng JPG, PNG, JPEG, GIF.');</script>";
            $uploadOk = 0;
        }

        if ($uploadOk && move_uploaded_file($_FILES["billImage"]["tmp_name"], $uploadFile)) {
            $ngayDat = date('Y-m-d H:i:s');
            $ngayChoi = $_SESSION['booking']['date'];
            $tongTien = $_SESSION['booking']['total_price'];
            $tinhTrang = 'Chờ xác nhận';
            $phuongThucThanhToan = $payMethod;
            $tongThanhToan = $final_price;
            $hinhAnh = $fileName;
            $maKM = $_SESSION['booking']['discount']['code'] ?? null;

            if (empty($maKM)) {
                $insertQuery = "
                INSERT INTO dondatsan(ngayDat, ngayChoi, tongTien, tinhTrang, phuongThucThanhToan, tongThanhToan, hinhAnh, maKH) 
                VALUES (STR_TO_DATE('$ngayDat', '%Y-%m-%d %H:%i:%s'), 
                        STR_TO_DATE('$ngayChoi', '%Y-%m-%d'), 
                        $tongTien, 
                        '$tinhTrang', 
                        '$phuongThucThanhToan', 
                        $tongThanhToan, 
                        '$hinhAnh', 
                        $user)";
            } else {
                $insertQuery = "
                INSERT INTO dondatsan(ngayDat, ngayChoi, tongTien, tinhTrang, phuongThucThanhToan, tongThanhToan, hinhAnh, maKM, maKH) 
                VALUES (STR_TO_DATE('$ngayDat', '%Y-%m-%d %H:%i:%s'), 
                        STR_TO_DATE('$ngayChoi', '%Y-%m-%d'), 
                        $tongTien, 
                        '$tinhTrang', 
                        '$phuongThucThanhToan', 
                        $tongThanhToan, 
                        '$hinhAnh', 
                        $maKM, 
                        $user)";
            }

            if ($conn->query($insertQuery)) {
                $maDon = $conn->insert_id;

                try {
                    $conn->begin_transaction();

                    foreach ($_SESSION['booking']['courts'] as $court_name => $bookings) {
                        // Lấy mã sân
                        $stmt = $conn->prepare("SELECT maSan FROM san WHERE tenSan = ?");
                        $stmt->bind_param("s", $court_name);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($row = $result->fetch_assoc()) {
                            $maSan = $row['maSan'];

                            foreach ($bookings as $booking) {
                                $time = $booking['time'];
                                $price = $booking['price'];

                                echo "Inserting: maDon=$maDon, maSan=$maSan, time=$time, price=$price<br>";

                                // Insert chitiethoadon với khóa chính mới
                                $sqlChiTiet = "INSERT INTO chitiethoadon (maDon, maSan, gioChoi, giaSan) 
                                              VALUES (?, ?, ?, ?)
                                              ON DUPLICATE KEY UPDATE giaSan = VALUES(giaSan)";

                                $stmtChiTiet = $conn->prepare($sqlChiTiet);
                                if (!$stmtChiTiet) {
                                    throw new Exception("Prepare chitiethoadon failed: " . $conn->error);
                                }

                                $stmtChiTiet->bind_param("iisi", $maDon, $maSan, $time, $price);
                                if (!$stmtChiTiet->execute()) {
                                    throw new Exception("Insert chitiethoadon failed: " . $stmtChiTiet->error);
                                }

                                // Insert tinhtrangsan
                                $sqlTinhTrang = "INSERT INTO tinhtrangsan (maSan, khungGio, tinhTrang, ngayDat) 
                                                VALUES (?, ?, 1, ?)
                                                ON DUPLICATE KEY UPDATE tinhTrang = 1";

                                $stmtTinhTrang = $conn->prepare($sqlTinhTrang);
                                if (!$stmtTinhTrang) {
                                    throw new Exception("Prepare tinhtrangsan failed: " . $conn->error);
                                }

                                $stmtTinhTrang->bind_param("iss", $maSan, $time, $ngayChoi);
                                if (!$stmtTinhTrang->execute()) {
                                    throw new Exception("Insert tinhtrangsan failed: " . $stmtTinhTrang->error);
                                }
                            }
                        } else {
                            throw new Exception("Không tìm thấy mã sân: $court_name");
                        }
                    }

                    $conn->commit();
                    unset($_SESSION['booking']);
                    echo "<script>alert('Thanh toán thành công! Chờ xác nhận.');window.location.href = 'index.php?home';</script>";

                } catch (Exception $e) {
                    $conn->rollback();
                    echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";

                    // Debug error details
                    echo "<pre>Error details:\n";
                    echo $e->getMessage() . "\n";
                    echo $e->getTraceAsString() . "\n";
                    echo "</pre>";
                }
            }
        } else {
            echo "<script>alert('Không thể tải ảnh lên. Vui lòng thử lại.');</script>";
        }
    } else {
        echo "<script>alert('Vui lòng tải lên ảnh hóa đơn');</script>";
    }
}
?>

<?php include("../layout/footer.php"); ?>