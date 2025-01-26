<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
if (isset($_GET['register'])) {
    $page = 'register';
    include("page/" . $page . "/index.php");
    exit();
} else if (isset($_GET['login'])) {
    $page = 'login';
    include("page/" . $page . "/index.php"); // include vào đang ở cấp Page ngoài cùng
    exit();
}
include("layout/header.php");
include("page/home/index.php");
include("layout/footer.php");
?>