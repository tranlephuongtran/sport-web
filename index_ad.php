<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
ob_start();
$isLoggedIn = isset($_SESSION['login_ad']);
if (!$isLoggedIn) {
    if (!isset($_GET['login'])) {
        header("Location: index_ad.php?login");
        exit();
    }
}

if (isset($_GET['login'])) {
    $page_ad = 'login';
    include("page_ad/" . $page_ad . "/index.php");
    exit();
}
include("class/classdatabase.php");
include("layout/sidebar.php");
if (isset($_GET['order_ad'])) {
    $page_ad = 'order_ad';
} else if (isset($_GET['dashboard'])) {
    $page_ad = 'dashboard';
} else if (isset($_GET['logout'])) {
    $page_ad = 'logout';
} else if (isset($_GET['rule'])) {
    $page_ad = 'rule';
} else if (isset($_GET['discount'])) {
    $page_ad = 'discount';
} else if (isset($_GET['order_detail'])) {
    $page_ad = 'order_detail';
} else if (isset($_GET['staff'])) {
    $page_ad = 'staff';
} else if (isset($_GET['role'])) {
    $page_ad = 'role';
} else {
    $page_ad = 'order_ad';
}
include("page_ad/" . $page_ad . "/index.php");
?>