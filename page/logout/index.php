<?php


// Hủy toàn bộ session
session_unset();
session_destroy();

// Chuyển hướng về trang đăng nhập
echo "<script>
    alert('Đăng xuất thành công!');
    window.location.href = 'index.php?home';
</script>";
exit();
?>