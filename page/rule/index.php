<?php include("../layout/header.php");
$conn = mysqli_connect("localhost", "nhomcnm", "nhomcnm", "sport");
$str = "SELECT * FROM chinhsach";
$result = $conn->query($str);
?>

<style>
    .policy-content {
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
        display: none;
        color: black;
        font-size: larger;
        font-family: Verdana, Geneva, Tahoma, sans-serif
    }

    .policy-content h4 {
        color: #007bff;
    }
</style>

<script>
    function showPolicy(policyId, element) {
        // Ẩn tất cả nội dung chính sách
        document.querySelectorAll('.policy-content').forEach(item => item.style.display = 'none');

        // Hiển thị nội dung được chọn
        document.getElementById(policyId).style.display = 'block';

        // Đổi trạng thái active cho nút
        document.querySelectorAll('.nav-link').forEach(item => item.classList.remove('active'));
        element.classList.add('active');
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Hiển thị chính sách đầu tiên khi tải trang
        let firstPolicy = document.querySelector('.policy-content');
        if (firstPolicy) {
            firstPolicy.style.display = 'block';
        }
    });
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
        <?php
        $first = true; // Biến kiểm tra để đặt active cho phần đầu tiên
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $maChinhSach = $row['maChinhSach'];
                $ten = $row['ten'];
                $activeClass = $first ? "active" : ""; // Nút đầu tiên sẽ có class active
                echo "<li class='nav-item'>
                    <button class='nav-link fs-4 px-4 py-2 $activeClass' onclick=\"showPolicy('$maChinhSach', this)\">$ten</button>
                </li>";
                $first = false; // Sau lần đầu tiên, không đặt active nữa
            }
        }
        ?>
    </ul>

    <!-- Nội dung chính sách -->
    <div class="card p-4">
        <?php
        $result = $conn->query($str); // Chạy lại query để lấy dữ liệu lần nữa
        $first = true; // Đặt biến để hiển thị chính sách đầu tiên
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $maChinhSach = $row['maChinhSach'];
                $noiDung = $row['noiDung'];
                $displayStyle = $first ? "style='display: block;'" : "style='display: none;'";
                echo "<div id='$maChinhSach' class='policy-content' $displayStyle>
                    <h4>{$row['ten']}</h4>
                    <p> <pre> $noiDung <pre></p>
                </div>";
                $first = false;
            }
        }
        ?>
    </div>
</div>

<?php include("../layout/footer.php"); ?>