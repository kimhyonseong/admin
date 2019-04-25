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
    }

    //기사 번호가 없다면 잘못된 접근
    elseif (!isset($_POST['art_num']))
        echo '<script>alert("잘못된 접근입니다."); history.back();</script>';

    //그 외 정상
    else {
        //변수 선언
        $art_num = (int)mysqli_real_escape_string($conn,$_POST['art_num']);
        $code = mysqli_real_escape_string($conn, $_POST['code']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $sub_title = mysqli_real_escape_string($conn, $_POST['sub_title']);
        $text = mysqli_real_escape_string($conn, $_POST['text']);
        $view_title = mysqli_real_escape_string($conn, $_POST['view_title']);
        $import = (int)mysqli_real_escape_string($conn, $_POST['import']);

        //이미지 검사 수행의 변수
        $img_success = 1;

        //세이브 포인트 만들기
        mysqli_query($conn, 'set autocommit=0');
        mysqli_query($conn, 'savepoint A');
//        $update_article = 'update article set code=' . $code . ',title=\''.$title.'\',sub_title=\''.$sub_title.'\',
//                                   view_title=\''.$view_title.'\',text=\''.$text.'\',import='.$import.',
//                                   ok=0, modi_date=current_timestamp()
//                           where art_num='. $art_num;
//        mysqli_query($conn, $update_article);


        //파일 올렸을 시 검사 및 삽입
        if (isset($_FILES['file']) && $_FILES['file']['name'] != "") {
            $file_count = count($_FILES['file']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if (isset($_FILES['file']['name'][$i]) && $_FILES['file']['name'][$i] != "") {

                    //확장자 제한
                    $ext_array = explode(".","jpg.jpeg.png.gif");
                    $file_ext = pathinfo($_FILES['file']['name'][$i]);

                    //확장자 문제,용량 문제 시 들어간 데이터 롤백, img_success를 0으로 만들어서 이미지 업로드 막기
                    if (!in_array($file_ext['extension'],$ext_array)) {
                        $img_success = 0;
                        //mysqli_query($conn,'rollback to savepoint A');
                        echo '<script>alert("확장자가 부적절합니다."); history.back();</script>';
                        exit;
                    }
                    else if ($_FILES['file']['error'][$i]!=UPLOAD_ERR_OK) {
                        $img_success = 0;
                        //mysqli_query($conn,'rollback to savepoint A');
                        switch( $_FILES['file']['error'][$i] ) {
                            case UPLOAD_ERR_INI_SIZE:
                            case UPLOAD_ERR_FORM_SIZE:
                                echo '<script>alert("파일용량은 2MB까지 입니다."); history.back();</script>';
                                exit;
                                break;
                            case UPLOAD_ERR_NO_FILE:
                                break;
                            default:
                                echo '<script>alert("파일이 정상적으로 첨부되지 않았습니다."); history.back();</script>';
                                exit;
                                break;
                        }
                    }
                }
            }//파일 검사 끝

            //파일 검사가 성공적으로 되었을 시 이미지 삽입
            if (isset($_FILES['file']) && $_FILES['file']['name'] != "" && $img_success == 1) {
                $update_article = 'update article set code=' . $code . ',title=\''.$title.'\',sub_title=\''.$sub_title.'\',
                                   view_title=\''.$view_title.'\',text=\''.$text.'\',import='.$import.',
                                   ok=0, modi_date=current_timestamp()
                                   where art_num='. $art_num;
                mysqli_query($conn, $update_article);
                $delete_img_file = mysqli_query($conn,'select img_url from img where art_num='.$art_num);
                while ($delete_img_file = mysqli_fetch_array($delete_img_file))
                {
                    $img_name=substr($delete_img_file['img_url'],strpos($delete_img_file['img_url'],'/img/'));
                    unlink('.'.$img_name);
                }
                mysqli_query($conn,'delete from img where art_num='.$art_num);

                for ($i = 0; $i < $file_count; $i++) {
                    if (isset($_FILES['file']['name'][$i]) && $_FILES['file']['name'][$i] != "") {

                        //$_FILES['변수']['tmp_name'][배열] tmp_name이 되어야 저장됨
                        move_uploaded_file($_FILES['file']['tmp_name'][$i], './img/' . $art_num.'_'.$_FILES['file']['name'][$i]);

                        //이미지 DB 삽입
                        $img_url = mysqli_real_escape_string($conn, 'http://localhost/intern/china_focus/admin/DB/img/' . $art_num.'_'.$_FILES['file']['name'][$i]);
                        if (isset($_POST['description'.$i]))
                            $img_desc = mysqli_real_escape_string($conn,$_POST['description'.$i]);
                        else $img_desc ='';
                        $insert_img = 'insert into img(img_url,description,img_order,art_num)
                                       values(\''.$img_url.'\',\''.$img_desc.'\','.$i.','.$art_num.')';
                        mysqli_query($conn,$insert_img);
                    }
                }
            } //삽입 및 파일 삭제 완료
        }
        else //파일 없을 시
        {
            $update_article = 'update article set code=' . $code . ',title=\''.$title.'\',sub_title=\''.$sub_title.'\',
                                   view_title=\''.$view_title.'\',text=\''.$text.'\',import='.$import.',
                                   ok=0, modi_date=current_timestamp()
                               where art_num='. $art_num;
            mysqli_query($conn, $update_article);
            $delete_img_file = mysqli_query($conn,'select img_url from img where art_num='.$art_num);
            while ($delete_img_file = mysqli_fetch_array($delete_img_file))
            {
                $img_name=substr($delete_img_file['img_url'],strpos($delete_img_file['img_url'],'/img/'));
                unlink('.'.$img_name);
            }
            mysqli_query($conn,'delete from img where art_num='.$art_num);
        }

        //완료되었으면 정상 커밋, 실패 시 롤백되어 세이브포인트 a에서 커밋, 오토커밋 활성화
        mysqli_query($conn, 'commit');
        mysqli_query($conn, 'set autocommit=1');

        if ($img_success==1)
            echo '<script>alert("수정이 완료되었습니다."); location.href="../view/article_view.php?art_num='.$art_num.'";</script>';
        else
            echo '';
    }
?>