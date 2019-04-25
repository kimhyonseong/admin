<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-04-02
 * Time: 오후 2:06
 */
    session_start();
    include_once __DIR__ . '/../DB/DBconnect.php';

    //세션 없으면 로그인 창으로 이동
    if (!isset($_SESSION['class'])) header('location: ../index.php');

    //관리자 또는 해당 기사 작성자일 때
    elseif ($_SESSION['class']=='관리자' || $_SESSION['name']==$_POST['name']) {
        if (isset($_POST['art_num']))
            $art_num = (int)mysqli_real_escape_string($conn, $_POST['art_num']);
        else {
            echo '<script>alert("잘못된 경로입니다."); history.back();</script>';
            exit;
        }

        //이미지 파일 삭제 이미지 DB삭제 전에 실행해야함
        $delete_img_file = mysqli_query($conn,'select img_url from img where art_num='.$art_num);
        while ($delete_img_file = mysqli_fetch_array($delete_img_file))
        {
            $img_name=substr($delete_img_file['img_url'],strpos($delete_img_file['img_url'],'/img/'));
            unlink('.'.$img_name);
        }
        //관련기사, 이미지, 기사 순으로 삭제(외래키 때문에 기사는 마지막에 동작해야함)
        $delete_img = mysqli_query($conn, 'delete from img where art_num=' . $art_num);
        $delete_rel = mysqli_query($conn, 'delete from rel_art where art_num=' . $art_num);
        $delete = mysqli_query($conn, 'delete from article where art_num=' . $art_num);

        //쿼리 실패 및 성공 시
        if ($delete === false) {
            echo '<script>alert("해당 기사는 없습니다."); history.back();</script>';
            exit;
        } else {
            echo '<script>alert("정상적으로 삭제되었습니다."); location.href="../view/main.php";</script>';
        }
    }
    else
       echo '<script>alert("권한이 없습니다."); location.href="../view/main.php";</script>';
?>