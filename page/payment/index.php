<?php include("../layout/header.php"); ?>
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
            <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Thanh Toán</h4>
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
                                                    value="momo" id="momo" onchange="updatePaymentInfo('momo')">
                                                <label class="form-check-label" for="momo">
                                                    <img style="width: 40px;height: 40px;margin-left: 20px;margin-right: 30px;"
                                                        src="layout/img/logomomo.png" alt="">Ví Momo
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input class="form-check-input" type="radio" name="paymentMethod"
                                                    value="zalopay" id="zalopay"
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
                                                value="<?php //echo $moneyPay ?> 100.000 đ">
                                        </div>
                                        <div class="mb-3">
                                            <label for="account" class="form-label">Nội dung chuyển khoản</label>
                                            <input type="text" class="form-control" id="account" readonly
                                                value="THANHTOAN#GOVAP-202502-12<?php //echo $payment ?>">
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

<?php include("../layout/footer.php"); ?>