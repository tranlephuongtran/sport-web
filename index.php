<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
if (isset($_GET['register'])) {
    $page = 'register';
    include("page/" . $page . "/index.php");
    exit();
} else if (isset($_GET['login'])) {
    $page = 'login';
    include("page/" . $page . "/index.php");
    exit();
}
include("layout/header.php");
if (isset($_GET['rule'])) {
    $page = 'rule';
} else if (isset($_GET['profile'])) {
    $page = 'profile';
} else if (isset($_GET['order'])) {
    $page = 'order';
} else if (isset($_GET['orderconfirm'])) {
    $page = 'orderconfirm';
} else if (isset($_GET['payment'])) {
    $page = 'payment';
} else if (isset($_GET['about'])) {
    $page = 'about';
} else if (isset($_GET['history'])) {
    $page = 'history';
} else {
    $page = 'home';
}
include("page/" . $page . "/index.php");
include("layout/footer.php");
?>