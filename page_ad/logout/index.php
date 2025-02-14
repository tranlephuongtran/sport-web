<?php

session_unset();
session_destroy();

echo "<script>
    alert('Đăng xuất thành công!');
    window.location.href = 'index_ad.php?login';
</script>";
exit();
?>