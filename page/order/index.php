<?php include("../layout/header.php") ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .time-slot {
        cursor: pointer;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.5rem;
        text-align: center;
        width: 120px;
        margin: 0.25rem;
        background-color: rgb(224, 233, 245);
        transition: background-color 0.3s;
    }

    .time-slot.selected {
        background-color: rgb(29, 94, 158);
        color: white;
    }

    .time-slot:hover {
        background-color: rgb(29, 94, 158);
        color: white;
    }

    .field-button {
        width: 100%;
        text-align: center;
        padding: 0.5rem;
        margin-bottom: 0.5rem;
        background-color: rgb(224, 233, 245);
        color: grey;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;
    }

    .field-button.selected {
        color: white;
        background-color: rgb(29, 94, 158);
    }

    .field-button:hover {
        color: white;
        background-color: rgb(29, 94, 158);
    }

    #booking-summary {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-top: 1rem;
    }

    #booking-summary .list-group-item {
        background-color: #ffffff;
        border: none;
        justify-content: space-between;
        align-items: center;
    }

    #booking-summary .list-group-item:hover {
        background-color: #e9ecef;
    }

    #total-price {
        font-weight: bold;
        color: #dc3545;
    }

    .booking-section {
        background-color: rgb(244, 244, 245);
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .info-section {
        border: 1px solid rgb(229, 238, 229);
        border-radius: 0.375rem;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
    }

    .legend {
        margin-top: 20px;
        padding: 10px;
        align-items: center;
        border-radius: 0.375rem;


    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;

    }

    .legend-color {
        width: 20px;
        height: 20px;
        margin-left: 10px;
        margin-right: 10px;
    }

    #confirm-button {
        width: 100%;
        padding: 0.5rem;
        border-radius: 0.375rem;
        transition: background-color 0.3s;
    }

    #confirm-button:hover {
        background-color: rgb(23, 162, 184);

    }
</style>
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center py-5" style="max-width: 900px;">
        <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Đặt sân</h4>
    </div>
</div>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-9 booking-section">
            <h4 class="mb-4">Đặt sân</h4>
            <form method="POST" id="booking-form">
                <div class="mb-3">
                    <label for="date" class="form-label">Chọn ngày</label>
                    <input type="date" id="date" name="date" class="form-control" style="width: 30%;" required>
                </div>
                <div class="row">
                    <div class="col-2">
                        <h5>Sân có sẵn:</h5>
                        <?php foreach (['Sân 01', 'Sân 02', 'Sân 03', 'Sân 04'] as $court): ?>
                            <button type="button" class="field-button"
                                onclick="selectCourt(this)"><?php echo $court; ?></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-10">
                        <h5>Khung giờ:</h5>
                        <div id="time-slots" class="d-flex flex-wrap">
                            <div class="time-slot">05:30 - 06:00</div>
                            <div class="time-slot">06:00 - 06:30</div>
                            <div class="time-slot">06:30 - 07:00</div>
                            <div class="time-slot">07:00 - 07:30</div>
                            <div class="time-slot">07:30 - 08:00</div>
                            <div class="time-slot">08:00 - 08:30</div>
                            <div class="time-slot">08:30 - 09:00</div>
                            <div class="time-slot">09:00 - 09:30</div>
                            <div class="time-slot">09:30 - 10:00</div>
                            <div class="time-slot">10:00 - 10:30</div>
                            <div class="time-slot">10:30 - 11:00</div>
                            <div class="time-slot">11:00 - 11:30</div>
                            <div class="time-slot">11:30 - 12:00</div>
                            <div class="time-slot">12:00 - 12:30</div>
                            <div class="time-slot">12:30 - 13:00</div>
                            <div class="time-slot">13:00 - 13:30</div>
                            <div class="time-slot">13:30 - 14:00</div>
                            <div class="time-slot">14:00 - 14:30</div>
                            <div class="time-slot">14:30 - 15:00</div>
                            <div class="time-slot">15:00 - 15:30</div>
                            <div class="time-slot">15:30 - 16:00</div>
                            <div class="time-slot">16:00 - 16:30</div>
                            <div class="time-slot">16:30 - 17:00</div>
                            <div class="time-slot">17:00 - 17:30</div>
                            <div class="time-slot">17:30 - 18:00</div>
                            <div class="time-slot">18:00 - 18:30</div>
                            <div class="time-slot">18:30 - 19:00</div>
                            <div class="time-slot">19:00 - 19:30</div>
                            <div class="time-slot">19:30 - 20:00</div>
                            <div class="time-slot">20:00 - 20:30</div>
                            <div class="time-slot">20:30 - 21:00</div>
                        </div>
                        <!-- Bảng chú thích -->
                        <div class="legend">

                            <div class="legend-item">
                                <div class="legend-color" style="background-color:rgb(71, 70, 70);"></div>
                                <span> Thời gian đã qua </span>
                                <div class="legend-color" style="background-color: rgb(16, 36, 77);"></div>
                                <span> Đã đặt </span>
                                <div class="legend-color" style="background-color: rgb(29, 94, 158);"></div>
                                <span> Chọn </span>
                                <div class="legend-color" style="background-color: rgb(224, 233, 245);"></div>
                                <span> Trống </span>

                            </div>

                        </div>

                    </div>
                </div>

            </form>
        </div>

        <div class="col-md-3 info-section">
            <h4 class="mb-4">Thông tin đặt sân</h4>
            <ul id="booking-summary" class="list-group">
                <li class="list-group-item">
                    <b>Sân 02 - 12/02/2025.</b>
                    <br>Thời gian: 7:00-7:30
                    <br>Thời lượng: 30p
                    <br>Thành tiền: 60.000 đ
                    <button class="btn btn-link text-danger" onclick="removeItem(this)">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </li>
                <li class="list-group-item">
                    <b>Sân 03 - 12/02/2025.</b>
                    <br>Thời gian: 7:00-7:30
                    <br>Thời lượng: 30p
                    <br>Thành tiền: 60.000 đ
                    <button class="btn btn-link text-danger" onclick="removeItem(this)">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </li>
            </ul>
            <div class="mt-3">
                <h5 class="mb-3">Tổng tiền: <span id="total-price">120.000 đ</span></h5>
                <button type="submit" class="btn btn-primary " id="confirm-button">Xác nhận đặt sân</button>
            </div>
        </div>

    </div>


</div>

<script>
    document.getElementById('confirm-button').onclick = function () {
        window.location.href = 'index.php?orderconfirm'; // Chuyển đến trang xác nhận
    };
    function removeItem(button) {
        const listItem = button.closest('.list-group-item');
        listItem.remove();
        // Cập nhật tổng tiền nếu cần
    }

    function selectCourt(button) {
        const dateInput = document.getElementById('date');

        // Kiểm tra xem ngày đã được chọn chưa
        if (!dateInput.value) {
            alert("Vui lòng chọn ngày trước khi chọn sân.");
            return;
        }

        // Bỏ chọn nút sân trước đó
        const allButtons = document.querySelectorAll('.field-button');
        allButtons.forEach(btn => btn.classList.remove('selected'));

        // Đánh dấu nút sân đang được chọn
        button.classList.add('selected');
    }
</script>

<!-- Footer Start -->
<?php include("../layout/footer.php") ?>