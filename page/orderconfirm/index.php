<?php include("../layout/header.php"); ?>
<style>
    .card {
        background-color: #f8f9fa;
        border: 1px solid #007bff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
        margin-bottom: 20px;
    }

    .total {
        margin-left: 70%;
    }

    .total h5 strong {
        float: right;
    }

    select,
    button {
        border-radius: 10px;
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        margin-left: 5px;
        border: 2px solid #007bff;
        transition: all 0.3s ease;
    }

    select:focus,
    button:hover {
        border-color: #0056b3;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    button {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }

    button:hover {
        background-color: #0056b3;

    }

    h5 {
        font-weight: bold;
        color: #343a40;
    }
</style>
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center py-5" style="max-width: 900px;">
        <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Xác nhận thông tin</h4>
    </div>
</div>
<div class="container col-6 mt-5">

    <div class="card p-3">
        <p>Mã đơn: <strong>#GOVAP-202502-12</strong></p>
        <p>Ngày tạo: <strong>02-02-2025</strong></p>
        <p>Thời gian: <strong>21:02:37</strong></p>
        <p>Thu ngân: <strong>Administrator</strong></p>
    </div>

    <h5 class="mt-4">Thông tin sân</h5>
    <table class="table">
        <thead>
            <tr>
                <th>Tên sân</th>
                <th>Thời gian</th>
                <th>Thành Tiền</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sân 02</td>
                <td>7:00-7:30</td>
                <td>60.000 đ</td>
            </tr>
            <tr>
                <td>Sân 03</td>
                <td>7:00-7:30</td>
                <td>60.000 đ</td>
            </tr>
        </tbody>
    </table>
    <div class="total row">
        <h5 class="mt-4 form-label">Khuyến Mãi:</h5>

        <select class="col-7" id="discount">
            <option value="GiamGia01">Vui lòng chọn</option>
            <option value="GiamGia01">Giảm giá 10K</option>
            <option value="GiamGia02">Giảm giá 20K</option>
            <option value="GiamGia03">Giảm giá 30K</option>
        </select>
        <button class="btn btn-primary col-4" onclick="applyDiscount()">Áp dụng</button>

        <h5 class="mt-4">Tổng tiền sân: <strong>120.000 đ</strong></h5>
        <h5 class="mt-4">Giảm giá sân: <strong>- 20.000 đ</strong></h5>
        <h5 class="mt-4"><b>TỔNG TIỀN:</b> <strong>100.000 đ</strong></h5>

        <button class="btn btn-primary mb-2 col-6" onclick="window.location.href='index.php?payment'">Thanh
            toán</button>
        <button class="btn btn-success mb-2 col-5">In hóa đơn</button>

    </div>

</div>

<script>
    function applyDiscount() {
        const discountSelect = document.getElementById('discount');
        const selectedCoupon = discountSelect.value;
        alert(`Mã giảm giá ${selectedCoupon} đã được áp dụng.`);
        // Logic để cập nhật tổng tiền có thể được thêm vào đây
    }
</script>

<?php include("../layout/footer.php"); ?>