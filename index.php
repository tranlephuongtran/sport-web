<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
if (isset($_GET['register'])) {
    $page = 'register';
} else if (isset($_GET['login'])) {
    $page = 'login';
} else if (isset($_GET['rule'])) {
    $page = 'rule';
} else if (isset($_GET['profile'])) {
    $page = 'profile';
} else if (isset($_GET['history'])) {
    $page = 'history';
} else {
    $page = 'home';
}
include("layout/header.php");
include("page/" . $page . "/index.php");
include("layout/footer.php");
?>