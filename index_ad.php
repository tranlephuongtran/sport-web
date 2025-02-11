<?php
session_start();
error_reporting(E_ERROR | E_PARSE);

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
include("layout/sidebar.php");
if (isset($_GET['order_ad'])) {
    $page_ad = 'order_ad';
} elseif (isset($_GET['dashboard'])) {
    $page_ad = 'dashboard';
} elseif (isset($_GET['logout'])) {
    $page_ad = 'logout';
} else {
    $page_ad = 'order_ad';
}


include("page_ad/" . $page_ad . "/index.php");
?>