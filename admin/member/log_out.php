<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-03-25
 * Time: 오전 10:24
 */
session_start();
session_destroy();
echo '<script>alert("로그아웃 합니다."); location.href="../index.php"</script>';
//header('location:../index.php');
?>