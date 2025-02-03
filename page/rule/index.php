<?php include("../layout/header.php"); ?>
<style>
    .policy-content {
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .policy-content h4 {
        color: #007bff;
    }
</style>
<script>
    function showPolicy(policy, element) {
        document.querySelectorAll('.policy-content').forEach(item => item.style.display = 'none');
        document.getElementById(policy).style.display = 'block';

        document.querySelectorAll('.nav-link').forEach(item => item.classList.remove('active'));
        element.classList.add('active');
    }
</script>

<!-- Header Start -->
<div class="container-fluid bg-breadcrumb">
    <div class="container text-center py-5" style="max-width: 900px;">
        <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Chính sách & điều khoản</h4>
    </div>
</div>
<!-- Header End -->

<div class="container-fluid mt-5">
    <!-- Thanh điều hướng -->
    <ul class="nav nav-pills my-3 w-100 d-flex justify-content-center bg-light py-3 rounded">
        <li class="nav-item">
            <button class="nav-link active fs-4 px-4 py-2" onclick="showPolicy('privacy', this)">Bảo vệ cá nhân</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fs-4 px-4 py-2" onclick="showPolicy('refund', this)">Hoàn tiền</button>
        </li>
        <li class="nav-item">
            <button class="nav-link fs-4 px-4 py-2" onclick="showPolicy('terms', this)">Điều khoản sử dụng</button>
        </li>
    </ul>

    <!-- Nội dung chính sách -->
    <div class="card p-4">
        <div id="privacy" class="policy-content">
            <h4>Chính sách bảo vệ cá nhân</h4>
            <p>Chúng tôi cam kết bảo vệ thông tin cá nhân của bạn...</p>
        </div>
        <div id="refund" class="policy-content" style="display: none;">
            <h4>Chính sách hoàn tiền</h4>
            <p>Bạn có thể yêu cầu hoàn tiền trong vòng 7 ngày nếu...</p>
        </div>
        <div id="terms" class="policy-content" style="display: none;">
            <h4>Điều khoản sử dụng</h4>
            <p>Khi sử dụng dịch vụ của chúng tôi, bạn đồng ý với các điều khoản...</p>
        </div>
        <div id="shipping" class="policy-content" style="display: none;">
            <h4>Chính sách giao hàng</h4>
            <p>Chúng tôi cam kết giao hàng trong vòng 3-5 ngày làm việc...</p>
        </div>
    </div>
</div>

<?php include("../layout/footer.php"); ?>