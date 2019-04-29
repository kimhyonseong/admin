<?php
/**
 * Created by PhpStorm.
 * User: mk
 * Date: 2019-03-28
 * Time: 오후 1:04
 */

session_start();
define('MB', 1048576);
include_once __DIR__ . '/../DB/DBconnect.php';
if (!isset($_SESSION['class']))
    header('location: ../index.php');

//빈칸시 못 들어오게함
if (!isset($_POST['title']) || $_POST['title'] == '' || $_POST['text'] == '' || $_POST['sub_title'] == '') {
    echo '<script>alert("빈칸을 입력해주세요"); history.back();</script>';
} //기사 번호가 없다면 잘못된 접근
elseif (!isset($_POST['art_num']))
    echo '<script>alert("잘못된 접근입니다."); history.back();</script>';

//그 외 정상
else {
    //변수 선언
    $art_num = (int)mysqli_real_escape_string($conn, $_POST['art_num']);
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $sub_title = mysqli_real_escape_string($conn, $_POST['sub_title']);
    $text = $_POST['text'];
    $view_title = mysqli_real_escape_string($conn, $_POST['view_title']);
    $import = (int)mysqli_real_escape_string($conn, $_POST['import']);


    //이미지 검사 수행의 변수
    $img_success = 1;

    $time_query = mysqli_query($conn, 'select date_format(now(),"%Y") Y,
                                              date_format(now(),"%m") m,
                                              date_format(now(),"%d") d,
                                              date_format(now(),"%H") H,
                                              date_format(now(),"%i") i,
                                              date_format(now(),"%S") S
                                             ');
    $time = mysqli_fetch_array($time_query);
    if (!is_dir(__DIR__ . '/img/' . $time['Y'] . '/' . $time['m'] . '/' . $time['d']))
        mkdir(__DIR__ . '/img/' . $time['Y'] . '/' . $time['m'] . '/' . $time['d'], 0777, true);
    $dir = 'img/' . $time['Y'] . '/' . $time['m'] . '/' . $time['d'];

    $delete_img_file_query = mysqli_query($conn, 'select img_url from img where art_num=' . $art_num);

    while ($delete_img_file = mysqli_fetch_array($delete_img_file_query)) {
        if (strpos($text, $delete_img_file['img_url']) != null) //이미지 url이 있다면 옮기기
        {
            $file = substr($delete_img_file['img_url'], strpos($delete_img_file['img_url'], '/img/'));
            $file_name = substr($delete_img_file['img_url'], strpos($delete_img_file['img_url'], $art_num . '_'));
            rename('.' . $file, './' . $dir . '/' . $file_name);
            continue;
        }
        $img_name = substr($delete_img_file['img_url'], strpos($delete_img_file['img_url'], '/img/'));
        unlink('.' . $img_name);
    }


    if (isset($_SESSION['img_name'])) {
        //-------------------------------------------------------------- 이미지 이름 바꾸기

        $img_abs_route = 'http://localhost/intern/china_focus/admin/DB/' . $dir;
        $img_old_name = unserialize($_SESSION['img_name']);
        $img_url = array('');

        $img_count = count($img_old_name);

        for ($i = 0; $i < $img_count; $i++) {
            $img_rel_route = substr($img_abs_route, strpos($img_abs_route, '/img/')); //절대경로에서 /img/부터 자르기


            $real_img_name[$i] = substr($img_old_name[$i], strpos($img_old_name[$i], '_'));
            $img_new_name[$i] = $art_num . $real_img_name[$i];

            // ./img/월/일/none_file
            if (is_file('.' . $img_rel_route . '/' . $img_old_name[$i]))
                rename('.' . $img_rel_route . '/' . $img_old_name[$i], '.' . $img_rel_route . '/' . $img_new_name[$i]);

            $img_url[$i] = $img_abs_route . '/' . $img_new_name[$i];

            echo $img_url[$i] . '<br>';
            //echo $img_url[$i].'<br>';
            while ($delete_img_file = mysqli_fetch_array($delete_img_file_query))
                $text = str_replace($delete_img_file['img_url'], $img_abs_route . '/' . $img_new_name[$i], $text);

            //이미지 파일 이름 바꾸기 none_파일 -> 기사번호_파일
            $text = str_replace($img_abs_route . '/' . $img_old_name[$i], $img_abs_route . '/' . $img_new_name[$i], $text);
        }
        //-------------------------------------------------------------- 이미지 이름 바꾸기
    }
    mysqli_query($conn,'delete from img where art_num='.$art_num);

    $img_order = 0;
    for ($i = 0; $i < $img_count; $i++) {
        if (strpos($text, $img_url[$i]) != null) {
            $insert_img =
                'insert into img(img_url,art_num,img_order) 
                         values(\'' . mysqli_real_escape_string($conn, $img_url[$i]) . '\',' . $art_num . ',' . $img_order . ')';
            mysqli_query($conn, $insert_img);
            echo $insert_img;
            $img_order++;
        }
    }

    $update_article = 'update article set code=' . $code . ',title=\'' . $title . '\',sub_title=\'' . $sub_title . '\',
                                   view_title=\'' . $view_title . '\',text=\'' . mysqli_real_escape_string($conn,$text) . '\',import=' . $import . ',
                                   ok=0, modi_date=current_timestamp()
                            where art_num=' . $art_num;
    mysqli_query($conn,$update_article);

    //location.href="../view/article_view.php?art_num='.$art_num.'";
    if ($img_success == 1) {
        unset($_SESSION['img_name']);
        unset($_SESSION['img_route']);
        echo '<script>alert("수정 완료"); location.href="../view/article_view.php?art_num='.$art_num.'";</script>';
        echo $text;
    } else
        echo '';
}
?>