<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-04-08
 * Time: 오후 2:07
 */

include_once 'DBconnect.php';
session_start();
//세션이 없다면 로그인 창으로 이동
if (!isset($_SESSION['class'])) header('location: ../index.php');

//세션 class가 관리자일 때 승인 과정
else if ($_SESSION['class']=='관리자') {

    //기사 넘버와 중요도를 갖고 있을 경우
    if (isset($_POST['art_num']) && isset($_POST['import'])) {
        $import = (int)mysqli_real_escape_string($conn, $_POST['import']);
        $art_num = mysqli_real_escape_string($conn, $_POST['art_num']);
        $admission = mysqli_query($conn,
            'update article set ok=1, import=' . $import . ' ,novel=0,code=710004
                    where art_num=' . $art_num);
        echo '<script>alert("소설연재로 취소되었습니다."); location.href="../view/main.php"</script>';
    }
    else
        echo '<script>alert("잘못된 경로입니다."); history.back();</script>';
}

//관리자가 아닌 경우
else
    echo '<script>alert("관리자만 가능한 경로입니다."); history.back();</script>';
?>