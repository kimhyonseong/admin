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
    }

    //그 외
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

        //세이브 포인트 만들기 및 기사 정보 삽입
        //mysqli_query($conn, 'set autocommit=0');
        //mysqli_query($conn,'savepoint A');



        //파일 갯수 알아내기
        $file_count = count($_FILES['file']['name']);


        //파일 올렸을 시 검사 및 삽입
        if (isset($_FILES['file']) && $_FILES['file']['name'] != "") {

            //파일 갯수만큼 반복 검사, 파일이름이 없으면 패스
            for ($i = 0; $i < $file_count; $i++) {
                if (isset($_FILES['file']['name'][$i]) && $_FILES['file']['name'][$i] != "") {
                    //저장될 폴더 , 파일 이름
                    $dir = './img/';
                    $file_name= $art_num.'_'.$_FILES['file']['name'][$i];


                    //확장자 제한
                    $ext_array = explode(".","jpg.jpeg.png.gif");
                    $file_ext = pathinfo($file_name);


                    //확장자 문제,용량문제 시 들어간 데이터 롤백, img_success를 0으로 만들어서 이미지 업로드 막기
                    if (!in_array($file_ext['extension'],$ext_array)) {
                        $img_success = 0;
                        mysqli_query($conn,'rollback to savepoint A');
                        echo '<script>alert("확장자가 부적절합니다."); history.back();</script>';
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
                        }
                    }
                }
            }
            //이미지 검사 끝

            $insert_article = 'insert into article(art_num,code,title,
                                                sub_title,view_title,text,
                                                url,copyright,import,
                                                w_name,w_email)
                           values(' . $art_num . ',' . $code . ',\'' . $title . '\',
                           \'' . $sub_title . '\',\'' . $view_title . '\',\'' . $text . '\',
                           \'' . $url . '\',\'' . $copyright . '\',' . $import . ',
                           \'' . $w_name . '\',\'' . $w_email . '\')';
            mysqli_query($conn, $insert_article);

            //-------------------------------------------------------------- 이미지 이름 바꾸기
            $img_abs_route = ($_SESSION['img_route']);
            $img_old_name = unserialize($_SESSION['img_name']);

            //var_dump($_SESSION['img']);
            //var_dump($img_array);
            $img_count = count($img_old_name);
            for ($i=0; $i<$img_count; $i++)
            {
                echo $img_abs_route . ' 의 ' . $img_old_name[$i] .'<br>';
            }
            for ($i=0; $i<$img_count; $i++)
            {
                //여기서 이름을 새로운 이름으로 바꾸는 작업해야되요
                //echo strpos($img_old_name[$i],'_').'<br>';
                echo substr($img_old_name[$i],strpos($img_old_name[$i],'_')).'<br>';

                $real_img_name[$i] = substr($img_old_name[$i],strpos($img_old_name[$i],'_'));
                $img_new_name[$i] ='1111'.$real_img_name[$i];

                echo $img_new_name[$i].'<br>';
                echo $img_abs_route.'/'.$img_new_name[$i].'<br><br>';

                $img_rel_route = substr($img_abs_route,strpos($img_abs_route,'/img/'));
                if (is_file('.'.$img_rel_route.'/'.$img_old_name[$i]))
                    rename('.'.$img_rel_route.'/'.$img_old_name[$i],'.'.$img_rel_route.'/'.$img_new_name[$i]);
            }
            //-------------------------------------------------------------- 이미지 이름 바꾸기


            //이미지 검사가 성공적으로 되었을 시 이미지 삽입
            if (isset($_FILES['file']) && $_FILES['file']['name'] != "" && $img_success == 1) {

                //파일 갯수만큼 반복하며 파일이름이 없으면 패스
                for ($i = 0; $i < $file_count; $i++) {
                    if (isset($_FILES['file']['name'][$i]) && $_FILES['file']['name'][$i] != "") {

                        //$_FILES['변수']['tmp_name'][배열] tmp_name이 되어야 저장됨
                        move_uploaded_file($_FILES['file']['tmp_name'][$i], './img/' . $art_num.'_'.$_FILES['file']['name'][$i]);

                        //이미지 DB 삽입 절대경로로 저장
                        $img_url = mysqli_real_escape_string($conn, 'http://localhost/intern/china_focus/admin/DB/img/' . $art_num.'_'.$_FILES['file']['name'][$i]);
                        if (isset($_POST['description'.$i]))
                            $img_desc = mysqli_real_escape_string($conn,$_POST['description'.$i]);
                        else $img_desc ='';

                        $insert_img = 'insert into img(img_url,description,img_order,art_num) values(\''.$img_url.'\',\''.$img_desc.'\','.$i.','.$art_num.')';
                        mysqli_query($conn,$insert_img);
                    }
                }   //반복 끝
            }   //삽입 완료
        }
        //완료되었으면 정상 커밋, 실패 시 롤백되어 세이브포인트 a에서 커밋, 오토커밋 활성화
        //mysqli_query($conn, 'commit');
        //mysqli_query($conn, 'set autocommit=1');
        echo '<script>alert("기사가 작성되었습니다."); location.href="../view/article_view.php?art_num='.$art_num.'"</script>';
    }
?>