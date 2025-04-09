<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="layout/img/img_ad/apple-icon.png">
    <link rel="icon" type="image/png" href="layout/img/logo.png">
    <title>
        Material Dashboard 2 by Creative Tim
    </title>
    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <!-- Nucleo Icons -->
    <link href="layout/css/nucleo-icons.css" rel="stylesheet" />
    <link href="layout/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="layout/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />
</head>



<body class="g-sidenav-show  bg-gray-200">
    <aside
        class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark"
        id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-5" target="_blank">
                <img src="layout/img/logo.png"
                    style="width: 130px;height: 100px;margin-top: 25px;margin-bottom: 30px;border-radius: 10px;"
                    alt="main_logo">
            </a>
        </div>
        <hr class="horizontal light mt-0 mb-2">
        <div class="collapse navbar-collapse  w-auto  max-height-vh-100" id="sidenav-collapse-main">
            <ul class="navbar-nav">

                <li class="nav-item">
                    <a class="nav-link text-white report-toggle" href="#">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">bar_chart</i>
                        </div>
                        <b class="nav-link-text ms-1">Báo cáo</b>
                    </a>
                    <div class="collapse" id="reportSubmenu" data-bs-parent="#sidenav-collapse-main">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link text-white" href="index_ad.php?dashboard">Báo cáo Doanh thu</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="index_ad.php?report_orders">Báo cáo Đơn đặt</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="index_ad.php?report_endday">Báo cáo Cuối ngày</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="index_ad.php?order_ad">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">receipt_long</i>
                        </div>
                        <b class="nav-link-text ms-1">Hóa đơn</b>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="index_ad.php?rule">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">policy</i>
                        </div>
                        <b class="nav-link-text ms-1">Chính sách</b>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="index_ad.php?discount">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">local_offer</i>
                        </div>
                        <b class="nav-link-text ms-1">Khuyến mãi</b>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="index_ad.php?staff">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">people</i>
                        </div>
                        <b class="nav-link-text ms-1">Nhân viên</b>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-white" href="index_ad.php?role">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">people</i>
                        </div>
                        <b class="nav-link-text ms-1">Vai trò</b>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="index_ad.php?customer">
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10">people</i>
                        </div>
                        <b class="nav-link-text ms-1">Khách hàng</b>
                    </a>
                </li>

                <li class="nav-item">
                    <?php if (isset($_SESSION['login_ad'])): ?>
                        <a class="nav-link text-white" href="index_ad.php?logout">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">logout</i>
                            </div>
                            <b class="nav-link-text ms-1">Sign Out</b>
                        </a>
                    <?php else: ?>
                        <a class="nav-link text-white" href="index_ad.php?login">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">login</i>
                            </div>
                            <b class="nav-link-text ms-1">Sign In</b>
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </aside>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

        <style>
            .nav-link.active {
                background-color: #015FC9;
            }
        </style>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                let links = document.querySelectorAll(".nav-link");
                let currentURL = window.location.href;
                links.forEach(link => {
                    if (currentURL.includes(link.getAttribute("href"))) {
                        link.classList.add("active");
                    }
                });
                let reportSubLinks = document.querySelectorAll("#reportSubmenu .nav-link");
                let reportMenu = document.getElementById("reportSubmenu");
                let reportToggle = document.querySelector(".report-toggle");

                reportSubLinks.forEach(subLink => {
                    if (currentURL.includes(subLink.getAttribute("href"))) {
                        reportMenu.classList.add("show");
                        reportToggle.classList.add("active");
                    }
                });
                reportToggle.addEventListener("click", function (event) {
                    event.preventDefault();
                    reportMenu.classList.toggle("show");
                });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>