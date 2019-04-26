<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-03-26
 * Time: 오전 9:50
 */
session_start();
define('MB', 1048576);
include_once __DIR__ . '/../DB/DBconnect.php';

//세션이 없다면 인덱스(로그인 창)으로 이돌
if (!isset($_SESSION['class']))
    header('location: ../index.php');


//빈칸시 못 들어오게함
else if (!isset($_POST['title']) || $_POST['title'] == '' || $_POST['text'] == '' || $_POST['sub_title'] == '') {
    echo '<script>alert("빈칸을 입력해주세요"); history.back();</script>';
} //그 외
else {
    //변수 선언
    $art_num = mysqli_fetch_array(mysqli_query($conn, 'select max(art_num)+1 art_num from article'))['art_num'];
    $url = mysqli_real_escape_string($conn, 'http://localhost/intern/china_focus/admin/view/article_view.php?art_num=' . $art_num);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $sub_title = mysqli_real_escape_string($conn, $_POST['sub_title']);
    $text = mysqli_real_escape_string($conn, $_POST['text']);
    $view_title = mysqli_real_escape_string($conn, $_POST['view_title']);
    $import = (int)mysqli_real_escape_string($conn, $_POST['import']);
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $copyright = mysqli_real_escape_string($conn, '봉황망코리아');

    //이미지 원자성을 위한 변수
    $img_success = 1;

    //세션 정보를 통해 이름 및 이메일 입력
    $w_name = mysqli_real_escape_string($conn, $_SESSION['name']);
    $w_email = mysqli_real_escape_string($conn, $_SESSION['email']);

    if (isset($_SESSION['img_name'])) {
        //-------------------------------------------------------------- 이미지 이름 바꾸기
        $img_abs_route = ($_SESSION['img_route']);
        $img_old_name = unserialize($_SESSION['img_name']);
        $img_url = array('');

        $img_count = count($img_old_name);

        for ($i = 0; $i < $img_count; $i++) {
            //여기서 이름을 새로운 이름으로 바꾸는 작업해야되요
            $real_img_name[$i] = substr($img_old_name[$i], strpos($img_old_name[$i], '_')); //'none_'을 '기사번호_'로 바꾸기 위해
            $img_new_name[$i] = $art_num . $real_img_name[$i];

            $img_rel_route = substr($img_abs_route, strpos($img_abs_route, '/img/')); //절대경로에서 /img/ 전까지 자르기기
            if (is_file('.' . $img_rel_route . '/' . $img_old_name[$i])) {
                rename('.' . $img_rel_route . '/' . $img_old_name[$i], '.' . $img_rel_route . '/' . $img_new_name[$i]);
                $img_url[$i] = $img_abs_route.'/'.$img_new_name[$i];
            }
            $text = str_replace($img_abs_route.'/'.$img_old_name[$i], $img_abs_route.'/'.$img_new_name[$i], $text);
        }
        //-------------------------------------------------------------- 이미지 이름 바꾸기
    }
    $insert_article = 'insert into article(art_num,code,title,
                                                sub_title,view_title,text,
                                                url,copyright,import,
                                                w_name,w_email)
                           values(' . $art_num . ',' . $code . ',\'' . $title . '\',
                           \'' . $sub_title . '\',\'' . $view_title . '\',\'' . $text . '\',
                           \'' . $url . '\',\'' . $copyright . '\',' . $import . ',
                           \'' . $w_name . '\',\'' . $w_email . '\')';
    mysqli_query($conn, $insert_article);

    if (isset($_SESSION['img_route'])) {
        for ($i = 0; $i < $img_count; $i++) {
            $insert_img = 'insert into img(img_url,art_num,img_order) values(\'' . $img_url[$i] . '\',' . $art_num . ','.$i.')';
            mysqli_query($conn, $insert_img);
        }
    }
    unset($_SESSION['img_name']);
    unset($_SESSION['img_route']);
    echo '<script>alert("기사가 작성되었습니다."); location.href="../view/article_view.php?art_num=' . $art_num . '"</script>';
}

?>